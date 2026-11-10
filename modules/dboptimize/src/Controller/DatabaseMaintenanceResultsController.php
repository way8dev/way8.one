<?php

namespace Drupal\dboptimize\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying database maintenance results.
 */
class DatabaseMaintenanceResultsController extends ControllerBase {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('state')
    );
  }

  /**
   * Displays the database maintenance results table.
   */
  public function view() {
    $results = $this->state->get('dboptimize.db_maintenance_results', []);

    $build = [];

    $build['actions'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['mb-3']],
      'clear' => [
        '#type' => 'link',
        '#title' => $this->t('Clear results'),
        '#url' => Url::fromRoute('dboptimize.database_maintenance_results_clear'),
        '#attributes' => [
          'class' => ['button', 'button--danger'],
        ],
      ],
      'back' => [
        '#type' => 'link',
        '#title' => $this->t('Back to maintenance form'),
        '#url' => Url::fromRoute('dboptimize.database_maintenance'),
        '#attributes' => [
          'class' => ['button'],
        ],
      ],
    ];

    if (empty($results)) {
      $build['empty'] = [
        '#markup' => $this->t('No batch results available.'),
      ];
      return $build;
    }

    $header = [
      $this->t('Table'),
      $this->t('Status'),
      $this->t('Output'),
    ];

    $rows = [];
    foreach ($results as $r) {
      $rows[] = [
        'data' => [
          ['#markup' => '<strong>' . $r['table'] . '</strong>'],
          ['#markup' => $r['status']],
          ['#markup' => '<small>' . $r['output'] . '</small>'],
        ],
        'class' => $r['status'] === 'OK' ? ['color-ok'] : ['color-error'],
      ];
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => $this->t('No results.'),
      '#attributes' => ['class' => ['database-maintenance-results']],
    ];

    return $build;
  }

  /**
   * Clears the stored database maintenance results.
   */
  public function clear() {
    $this->state->delete('dboptimize.db_maintenance_results');
    $this->messenger()->addStatus($this->t('Results have been cleared.'));
    return $this->redirect('dboptimize.database_maintenance_results');
  }

}
