<?php

namespace Drupal\dboptimize\Cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DbRepairCron.
 *
 * Provide cron functionality for dboptimize module.
 */
class DbRepairCron {

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;
  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;
  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;
  /**
   * The logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  public function __construct(ConfigFactoryInterface $config_factory, StateInterface $state, Connection $database) {
    $this->configFactory = $config_factory;
    $this->state = $state;
    $this->database = $database;
    
  }

  /**
   * Backwards-compatible invoker. Drupal core may call __invoke or run().
   */
  public function __invoke() {
    // Delegate to run() for compatibility.
    return $this->run();
  }

  /**
   * Run database REPAIR on specified tables.
   */
  public function run() {
    $config = $this->configFactory->getEditable('dboptimize.settings');

    $op = 'repair';
    $frequency_seconds = (int) $config->get('cron_frequency_repair') ?: 604800;
    $last_run = (int) $this->state->get("dboptimize.last_run.{$op}", 0);
    $now = time();
    if (($now - $last_run) < $frequency_seconds) {
      return;
    }

    $tables = $config->get('cron_tables_repair') ?? [];
    if (empty($tables)) {
      $result = $this->database->query('SHOW TABLES');
      $tables = array_column($result->fetchAll(\PDO::FETCH_NUM), 0);
    }

    $is_cli = (PHP_SAPI === 'cli' || defined('DRUSH_VERSION'));
    if ($is_cli) {
      $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
      $count = is_array($tables) ? count($tables) : 0;
      fwrite($out, sprintf("dboptimize: starting repair on %d tables at %s\n", $count, date('Y-m-d H:i:s')));
      if (!defined('STDOUT')) {
        fclose($out);
      }
    }

    $repaired = [];
    $errors = [];
    $start = microtime(TRUE);
    $error = NULL;
    try {
      foreach ($tables as $table) {
        if ($this->tableExists($table)) {
          try {
            $this->database->query("REPAIR TABLE `{$table}`");
            $repaired[] = $table;
          }
          catch (\Exception $e) {
            $errors[] = "$table: {$e->getMessage()}";
          }
        }
      }
      if (!empty($repaired)) {
        $this->logger->info('Repaired tables: @tables.', ['@tables' => implode(', ', $repaired)]);
      }
      if (!empty($errors)) {
        $this->logger->error('Errors during repair: @errors', ['@errors' => implode('; ', $errors)]);
      }
    }
    catch (\Throwable $e) {
      $error = $e->getMessage();
      $this->logger->error('Unexpected repair error: @msg', ['@msg' => $error]);
    } finally {
      $duration = round(microtime(TRUE) - $start, 3);
      $this->state->set("dboptimize.last_run.{$op}", $now);
      $this->state->set("dboptimize.last_duration.{$op}", $duration);
      $this->state->set("dboptimize.last_error.{$op}", $error ?? NULL);

      if ($is_cli) {
        $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
        fwrite($out, sprintf("dboptimize: repair finished at %s\n", date('Y-m-d H:i:s')));
        fwrite($out, sprintf("  tables repaired: %d\n", count($repaired)));
        fwrite($out, sprintf("  duration (s): %s\n", $duration));
        fwrite($out, sprintf("  errors: %d\n", count($errors)));
        if (!defined('STDOUT')) {
          fclose($out);
        }
      }
    }
  }

  /**
   * Check if a table exists in the database.
   */
  protected function tableExists($table_name) {
    $result = $this->database->query("SHOW TABLES LIKE :name", [':name' => $table_name])->fetchField();
    return (bool) $result;
  }

}
