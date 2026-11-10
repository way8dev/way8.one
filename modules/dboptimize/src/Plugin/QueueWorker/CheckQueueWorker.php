<?php

namespace Drupal\dboptimize\Plugin\QueueWorker;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\Core\State\StateInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Dboptimize check queue worker.
 *
 * @QueueWorker(
 *   id = "dboptimize_check",
 *   title = @Translation("Dboptimize check queue worker"),
 *   cron = {"time" = 0}
 * )
 */
class CheckQueueWorker extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  /**
   * Database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * State management.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Logger service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Optional cron service for check operation.
   *
   * If present, will be used to delegate execution to the Cron.
   *
   * @var object|null
   */
  protected $cronService;

  /**
   * Constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Connection $database, StateInterface $state, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->state = $state;
    $this->configFactory = $config_factory;
    
  }

  /**
   * Creates an instance of the queue worker.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    // Attempt to get the optional cron service for the 'check' operation. If it
    // doesn't exist, pass NULL and the worker will use its fallback logic.
    $cron_service = NULL;
    if ($container->has('dboptimize.cron.check')) {
      $cron_service = $container->get('dboptimize.cron.check');
    }

    $instance = new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('state'),
      $container->get('config.factory'),
    );
    $instance->cronService = $cron_service;
    return $instance;
  }

  /**
   * Process a single item in the queue.
   */
  public function processItem($data) {
    // If a cron service for this operation exists and exposes a run() method,
    // delegate execution to it. This avoids duplicating the logic present in
    // /src/Cron/*. If not available, fallback to the worker's internal logic.
    $op = $data['op'] ?? 'check';

    if ($this->cronService && is_callable([$this->cronService, 'run'])) {
      try {
        $this->cronService->run();
        $this->logger->info('Delegated @op execution to cron service from queue worker.', ['@op' => $op]);
      }
      catch (\Throwable $e) {
        $this->logger->error('Cron service delegation failed for @op: @msg', ['@op' => $op, '@msg' => $e->getMessage()]);
      }
      return;
    }

  }

}
