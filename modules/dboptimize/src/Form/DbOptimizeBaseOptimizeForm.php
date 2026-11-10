<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Base class for database optimization forms.
 */
abstract class DbOptimizeBaseOptimizeForm extends FormBase {

  /**
   * Gets the database operation to perform.
   */
  abstract protected function getOperation(): string;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $connection = Database::getConnection();
    $tables = $connection->query('SHOW TABLES')->fetchCol();

    $options = [];
    foreach ($tables as $table) {
      $options[$table] = ['name' => $table];
    }

    $search = $form_state->getValue('search', '');

    $description = '';
    switch ($this->getOperation()) {
      case 'analyze':
        $description = $this->t('<strong>ANALYZE Command</strong>: Updates table statistics used by the query optimizer to improve query performance.');
        break;

      case 'check':
        $description = $this->t('<strong>CHECK Command</strong>: Verifies the integrity of table data and indexes, reporting any detected errors.');
        break;

      case 'repair':
        $description = $this->t('<strong>REPAIR Command</strong>: Attempts to fix issues found in table structure or indexes after a CHECK operation.');
        break;
    }

    $form['form'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('@operation Database Tables', ['@operation' => ucfirst($this->getOperation())]),
      '#description' => $description,
    ];

    $form['form']['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
      '#default_value' => $search,
      '#attributes' => [
        'placeholder' => $this->t('Table name'),
        'class' => ['dboptimize-filter'],
      ],
    ];

    $form['form']['button_actions'] = [
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => ['class' => ['dboptimize-select-all']],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => ['class' => ['dboptimize-toggle']],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => ['class' => ['dboptimize-clear']],
      ],
    ];

    $form['actions_submit_header'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button', 'button--primary', 'dboptimize-submit'],
      ],
      '#value' => $this->t('@operation selected tables', ['@operation' => ucfirst($this->getOperation())]),
    ];

    $form['tables'] = [
      '#type' => 'tableselect',
      '#header' => ['name' => $this->t('Table name')],
      '#options' => $options,
      '#attributes' => ['class' => ['dboptimize-table']],
      '#empty' => $this->t('No tables found.'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('@operation Selected Tables', ['@operation' => ucfirst($this->getOperation())]),
      '#button_type' => 'primary',
    ];
    $form['#attached']['library'][] = 'dboptimize/dboptimize';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected = array_filter($form_state->getValue('tables'));
    if (empty($selected)) {
      $this->messenger()->addWarning($this->t('Please select at least one table.'));
      return;
    }

    // Store selected tables in form_state for confirmation form.
    $form_state->set('selected_tables', $selected);

    // Redirect to confirmation form instead of running batch directly.
    $operation = strtolower($this->getOperation());
    $confirm_route = "dboptimize.{$operation}_confirm";
    $form_state->setRedirect($confirm_route, [], ['query' => ['tables' => implode(',', $selected)]]);
  }

  /**
   * Helper to run the batch process after confirmation.
   */
  public function runBatch(array $tables) {
    $operation = strtoupper($this->getOperation());
    $operations = [];
    foreach ($tables as $table) {
      // Pass $operation as the first argument to the batch process.
      $operations[] = [['\Drupal\dboptimize\Batch\DbOptimizeBatch', 'process'], [$operation, $table]];
    }

    $batch = [
      'title' => $this->t('@operation tables', ['@operation' => $operation]),
      'operations' => $operations,
      'finished' => ['\Drupal\dboptimize\Batch\DbOptimizeBatch', 'finished'],
      'init_message' => $this->t('Starting @operation...', ['@operation' => $operation]),
      'progress_message' => $this->t('Processing @current out of @total.'),
      'error_message' => $this->t('@operation encountered an error.'),
    // Pass operation to batch context.
      'operation' => $operation,
    ];

    batch_set($batch);
  }

}
