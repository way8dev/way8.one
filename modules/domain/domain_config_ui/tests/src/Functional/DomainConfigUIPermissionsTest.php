<?php

namespace Drupal\Tests\domain_config_ui\Functional;

use Drupal\Tests\domain_config\Functional\DomainConfigTestBase;
use Drupal\Tests\domain_config_ui\Traits\DomainConfigUITestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the domain config user interface.
 *
 * @group domain_config_ui
 */
#[Group('domain_config_ui')]
#[RunTestsInSeparateProcesses]
class DomainConfigUIPermissionsTest extends DomainConfigTestBase {

  use DomainConfigUITestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain_config_ui',
    'domain_config_ui_test',
  ];

  /**
   * {@inheritDoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->createAdminUser();
    $this->createEditorUser();

    $this->domainCreateTestDomains(5);
  }

  /**
   * Tests access the the settings form.
   */
  public function testSettingsAccess() {
    $this->drupalLogin($this->adminUser);
    $path = '/admin/config/domain/config-ui';
    $path2 = '/admin/config/system/site-information';

    // Visit the domain config ui administration page.
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(200);

    // Visit the site information page.
    $this->drupalGet($path2);
    $this->assertSession()->statusCodeEquals(200);
    /* $this->findField('domain'); */

    $this->drupalLogin($this->editorUser);

    // Visit the domain config ui administration page.
    $this->drupalGet($path);
    $this->assertSession()->statusCodeEquals(403);

    // Visit the site information page.
    $this->drupalGet($path2);
    $this->assertSession()->statusCodeEquals(200);
    /* $this->findNoField('domain'); */
  }

  /**
   * The toggler does not break validation on a pure #config_target form.
   *
   * Regression test for #3592825. A ConfigFormBase that uses
   * RedundantEditableConfigNamesTrait (empty getEditableConfigNames()) has the
   * enable/disable toggler attached through the #after_build path, so the
   * toggler element is never processed by the form builder and historically
   * carried no #parents. On any validation error, core's
   * FormState::getError() iterated $element['#parents'] for the toggler and
   * emitted "Undefined array key #parents" — which BrowserTestBase escalates
   * to a failure and which masks the real validation error. This test loads
   * such a form, confirms the toggler is present, submits to force a
   * validation error, and asserts the real error is shown with no PHP
   * warning. With the explicit empty #parents on the toggler it passes;
   * without it the run fails on the warning.
   */
  public function testTogglerParentsOnConfigTargetForm(): void {
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('/admin/config/domain_config_ui_test/form4');
    $this->assertSession()->statusCodeEquals(200);
    // The toggler is attached via the #after_build path on this form.
    $this->assertSession()->linkExists('Enable domain configuration');

    // Submitting triggers the forced validation error, so the form error
    // walker runs over every element including the toggler.
    $this->submitForm(['field1' => 'whatever'], 'Save configuration');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains('Field 1 is invalid (test fixture).');
  }

}
