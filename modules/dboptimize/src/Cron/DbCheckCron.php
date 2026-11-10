<?php

namespace Drupal\dboptimize\Cron;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\State\StateInterface;
use Psr\Log\LoggerInterface;

/**
 * Class DbCheckCron.
 *
 * Provide cron functionality for dboptimize module.
 */
class DbCheckCron {

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
   * Run database CHECK on specified tables.
   */
  public function run() {
    $config = $this->configFactory->getEditable('dboptimize.settings');

    $op = 'check';
    $frequency_seconds = (int) $config->get('cron_frequency_check') ?: 604800;
    $last_run = (int) $this->state->get("dboptimize.last_run.{$op}", 0);
    $now = time();
    if (($now - $last_run) < $frequency_seconds) {
      return;
    }

    $tables = $config->get('cron_tables_check') ?? [];
    if (empty($tables)) {
      $result = $this->database->query('SHOW TABLES');
      $tables = array_column($result->fetchAll(\PDO::FETCH_NUM), 0);
    }

    $is_cli = (PHP_SAPI === 'cli' || defined('DRUSH_VERSION'));
    if ($is_cli) {
      $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
      $count = is_array($tables) ? count($tables) : 0;
      fwrite($out, sprintf("dboptimize: starting check on %d tables at %s\n", $count, date('Y-m-d H:i:s')));
      if (!defined('STDOUT')) {
        fclose($out);
      }
    }

    $checked = [];
    $errors = [];
    $start = microtime(TRUE);
    $error = NULL;
    try {
      foreach ($tables as $table) {
        if ($this->tableExists($table)) {
          try {
            $this->database->query("CHECK TABLE `{$table}`");
            $checked[] = $table;
          }
          catch (\Exception $e) {
            $errors[] = "$table: {$e->getMessage()}";
          }
        }
      }
      if (!empty($checked)) {
        $this->logger->info('Checked tables: @tables.', ['@tables' => implode(', ', $checked)]);
      }
      if (!empty($errors)) {
        $this->logger->error('Errors during check: @errors', ['@errors' => implode('; ', $errors)]);
      }
    }
    catch (\Throwable $e) {
      $error = $e->getMessage();
      $this->logger->error('Unexpected check error: @msg', ['@msg' => $error]);
    } finally {
      $duration = round(microtime(TRUE) - $start, 3);
      $this->state->set("dboptimize.last_run.{$op}", $now);
      $this->state->set("dboptimize.last_duration.{$op}", $duration);
      $this->state->set("dboptimize.last_error.{$op}", $error ?? NULL);

      if ($is_cli) {
        $out = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'w');
        fwrite($out, sprintf("dboptimize: check finished at %s\n", date('Y-m-d H:i:s')));
        fwrite($out, sprintf("  tables checked: %d\n", count($checked)));
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
