<?php

namespace Drupal\Tests\domain_config_ui\Functional;

use Drupal\Tests\domain_config\Functional\DomainConfigTestBase;
use Drupal\Tests\domain_config_ui\Traits\DomainConfigUITestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests per-config selection on the override delete confirmation form.
 *
 * @group domain_config_ui
 */
#[Group('domain_config_ui')]
#[RunTestsInSeparateProcesses]
class DomainConfigUIDeleteFormTest extends DomainConfigTestBase {

  use DomainConfigUITestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain_config_ui',
  ];

  /**
   * The default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * Seeds per-domain overrides for the test domain.
   *
   * @param array $values
   *   Keyed by config name, each value an array of key/value pairs to set on
   *   that override.
   *
   * @return \Drupal\domain_config\Config\DomainConfigFactoryOverrideInterface
   *   The base override factory, for convenience.
   */
  protected function seedOverrides(array $values) {
    $base_factory = $this->container->get('domain.config_factory_override');
    foreach ($values as $config_name => $pairs) {
      $override = $base_factory->getOverrideEditable('example_com', $config_name);
      foreach ($pairs as $key => $value) {
        $override->set($key, $value);
      }
      $override->save();
    }
    return $base_factory;
  }

  /**
   * Unchecking a configuration on the delete form keeps that override.
   *
   * Seeds two per-domain overrides (system.site and system.maintenance),
   * opens the delete confirmation form for both, unchecks system.site, and
   * submits. Asserts that the checked override (system.maintenance) is
   * deleted while the unchecked one (system.site) survives intact.
   *
   * This locks in the per-config delete control from #3593113: a single
   * "remove" trigger can no longer wipe an override the user meant to keep,
   * such as losing site-information values when clearing account settings
   * that share the system.site object.
   */
  public function testDeleteFormDeletesOnlySelectedOverrides(): void {
    $this->createAdminUser();
    $this->domainCreateTestDomains(1);
    $this->seedOverrides([
      'system.site' => ['name' => 'Kept site name'],
      'system.maintenance' => ['message' => 'Deleted maintenance message'],
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/domain/config_ui/delete/example_com/system.site,system.maintenance');
    $this->assertSession()->statusCodeEquals(200);

    // Both configurations are offered as checkboxes, checked by default.
    $this->assertSession()->checkboxChecked('config_names[system.site]');
    $this->assertSession()->checkboxChecked('config_names[system.maintenance]');

    // Keep system.site, delete only system.maintenance.
    $this->submitForm([
      'config_names[system.site]' => FALSE,
    ], 'Delete configuration');
    $this->assertSession()->statusCodeEquals(200);

    // Read the stored overrides afresh after the submit.
    $this->resetAll();
    $base_factory = $this->container->get('domain.config_factory_override');

    // The unchecked override survives intact.
    self::assertSame(
      'Kept site name',
      $base_factory->getOverride('example_com', 'system.site')->get('name'),
      'The unchecked system.site override is preserved.'
    );
    // The checked override is deleted.
    self::assertSame(
      [],
      $base_factory->getOverride('example_com', 'system.maintenance')->getRawData(),
      'The checked system.maintenance override is deleted.'
    );
  }

  /**
   * Submitting with no configuration selected raises a validation error.
   *
   * Opens the multi-config delete form, unchecks every box, and submits.
   * Asserts the validation error is shown and that both overrides are left
   * untouched, so an empty submission cannot silently redirect as if a
   * deletion had happened.
   */
  public function testDeleteFormRejectsEmptySelection(): void {
    $this->createAdminUser();
    $this->domainCreateTestDomains(1);
    $base_factory = $this->seedOverrides([
      'system.site' => ['name' => 'Kept site name'],
      'system.maintenance' => ['message' => 'Kept maintenance message'],
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/domain/config_ui/delete/example_com/system.site,system.maintenance');
    $this->assertSession()->statusCodeEquals(200);

    // Uncheck every option and submit.
    $this->submitForm([
      'config_names[system.site]' => FALSE,
      'config_names[system.maintenance]' => FALSE,
    ], 'Delete configuration');
    $this->assertSession()->pageTextContains('Select at least one configuration override to delete.');

    // Both overrides are untouched.
    $this->resetAll();
    $base_factory = $this->container->get('domain.config_factory_override');
    self::assertSame('Kept site name', $base_factory->getOverride('example_com', 'system.site')->get('name'));
    self::assertSame('Kept maintenance message', $base_factory->getOverride('example_com', 'system.maintenance')->get('message'));
  }

  /**
   * A single override is confirmed without a checkbox and still deletes.
   *
   * Opens the delete form for a lone override and asserts no checkbox is
   * rendered (there is nothing to choose), the help names the override, and
   * pressing the confirm button deletes it. Covers the single-config UI
   * simplification requested in the #3593113 review.
   */
  public function testSingleOverrideShowsNoCheckbox(): void {
    $this->createAdminUser();
    $this->domainCreateTestDomains(1);
    $this->seedOverrides([
      'system.site' => ['name' => 'Lone override'],
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/domain/config_ui/delete/example_com/system.site');
    $this->assertSession()->statusCodeEquals(200);

    // No checkbox is rendered for the single override, and the help names it.
    $this->assertSession()->fieldNotExists('config_names[system.site]');
    $this->assertSession()->pageTextContains('Delete the system.site override');

    // The review section is open by default for a single override.
    $review = $this->getSession()->getPage()->find('xpath', '//details[summary[contains(., "Review settings")]]');
    self::assertNotNull($review, 'The review section is present.');
    self::assertTrue($review->hasAttribute('open'), 'The review section is open by default for a single override.');

    // No redundant per-config sub-section is rendered for a single override.
    $inner = $this->getSession()->getPage()->find('xpath', '//details[summary[contains(., "system.site")]]');
    self::assertNull($inner, 'A single override is not wrapped in its own config section.');

    $this->submitForm([], 'Delete configuration');
    $this->assertSession()->statusCodeEquals(200);

    // The override is deleted.
    $this->resetAll();
    $base_factory = $this->container->get('domain.config_factory_override');
    self::assertSame(
      [],
      $base_factory->getOverride('example_com', 'system.site')->getRawData(),
      'The single override is deleted.'
    );
  }

  /**
   * A registered config name with no override shows a clear note.
   *
   * Seeds only system.site but opens the delete form for both system.site
   * and system.maintenance, mirroring a form that registers several config
   * names while only one carries overrides. Asserts the empty one renders
   * the "Not overridden" note rather than a blank review block.
   */
  public function testReviewShowsNoteWhenConfigNotOverridden(): void {
    $this->createAdminUser();
    $this->domainCreateTestDomains(1);
    $this->seedOverrides([
      'system.site' => ['name' => 'Kept site name'],
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/domain/config_ui/delete/example_com/system.site,system.maintenance');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Not overridden for this domain.');

    // The review section is open by default, even with several configs.
    $review = $this->getSession()->getPage()->find('xpath', '//details[summary[contains(., "Review settings")]]');
    self::assertNotNull($review, 'The review section is present.');
    self::assertTrue($review->hasAttribute('open'), 'The review section is open by default.');

    // Each per-config section is also expanded by default.
    $inner = $this->getSession()->getPage()->find('xpath', '//details[summary[contains(., "system.maintenance")]]');
    self::assertNotNull($inner, 'The per-config section is present.');
    self::assertTrue($inner->hasAttribute('open'), 'Each per-config section is expanded by default.');
  }

}
