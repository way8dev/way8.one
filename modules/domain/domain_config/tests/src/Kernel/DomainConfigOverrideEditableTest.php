<?php

namespace Drupal\Tests\domain_config\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\domain\Entity\Domain;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests that empty domain config overrides are not persisted.
 *
 * An override with no overridden values must not be written as an empty
 * record into the domain config collection: an empty override otherwise
 * lingers and is perpetually reported as modified by config import/export
 * (drush cex/cim), with no diff and no way to remove it through sync.
 *
 * @group domain_config
 *
 * @see \Drupal\domain_config\Config\DomainConfigOverrideEditable::save()
 * @see https://www.drupal.org/project/domain/issues/3593636
 */
#[Group('domain_config')]
#[RunTestsInSeparateProcesses]
class DomainConfigOverrideEditableTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   *
   * The domain_config language_manager decorator inherits the parent service
   * definition, which is ConfigurableLanguageManager only when the language
   * module is installed, so language is required here on 3.0.x.
   */
  protected static $modules = [
    'system',
    'user',
    'language',
    'domain',
    'domain_config',
  ];

  /**
   * Domain id used by the test.
   */
  private const DOMAIN_ID = 'example_com';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('domain');
    $this->installEntitySchema('user');
    $this->installConfig(['system']);

    Domain::create([
      'id' => self::DOMAIN_ID,
      'hostname' => 'example.com',
      'name' => 'Example',
      'scheme' => 'http',
      'status' => 1,
      'weight' => 0,
      'is_default' => TRUE,
    ])->save();
  }

  /**
   * An override with no overridden values is not written as an empty record.
   *
   * Saving an override editable that has no overridden values must leave no
   * override row in the domain config collection. Before the fix an empty
   * record was written and then perpetually reported as modified by config
   * import/export.
   */
  public function testEmptyOverrideIsNotWritten(): void {
    $this->container->get('config.factory')
      ->getEditable('system.site')
      ->set('name', 'Base name')
      ->save();

    $factory = $this->container->get('domain.config_factory_override');

    // No overridden values are set before saving.
    $factory->getOverrideEditable(self::DOMAIN_ID, 'system.site')->save();

    self::assertFalse(
      $factory->getStorage(self::DOMAIN_ID)->exists('system.site'),
      'An override with no overridden values is not written as an empty record.'
    );
  }

}
