<?php

namespace Drupal\dboptimize\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for displaying database overview.
 */
class DbOptimizeOverviewController extends ControllerBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * Constructs a new DbOptimizeOverviewController.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(Connection $database) {
    $this->database = $database;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database')
    );
  }

  /**
   * Returns an overview of the database size and key tables.
   */
  public function overview() {
    $tables = $this->database->query('SHOW TABLE STATUS')->fetchAll();
    $total_size = 0;

    $groups = [
      'node_field' => 0,
      'node_revision' => 0,
      'media' => 0,
      'media_revision' => 0,
      'taxonomy' => 0,
      'paragraphs' => 0,
      'paragraph_revision' => 0,
      'paragraph_field' => 0,
      'block' => 0,
      'comment' => 0,
      'cache' => 0,
      'other' => 0,
    ];

    foreach ($tables as $table) {
      // MB.
      $size = ($table->Data_length + $table->Index_length) / 1048576;
      $total_size += $size;
      $name = $table->Name;

      if (preg_match('/^node__/', $name) || preg_match('/^node_field_/', $name)) {
        $groups['node_field'] += $size;
      }
      elseif (preg_match('/^node_revision/', $name) || preg_match('/^node__revision/', $name)) {
        $groups['node_revision'] += $size;
      }
      elseif (preg_match('/^media__/', $name) || preg_match('/^media_field_/', $name)) {
        $groups['media'] += $size;
      }
      elseif (preg_match('/^media_revision/', $name) || preg_match('/^media__revision/', $name)) {
        $groups['media_revision'] += $size;
      }
      elseif (preg_match('/^taxonomy_/', $name)) {
        $groups['taxonomy'] += $size;
      }
      elseif (preg_match('/^paragraph__/', $name) || preg_match('/^paragraphs_/', $name)) {
        $groups['paragraphs'] += $size;
      }
      elseif (preg_match('/^paragraph_revision/', $name) || preg_match('/^paragraph__revision/', $name)) {
        $groups['paragraph_revision'] += $size;
      }
      elseif (preg_match('/^paragraph_field_/', $name)) {
        $groups['paragraph_field'] += $size;
      }
      elseif (preg_match('/^block(_|__)/', $name) || preg_match('/^block_content/', $name)) {
        $groups['block'] += $size;
      }
      elseif (preg_match('/^comment(_|__)/', $name)) {
        $groups['comment'] += $size;
      }
      elseif (preg_match('/^cache/', $name)) {
        $groups['cache'] += $size;
      }
      else {
        $groups['other'] += $size;
      }
    }

    $build = [
      '#type' => 'fieldset',
      'title' => [
        '#markup' => '<h2>' . $this->t('Overview') . '</h2>',
      ],
      'total' => [
        '#markup' => '<h5>' . $this->t('Total database size:') . ' <strong>' . number_format($total_size, 2) . '</strong> MB</h5>',
      ],
      'summary_table' => [
        '#type' => 'table',
        '#header' => [$this->t('Group'), $this->t('Total Size (MB)'), $this->t('% of DB')],
        '#rows' => [],
        '#attributes' => ['class' => ['dboptimize-overview-summary']],
      ],
    ];

    foreach ($groups as $group => $group_size) {
      if ($group_size > 0) {
        $build['summary_table']['#rows'][] = [
          ucfirst(str_replace('_', ' ', $group)),
          number_format($group_size, 2),
          $total_size > 0 ? number_format(($group_size / $total_size) * 100, 2) . '%' : '0%',
        ];
      }
    }

    // Show top 5 largest tables (optional, for quick insight)
    $largest_tables = [];
    foreach ($tables as $table) {
      $largest_tables[$table->Name] = ($table->Data_length + $table->Index_length) / 1048576;
    }
    arsort($largest_tables);
    $largest_rows = [];
    foreach (array_slice($largest_tables, 0, 10, TRUE) as $name => $size) {
      $largest_rows[] = [$name, number_format($size, 2)];
    }
    $build['largest_tables'] = [
      '#markup' => '<h3>' . $this->t('Top 10 Largest Tables') . '</h3>',
    ];
    $build['largest_tables_table'] = [
      '#type' => 'table',
      '#header' => [$this->t('Table'), $this->t('Size (MB)')],
      '#rows' => $largest_rows,
      '#attributes' => ['class' => ['dboptimize-overview-largest']],
    ];

    return $build;
  }

}
