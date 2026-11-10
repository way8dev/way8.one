<?php

namespace Drupal\dboptimize\Commands;

use Drupal\Core\Database\Connection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drush\Commands\DrushCommands;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides Drush commands for dboptimize module.
 */
class DbOptimizeCommands extends DrushCommands {

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The database connection service.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new DbOptimizeCommands object.
   */
  public function __construct(ModuleHandlerInterface $module_handler, Connection $database) {
    parent::__construct();
    $this->moduleHandler = $module_handler;
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('database')
    );
  }

  /**
   * Run ANALYZE on tables.
   *
   * @command dboptimize:analyze
   * @aliases dbo-analyze
   * @option tables Comma-separated list of tables. If empty, all tables are analyzed.
   */
  public function analyze(array $options = ['tables' => '']) {
    $this->runCommandBatch('ANALYZE', $options['tables'] ?? '');
  }

  /**
   * Run CHECK on tables.
   *
   * @command dboptimize:check
   * @aliases dbo-check
   * @option tables Comma-separated list of tables. If empty, all tables are checked.
   */
  public function check(array $options = ['tables' => '']) {
    $this->runCommandBatch('CHECK', $options['tables'] ?? '');
  }

  /**
   * Run REPAIR on tables.
   *
   * @command dboptimize:repair
   * @aliases dbo-repair
   * @option tables Comma-separated list of tables. If empty, all tables are repaired.
   */
  public function repair(array $options = ['tables' => '']) {
    $this->runCommandBatch('REPAIR', $options['tables'] ?? '');
  }

  /**
   * Run OPTIMIZE on tables.
   *
   * @command dboptimize:optimize
   * @aliases dbo-optimize,dbo
   * @option tables Comma-separated list of tables. If empty, all tables are optimized.
   */
  public function optimize(array $options = ['tables' => '']) {
    $this->runCommandBatch('OPTIMIZE', $options['tables'] ?? '');
  }

  /**
   * Internal method to run a "batch-like" table operation.
   */
  protected function runCommandBatch(string $command, string $tables_option = '') {
    $database = $this->database;

    // Get tables to process.
    if (!empty($tables_option)) {
      $tables = array_map('trim', explode(',', $tables_option));
    }
    else {
      $tables = $database->query('SHOW TABLES')->fetchCol();
    }

    $this->output()->writeln("▶️ Running {$command} on " . count($tables) . " tables...");

    $successCount = 0;
    $failCount = 0;

    foreach ($tables as $table) {
      $this->output()->write("Processing table: $table ... ");

      try {
        $results = $database->query("$command TABLE `{$table}`")->fetchAll();
        foreach ($results as $row) {
          $rowArray = (array) $row;
          $outputLine = implode(' | ', $rowArray);
          $this->output()->writeln($outputLine);
        }
        $this->output()->writeln("<info>OK</info>");
        $successCount++;
      }
      catch (\Exception $e) {
        $this->output()->writeln("<error>❌ Error: {$e->getMessage()}</error>");
        $failCount++;
      }
    }

    $this->output()->writeln("✅ {$command} completed for all tables.");
    $this->output()->writeln("Summary: Processed " . count($tables) . " tables. Success: $successCount. Failed: $failCount.");
  }

}
