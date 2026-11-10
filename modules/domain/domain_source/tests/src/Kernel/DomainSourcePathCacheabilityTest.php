<?php

declare(strict_types=1);

namespace Drupal\Tests\domain_source\Kernel;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Url;
use Drupal\domain_source\DomainSourceElementManagerInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\domain\Traits\DomainTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DependencyInjection\ChildDefinition;

/**
 * Tests the cacheability of URLs rewritten to a source domain.
 *
 * @group domain_source
 */
#[Group('domain_source')]
#[RunTestsInSeparateProcesses]
class DomainSourcePathCacheabilityTest extends KernelTestBase {

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
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('domain');
    $this->installEntitySchema('node');
    $this->installEntitySchema('user');
    $this->installConfig(['domain', 'domain_source', 'system']);
    $this->installSchema('node', ['node_access']);

    $this->domainCreateTestDomains(2, 'example.com');

    NodeType::create(['type' => 'page', 'name' => 'Page'])->save();
    $this->container->get('domain_source.helper')->confirmFields('node', 'page');
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
   * Tests the same URL generated twice in one request.
   *
   * The processor remembers the URLs it already processed. A render cache
   * entry holding the second URL must still be cleared when the node changes,
   * for example when its source domain changes.
   */
  public function testRepeatedUrlKeepsTheNodeCacheTag(): void {
    $node = Node::create([
      'type' => 'page',
      'title' => 'Test node',
      DomainSourceElementManagerInterface::DOMAIN_SOURCE_FIELD => 'one_example_com',
    ]);
    $node->save();

    foreach (['first', 'second'] as $call) {
      $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()])->toString(TRUE);
      $this->assertStringStartsWith('http://one.example.com/', $url->getGeneratedUrl(), $call);
      $this->assertContains('node:' . $node->id(), $url->getCacheTags(), $call);
    }
  }

}
