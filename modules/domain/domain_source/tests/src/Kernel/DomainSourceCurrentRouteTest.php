<?php

declare(strict_types=1);

namespace Drupal\Tests\domain_source\Kernel;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Routing\RouteObjectInterface;
use Drupal\Core\Url;
use Drupal\domain_source\DomainSourceElementManagerInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\NodeType;
use Drupal\node\NodeInterface;
use Drupal\Tests\domain\Traits\DomainTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Tests URLs built from "<current>" on the pages of an entity.
 *
 * Core builds such URLs for #ajax elements, pagers and language switcher
 * links. They must be treated like a URL built from the route of the page.
 *
 * @group domain_source
 */
#[Group('domain_source')]
#[RunTestsInSeparateProcesses]
class DomainSourceCurrentRouteTest extends KernelTestBase {

  use DomainTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_source',
    'field',
    'node',
    'system',
    'user',
  ];

  /**
   * The node whose source domain is not the active domain.
   */
  protected NodeInterface $node;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('domain');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['domain', 'domain_source', 'system']);
    $this->installSchema('node', ['node_access']);
    $this->container->get('router.builder')->rebuild();

    $this->domainCreateTestDomains(2, 'example.com');

    NodeType::create(['type' => 'page', 'name' => 'Page'])->save();
    $this->container->get('domain_source.helper')->confirmFields('node', 'page');

    $this->node = $this->container->get('entity_type.manager')
      ->getStorage('node')
      ->create([
        'type' => 'page',
        'title' => 'Test node',
        DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => 'one_example_com',
      ]);
    $this->node->save();
  }

  /**
   * {@inheritdoc}
   */
  public function register(ContainerBuilder $container): void {
    parent::register($container);
    // Before Drupal 11.4, the processor matches the path to find its route,
    // with domain_source's own route provider. That provider extends
    // "router.route_provider", which a kernel test replaces by a provider
    // that takes no arguments. Extend the real one, kept under another name.
    $definition = $container->getDefinition('domain_source.route_provider');
    if ($definition instanceof ChildDefinition) {
      $definition->setParent('test.router.route_provider');
    }
  }

  /**
   * Tests a "<current>" URL on the canonical page of the node.
   *
   * The canonical route is not excluded, so the URL goes to the source domain
   * of the node, as a URL built from "entity.node.canonical" does.
   */
  public function testCurrentUrlOnCanonicalPageGoesToSourceDomain(): void {
    $this->viewNodePage('entity.node.canonical');

    $source_url = $this->getDomains()['one_example_com']->getPath();
    $this->assertStringStartsWith($source_url, $this->generateNodePageUrl('entity.node.canonical'));
    $this->assertStringStartsWith($source_url, $this->generateNodePageUrl('<current>'));
  }

  /**
   * Tests a "<current>" URL on the edit form of the node.
   *
   * The edit_form route is excluded, so the URL stays on the active domain.
   */
  public function testCurrentUrlOnExcludedEditFormStaysOnActiveDomain(): void {
    $this->config('domain_source.settings')
      ->set('exclude_routes', ['edit_form'])
      ->save();
    $this->viewNodePage('entity.node.edit_form');

    $this->assertStringStartsWith('/', $this->generateNodePageUrl('entity.node.edit_form'));
    $this->assertStringStartsWith('/', $this->generateNodePageUrl('<current>'));
  }

  /**
   * Makes a page of the node the current route, on the default domain.
   *
   * @param string $route_name
   *   The name of a route of the node, such as "entity.node.canonical".
   */
  protected function viewNodePage(string $route_name): void {
    $route = $this->container->get('router.route_provider')->getRouteByName($route_name);
    $path = str_replace('{node}', (string) $this->node->id(), $route->getPath());

    $request = Request::create('http://' . $this->getDomains()['example_com']->getHostname() . $path);
    $request->attributes->set(RouteObjectInterface::ROUTE_NAME, $route_name);
    $request->attributes->set(RouteObjectInterface::ROUTE_OBJECT, $route);
    $request->attributes->set('node', $this->node);
    $request->attributes->set('_raw_variables', new InputBag(['node' => (string) $this->node->id()]));
    $request->setSession(new Session(new MockArraySessionStorage()));

    $this->container->get('request_stack')->push($request);
    $this->container->get('current_route_match')->resetRouteMatch();
  }

  /**
   * Builds the URL of a route on the current page of the node.
   *
   * @param string $route_name
   *   The route name: "<current>", or the name of a route of the node.
   *
   * @return string
   *   The URL. It is absolute only when it goes to another domain.
   */
  protected function generateNodePageUrl(string $route_name): string {
    $parameters = $route_name === '<current>' ? [] : ['node' => $this->node->id()];
    return Url::fromRoute($route_name, $parameters)->toString();
  }

}
