<?php

namespace Drupal\dboptimize\Form;

/**
 * Provides a form for checking database tables.
 */
class DbOptimizeCheckForm extends DbOptimizeBaseOptimizeForm {

  /**
   * {@inheritdoc}
   */
  protected function getOperation(): string {
    return 'check';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_check_form';
  }

}
