<?php

namespace Drupal\dboptimize\Batch;

use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Batch operations for database optimization.
 */
class DbOptimizeBatch {

  /**
   * Processes a single database table.
   */
  public static function process($operation, $table, &$context) {
    $connection = Database::getConnection();
    $query = "$operation TABLE `$table`";

    try {
      $result = $connection->query($query)->fetchAll();
      $output = [];

      foreach ($result as $row) {
        $output[] = "{$row->Table}: {$row->Msg_type} - {$row->Msg_text}";
      }

      $context['results']['success'][] = implode("\n", $output);
    }
    catch (\Exception $e) {
      $context['results']['error'][] = "$table: " . $e->getMessage();
    }

    $context['message'] = t('Processing @table...', ['@table' => $table]);
  }

  /**
   * Batch finish callback.
   */
  public static function finished($success, $results, $operations) {
    $message = '';

    if (!empty($results['success'])) {
      $message .= "<h3><pre>" . implode("\n\n", $results['success']) . "</pre>";
    }

    if (!empty($results['error'])) {
      $message .= "<h3>❌ Erros:</h3><pre>" . implode("\n", $results['error']) . "</pre>";
    }

    \Drupal::messenger()->addMessage(t('Operation completed. See results below.'), 'status');
    \Drupal::state()->set('dboptimize.results', $message);

    $response = new RedirectResponse(Url::fromRoute('dboptimize.results')->toString());
    $response->send();
  }

}
