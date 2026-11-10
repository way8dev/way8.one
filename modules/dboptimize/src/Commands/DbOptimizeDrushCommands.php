<?php

namespace Drupal\dboptimize\Commands;

use Drupal\Core\State\StateInterface;
use Drush\Commands\DrushCommands;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface as Container;

/**
 * Drush commands for dboptimize cron operations (separate class).
 */
class DbOptimizeDrushCommands extends DrushCommands {

  /**
   * The container (to fetch cron services dynamically).
   *
   * @var \Symfony\Component\DependencyInjection\ContainerInterface
   */
  protected $container;

  /**
   * State service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs the command class.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The service container.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state service.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logger for dboptimize.
   */
  public function __construct(Container $container, StateInterface $state) {
    parent::__construct();
    $this->container = $container;
    $this->state = $state;
    
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container,
      $container->get('state'),
    );
  }

  /**
   * Run optimize cron via Drush.
   *
   * @param string $tables
   *   Optional comma-separated list of tables.
   * @param array $options
   *   Command options (e.g. 'bypass-last-run').
   *
   * @command dboptimize:cron-optimize
   * @aliases dbo-cron-optimize
   *
   * @option bypass-last-run
   *   Bypass the last_run check (boolean).
   */
  public function optimize($tables = '', array $options = ['bypass-last-run' => FALSE]) {
    return $this->runOp('optimize', $tables, (bool) ($options['bypass-last-run'] ?? FALSE));
  }

  /**
   * Run analyze cron via Drush.
   *
   * @param string $tables
   *   Optional comma-separated list of tables.
   * @param array $options
   *   Command options (e.g. 'bypass-last-run').
   *
   * @command dboptimize:cron-analyze
   * @aliases dbo-cron-analyze
   *
   * @option bypass-last-run
   *   Bypass the last_run check (boolean).
   */
  public function analyze($tables = '', array $options = ['bypass-last-run' => FALSE]) {
    return $this->runOp('analyze', $tables, (bool) ($options['bypass-last-run'] ?? FALSE));
  }

  /**
   * Run repair cron via Drush.
   *
   * @param string $tables
   *   Optional comma-separated list of tables.
   * @param array $options
   *   Command options (e.g. 'bypass-last-run').
   *
   * @command dboptimize:cron-repair
   * @aliases dbo-cron-repair
   *
   * @option bypass-last-run
   *   Bypass the last_run check (boolean).
   */
  public function repair($tables = '', array $options = ['bypass-last-run' => FALSE]) {
    return $this->runOp('repair', $tables, (bool) ($options['bypass-last-run'] ?? FALSE));
  }

  /**
   * Run check cron via Drush.
   *
   * @param string $tables
   *   Optional comma-separated list of tables.
   * @param array $options
   *   Command options (e.g. 'bypass-last-run').
   *
   * @command dboptimize:cron-check
   * @aliases dbo-cron-check
   *
   * @option bypass-last-run
   *   Bypass the last_run check (boolean).
   */
  public function check($tables = '', array $options = ['bypass-last-run' => FALSE]) {
    return $this->runOp('check', $tables, (bool) ($options['bypass-last-run'] ?? FALSE));
  }

  /**
   * Internal helper to run a specific operation service.
   *
   * @param string $op
   *   Operation name: optimize|analyze|repair|check.
   * @param string $tables
   *   Comma separated table names (optional).
   * @param bool $bypass_last_run
   *   If TRUE, set last_run to 0 before executing.
   *
   * @return int
   *   Exit code (0 success, 1 failure).
   */
  protected function runOp(string $op, string $tables = '', bool $bypass_last_run = FALSE): int {
    $service_name = "dboptimize.cron.{$op}";
    $state_key = "dboptimize.last_run.{$op}";

    $this->output()->writeln(sprintf('Running dboptimize %s via Drush...', $op));

    try {
      if ($bypass_last_run) {
        $this->state->set($state_key, 0);
        $this->output()->writeln('Bypass last_run: state cleared.');
      }

      if (!$this->container->has($service_name)) {
        $msg = "Service {$service_name} not found.";
        $this->logger->error($msg);
        $this->output()->writeln("<error>{$msg}</error>");
        return 1;
      }

      $service = $this->container->get($service_name);

      // Show which tables will be passed (if provided).
      if (!empty($tables) && is_string($tables)) {
        $tablesArg = array_values(array_filter(array_map('trim', explode(',', $tables))));
        $count = count($tablesArg);
        $preview = implode(', ', array_slice($tablesArg, 0, 10));
        $this->output()->writeln('Tables provided: ' . $count);
        $this->output()->writeln('Preview: ' . ($preview ?: 'none'));
        if ($count > 10) {
          $this->output()->writeln('...and ' . ($count - 10) . ' more');
        }
      }
      else {
        $this->output()->writeln('Tables: all (no specific tables provided)');
        $tablesArg = NULL;
      }

      $this->output()->writeln('Invoking service: ' . $service_name);

      // Prefer calling run($tables) if the method accepts parameters.
      if (is_callable([$service, 'run'])) {
        $ref = new \ReflectionMethod($service, 'run');
        if ($ref->getNumberOfParameters() >= 1 && $tablesArg !== NULL) {
          $service->run($tablesArg);
        }
        else {
          $service->run();
        }
      }
      elseif (is_callable([$service, '__invoke'])) {
        // Try to pass tables if possible; otherwise call without args.
        try {
          if ($tablesArg !== NULL) {
            $service->__invoke($tablesArg);
          }
          else {
            $service->__invoke();
          }
        }
        catch (\ArgumentCountError $e) {
          $service->__invoke();
        }
      }
      else {
        $msg = "Service {$service_name} is not invokable.";
        $this->logger->warning($msg);
        $this->output()->writeln("<comment>{$msg}</comment>");
        return 1;
      }

      $msg = sprintf('Operation %s executed via Drush.', $op);
      $this->logger->info($msg);
      $this->output()->writeln("<info>{$msg}</info>");

      $last_run = $this->state->get("dboptimize.last_run.{$op}");
      $last_duration = $this->state->get("dboptimize.last_duration.{$op}");
      $last_error = $this->state->get("dboptimize.last_error.{$op}");

      $this->output()->writeln('--- Execution summary ---');
      $this->output()->writeln('Last run timestamp: ' . ($last_run ? date('Y-m-d H:i:s', $last_run) : 'n/a'));
      $this->output()->writeln('Last duration (s): ' . ($last_duration ?? 'n/a'));
      $this->output()->writeln('Last error: ' . ($last_error ? $last_error : 'none'));
      return 0;
    }
    catch (\Throwable $e) {
      $msg = "Error running {$op} via Drush: " . $e->getMessage();
      $this->logger->error($msg);
      $this->output()->writeln("<error>{$msg}</error>");
      return 1;
    }
  }

}
