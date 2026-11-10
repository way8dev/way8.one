<?php

namespace Drupal\dboptimize\Form;

/**
 * Provides a form for analyzing database tables.
 */
class DbOptimizeAnalyzeForm extends DbOptimizeBaseOptimizeForm {

  /**
   * {@inheritdoc}
   */
  protected function getOperation(): string {
    return 'analyze';
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_analyze_form';
  }

}
