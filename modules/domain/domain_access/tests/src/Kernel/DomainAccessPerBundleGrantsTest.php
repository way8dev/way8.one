<?php

namespace Drupal\Tests\domain_access\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\domain\Traits\DomainTestTrait;
use Drupal\Tests\node\Traits\NodeCreationTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;
use Drupal\domain_access\DomainAccessManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the node grants handed out when per-bundle grants are enabled.
 *
 * With the per_bundle_grants setting on, Domain Access writes one extra realm
 * per content type, such as domain_id:article, into the node access records,
 * and grants it to users who may edit or delete that type of content on their
 * assigned domains. The grant must cover every such content type, and only the
 * domains the user is assigned to: an editor who browses another domain must
 * not receive the realm for it, or core's grants check lets them edit and
 * delete that domain's content.
 *
 * @group domain_access
 */
#[Group('domain_access')]
#[RunTestsInSeparateProcesses]
class DomainAccessPerBundleGrantsTest extends KernelTestBase {

  use DomainTestTrait;
  use NodeCreationTrait;
  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'field',
    'filter',
    'text',
    'user',
    'node',
    'domain',
    'domain_access',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installSchema('user', ['users_data']);
    $this->installEntitySchema('node');
    $this->installEntitySchema('node_type');
    $this->installSchema('node', ['node_access']);
    $this->installConfig(static::$modules);
    $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create(['type' => 'article', 'name' => 'Article'])
      ->save();
    $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create(['type' => 'page', 'name' => 'Page'])
      ->save();
    $this->container->get('module_handler')->loadInclude('domain_access', 'install');
    domain_access_install();
    $this->config('domain_access.settings')->set('per_bundle_grants', TRUE)->save();

    // Keep user 1, who bypasses every access check, out of the way.
    $this->createUser();
    $this->setBaseHostname();
    $this->domainCreateTestDomains(2);
  }

  /**
   * An editor assigned to another domain cannot edit or delete its content.
   */
  public function testEditorAssignedElsewhereCannotEditOrDelete(): void {
    [$browsed, $assigned] = array_values($this->container->get('entity_type.manager')->getStorage('domain')->loadMultipleSorted());

    $editor = $this->createUser([
      'access content',
      'edit domain content',
      'delete domain content',
      'update article content on assigned domains',
      'delete article content on assigned domains',
    ]);
    $editor->set(DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, [$assigned->id()])->save();

    $this->container->get('domain.negotiation_context')->setDomain($browsed);
    $node = $this->createNode([
      'type' => 'article',
      DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD => [$browsed->id()],
      DomainAccessManagerInterface::DOMAIN_ACCESS_ALL_FIELD => 0,
    ]);

    $this->assertFalse($node->access('update', $editor), 'The editor cannot edit an article on a domain they are not assigned to.');
    $this->assertFalse($node->access('delete', $editor), 'The editor cannot delete an article on a domain they are not assigned to.');
  }

  /**
   * An editor gets a per-type grant for every type on an assigned domain.
   */
  public function testAssignedEditorGetsEveryPerTypeGrant(): void {
    [$assigned] = array_values($this->container->get('entity_type.manager')->getStorage('domain')->loadMultipleSorted());

    $editor = $this->createUser([
      'access content',
      'update article content on assigned domains',
      'update page content on assigned domains',
      'delete article content on assigned domains',
      'delete page content on assigned domains',
    ]);
    $editor->set(DomainAccessManagerInterface::DOMAIN_ACCESS_FIELD, [$assigned->id()])->save();
    $this->container->get('domain.negotiation_context')->setDomain($assigned);

    foreach (['update', 'delete'] as $op) {
      $grants = $this->container->get('module_handler')->invokeAll('node_grants', [$editor, $op]);
      foreach (['article', 'page'] as $type) {
        $this->assertSame([$assigned->getDomainId()], $grants['domain_id:' . $type] ?? [], "The $op grant covers $type content on the assigned domain.");
        $this->assertSame([$assigned->getDomainId()], $grants['domain_unpublished:' . $type] ?? [], "The $op grant covers unpublished $type content on the assigned domain.");
      }
    }
  }

}
