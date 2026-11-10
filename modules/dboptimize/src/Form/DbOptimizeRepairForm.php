<?php

namespace Drupal\dboptimize\Form;

/**
 * Provides a form for repairing database tables.
 */
class DbOptimizeRepairForm extends DbOptimizeBaseOptimizeForm {

  /**
   * {@inheritdoc}
   */
  protected function getOperation(): string {
    return 'repair';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_repair_form';
  }

}
