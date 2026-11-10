<?php

namespace Drupal\dboptimize\Cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DbOptimizeCron.
 *
 * Provide cron functionality for dboptimize module.
 */
class DbOptimizeCron {

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
   * Run database optimization on specified tables.
   */
  public function run() {
    $config = $this->configFactory->getEditable('dboptimize.settings');

    $op = 'optimize';
    $frequency_seconds = (int) $config->get('cron_frequency') ?: 86400;
    $last_run = (int) $this->state->get("dboptimize.last_run.{$op}", 0);
    $now = time();

    // If called by cron (no manual tables) respect frequency.
    if (($now - $last_run) < $frequency_seconds) {
      return;
    }

    $optimized = [];
    $errors = [];
    $freed_mb = [];

    $tables = $config->get('cron_tables') ?? [];
    if (empty($tables)) {
      $result = $this->database->query('SHOW TABLES');
      $tables = array_column($result->fetchAll(\PDO::FETCH_NUM), 0);
    }

    // Detect CLI/Drush execution and print start info to terminal.
    $is_cli = (PHP_SAPI === 'cli' || defined('DRUSH_VERSION'));
    if ($is_cli) {
      $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
      $count = is_array($tables) ? count($tables) : 0;
      fwrite($out, sprintf("dboptimize: starting optimize on %d tables at %s\n", $count, date('Y-m-d H:i:s')));
      if (!defined('STDOUT')) {
        fclose($out);
      }
    }

    $start = microtime(TRUE);
    $error = NULL;
    try {
      foreach ($tables as $table) {
        if ($this->tableExists($table)) {
          try {
            $size_before = $this->database->query("SHOW TABLE STATUS LIKE '{$table}'")->fetchObject();
            $free_before = $size_before->Data_free ?? 0;

            $this->database->query("OPTIMIZE TABLE `{$table}`");

            $optimized[] = $table;
            $size_after = $this->database->query("SHOW TABLE STATUS LIKE '{$table}'")->fetchObject();
            $free_after = $size_after->Data_free ?? 0;

            $freed_mb[] = round(($free_before - $free_after) / 1048576, 2);
          }
          catch (\Exception $e) {
            $errors[] = "$table: {$e->getMessage()}";
          }
        }
      }
      if ($freed_mb) {
        $freed_mb = array_sum($freed_mb);
      }

      if (!empty($optimized)) {
        $this->logger->info('Optimized tables: @tables. Freed: @total MB', [
          '@tables' => implode(', ', $optimized),
          '@total' => $freed_mb,
        ]);
      }

      if (!empty($errors)) {
        $this->logger->error('Errors during optimization: @errors', [
          '@errors' => implode('; ', $errors),
        ]);
      }
    }
    catch (\Throwable $e) {
      $error = $e->getMessage();
      $this->logger->error('Unexpected error during optimize: @msg', ['@msg' => $error]);
    }
    finally {
      $duration = round(microtime(TRUE) - $start, 3);
      $this->state->set("dboptimize.last_run.{$op}", $now);
      $this->state->set("dboptimize.last_duration.{$op}", $duration);
      $this->state->set("dboptimize.last_error.{$op}", $error ?? NULL);

      // CLI summary output.
      if ($is_cli) {
        $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
        fwrite($out, sprintf("dboptimize: optimize finished at %s\n", date('Y-m-d H:i:s')));
        fwrite($out, sprintf("  tables optimized: %d\n", count($optimized)));
        fwrite($out, sprintf("  freed MB total: %s\n", $freed_mb === [] ? '0' : $freed_mb));
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
