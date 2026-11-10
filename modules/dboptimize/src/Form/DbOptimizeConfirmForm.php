<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Batch\BatchBuilder;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Provides a confirmation form for optimizing selected database tables.
 */
class DbOptimizeConfirmForm extends ConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_confirm_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to optimize the selected tables?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('dboptimize.optimize_form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Confirm');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $tables = \Drupal::request()->getSession()->get('selected_tables', []);

    $form['fieldet'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Tables to be optimized:'),
    ];

    $form['fieldet']['tables'] = [
      '#markup' => '<p>' . implode('<br>', array_map('htmlspecialchars', $tables)) . '</p>',
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tables = \Drupal::request()->getSession()->get('selected_tables', []);

    $batch = (new BatchBuilder())
      ->setTitle($this->t('Optimizing database tables'))
      ->setFinishCallback([static::class, 'optimizeFinished']);

    foreach ($tables as $table) {
      $batch->addOperation([static::class, 'optimizeTable'], [$table]);
    }

    batch_set($batch->toArray());
  }

  /**
   * Batch operation callback to optimize a single table.
   *
   * @param string $table
   *   The table name.
   * @param array $context
   *   The batch context array.
   */
  public static function optimizeTable($table, array &$context) {
    $size_before = \Drupal::database()->query("SHOW TABLE STATUS LIKE '{$table}'")->fetchObject();
    $free_before = $size_before->Data_free;

    \Drupal::database()->query("OPTIMIZE TABLE `{$table}`");

    $size_after = \Drupal::database()->query("SHOW TABLE STATUS LIKE '{$table}'")->fetchObject();
    $free_after = $size_after->Data_free;

    $freed_mb = round(($free_before - $free_after) / 1048576, 2);

    $context['results']['optimized'][] = ['table' => $table, 'freed' => $freed_mb];
    $context['message'] = t('Optimized @table: freed @freed MB', ['@table' => $table, '@freed' => $freed_mb]);
  }

  /**
   * Batch finished callback for optimization.
   *
   * @param bool $success
   *   Whether the batch was successful.
   * @param array $results
   *   The results array.
   * @param array $operations
   *   The operations array.
   */
  public static function optimizeFinished($success, $results, $operations) {
    $total_freed = 0;
    foreach ($results['optimized'] as $data) {
      $total_freed += $data['freed'];
    }

    \Drupal::messenger()->addMessage(t('Database optimization completed. Total space freed: @total MB.', ['@total' => round($total_freed, 2)]));

    $response = new RedirectResponse(Url::fromRoute('dboptimize.optimize_form')->toString());
    $response->send();
  }

}
