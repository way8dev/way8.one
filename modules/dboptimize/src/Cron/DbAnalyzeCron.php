<?php

namespace Drupal\dboptimize\Cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DbAnalyzeCron.
 *
 * Provide cron functionality for dboptimize module.
 */
class DbAnalyzeCron {

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
   * Run database ANALYZE on specified tables.
   */
  public function run() {
    $config = $this->configFactory->getEditable('dboptimize.settings');

    $op = 'analyze';
    $frequency_seconds = (int) $config->get('cron_frequency_analyze') ?: 86400;
    $last_run = (int) $this->state->get("dboptimize.last_run.{$op}", 0);
    $now = time();
    if (($now - $last_run) < $frequency_seconds) {
      return;
    }

    $tables = $config->get('cron_tables_analyze') ?? [];
    if (empty($tables)) {
      $result = $this->database->query('SHOW TABLES');
      $tables = array_column($result->fetchAll(\PDO::FETCH_NUM), 0);
    }

    $is_cli = (PHP_SAPI === 'cli' || defined('DRUSH_VERSION'));
    if ($is_cli) {
      $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
      $count = is_array($tables) ? count($tables) : 0;
      fwrite($out, sprintf("dboptimize: starting analyze on %d tables at %s\n", $count, date('Y-m-d H:i:s')));
      if (!defined('STDOUT')) {
        fclose($out);
      }
    }

    $analyzed = [];
    $errors = [];
    $start = microtime(TRUE);
    $error = NULL;
    try {
      foreach ($tables as $table) {
        if ($this->tableExists($table)) {
          try {
            $this->database->query("ANALYZE TABLE `{$table}`");
            $analyzed[] = $table;
          }
          catch (\Exception $e) {
            $errors[] = "$table: {$e->getMessage()}";
          }
        }
      }
      if (!empty($analyzed)) {
        $this->logger->info('Analyzed tables: @tables.', ['@tables' => implode(', ', $analyzed)]);
      }
      if (!empty($errors)) {
        $this->logger->error('Errors during analyze: @errors', ['@errors' => implode('; ', $errors)]);
      }
    }
    catch (\Throwable $e) {
      $error = $e->getMessage();
      $this->logger->error('Unexpected analyze error: @msg', ['@msg' => $error]);
    } finally {
      $duration = round(microtime(TRUE) - $start, 3);
      $this->state->set("dboptimize.last_run.{$op}", $now);
      $this->state->set("dboptimize.last_duration.{$op}", $duration);
      $this->state->set("dboptimize.last_error.{$op}", $error ?? NULL);

      if ($is_cli) {
        $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
        fwrite($out, sprintf("dboptimize: analyze finished at %s\n", date('Y-m-d H:i:s')));
        fwrite($out, sprintf("  tables analyzed: %d\n", count($analyzed)));
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
