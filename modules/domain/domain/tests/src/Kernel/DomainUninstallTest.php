<?php

namespace Drupal\Tests\domain\Kernel;

use Drupal\KernelTests\KernelTestBase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests that the module uninstalls cleanly.
 *
 * Uninstalling purges deleted field data from within hook_uninstall(), because
 * core leaves behind the fields that configuration entities provided. The batch
 * size handed to the purger has to be resolved from wherever the running core
 * version keeps it, and the purger types that argument as an int, so a lookup
 * resolving to NULL aborts the uninstall rather than merely purging the wrong
 * amount. Drupal 11.4 stopped installing field.settings, so on that version a
 * site which never changed the batch size has no config object left to read.
 *
 * @see domain_uninstall()
 *
 * @group domain
 */
#[Group('domain')]
#[RunTestsInSeparateProcesses]
class DomainUninstallTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'field',
    'options',
    'system',
    'user',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // ModuleInstaller::uninstall() clears the uninstalled module's rows from
    // the user data store.
    $this->installSchema('user', ['users_data']);
  }

  /**
   * Tests uninstalling the module with no purge batch size configured.
   */
  public function testUninstall(): void {
    // The state this guards against: no field.settings config object exists,
    // which is what a Drupal 11.4 site looks like unless it carries a
    // non-default batch size.
    $this->assertTrue($this->config('field.settings')->isNew());

    $this->container->get('module_installer')->uninstall(['domain']);

    // Uninstalling rebuilds the kernel, so ask the current container.
    $this->assertFalse(\Drupal::moduleHandler()->moduleExists('domain'));
  }

}
