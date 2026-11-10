<?php

namespace Drupal\Tests\domain_config_ui\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\domain\Entity\Domain;
use Drupal\domain_config_ui\DomainConfigUIManagerInterface;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests registration state for multi-config forms.
 *
 * A ConfigFormBase can edit several config names (the account settings form
 * edits system.site, user.mail and user.settings). The toggler shown by
 * domain_config_ui is one control for the whole form, so a form counts as
 * registered for a domain only when every one of its config names is
 * registered. Previously the check looked at the first name only, so enabling
 * a shared config such as system.site made every form that lists it first
 * (account settings) appear registered.
 *
 * @group domain_config_ui
 *
 * @see https://www.drupal.org/project/domain/issues/3593113
 */
#[Group('domain_config_ui')]
#[RunTestsInSeparateProcesses]
class DomainConfigRegistrationTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    // The domain_config language_manager decorator inherits the parent
    // service definition, which is ConfigurableLanguageManager only when the
    // language module is installed. (3.x decoupled this via #3588828, but
    // 3.0.x still requires language for the service to resolve.)
    'language',
    'domain',
    'domain_config',
    'domain_config_ui',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('user');
    $this->installEntitySchema('domain');
    $this->installConfig(['system', 'domain_config_ui']);

    foreach (['example_com' => 'Example', 'one_example_com' => 'One'] as $id => $name) {
      Domain::create([
        'id' => $id,
        'hostname' => str_replace('_', '.', $id),
        'name' => $name,
        'scheme' => 'http',
        'status' => 1,
        'weight' => 0,
        'is_default' => $id === 'example_com',
      ])->save();
    }
  }

  /**
   * A multi-config form is registered only when all its names are registered.
   *
   * Registers only the shared system.site config, then asserts the
   * account-settings set is reported as not registered (it also needs
   * user.mail and user.settings). After registering those too, the set is
   * reported as registered. Before the fix only the first name was checked,
   * so the partial set wrongly reported as registered.
   */
  public function testRegistrationRequiresAllConfigNames(): void {
    $manager = $this->container->get(DomainConfigUIManagerInterface::class);
    $names = ['system.site', 'user.mail', 'user.settings'];

    // Only the shared system.site name is registered so far.
    $manager->addConfigurationsToDomain('one_example_com', ['system.site']);
    $this->assertFalse(
      $manager->isConfigurationRegisteredForDomain('one_example_com', $names),
      'A set with only system.site registered is not reported as registered.',
    );

    // Once every name in the set is registered, the set is registered.
    $manager->addConfigurationsToDomain('one_example_com', ['user.mail', 'user.settings']);
    $this->assertTrue(
      $manager->isConfigurationRegisteredForDomain('one_example_com', $names),
      'A set with every name registered is reported as registered.',
    );
  }

}
