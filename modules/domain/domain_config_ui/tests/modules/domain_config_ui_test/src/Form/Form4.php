<?php

namespace Drupal\domain_config_ui_test\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\RedundantEditableConfigNamesTrait;

/**
 * A pure #config_target config form with no getEditableConfigNames() override.
 *
 * Uses RedundantEditableConfigNamesTrait, so getEditableConfigNames() returns
 * an empty array and domain_config_ui attaches its enable/disable toggler
 * through the #after_build path (configFormAfterBuild). Elements added in
 * #after_build are not processed by the form builder, so the toggler has no
 * #parents unless it sets one explicitly. This fixture exists to exercise
 * that path under a validation error.
 *
 * @see https://www.drupal.org/project/domain/issues/3592825
 */
class Form4 extends ConfigFormBase {

  use RedundantEditableConfigNamesTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'domain_config_ui_test_form4';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['field1'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field 1'),
      '#config_target' => 'domain_config_ui_test.settings:field1',
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    // Force a validation error so core's form error walker runs over every
    // element on submit, including the toggler.
    $form_state->setErrorByName('field1', $this->t('Field 1 is invalid (test fixture).'));
  }

}
