<?php

namespace Drupal\dboptimize\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Controller that describes available dboptimize Drush commands.
 */
class CommandsInfoController extends ControllerBase {

  /**
   * Overview page listing Drush commands, description and usage.
   */
  public function overview() {
    $header = [
      $this->t('Command'),
      $this->t('Description'),
      $this->t('Usage / Examples'),
    ];

    // List of commands with description and examples. Keep in sync with
    // src/Commands/*.php implementations.
    $commands = [
      [
        'cmd' => 'drush dboptimize:optimize',
        'desc' => $this->t('Optimize tables (OPTIMIZE TABLE)'),
        'usage' => $this->t('drush dboptimize:optimize --tables="cache,cache_bootstrap"'),
      ],
      [
        'cmd' => 'drush dboptimize:analyze',
        'desc' => $this->t('Analyze tables (ANALYZE TABLE)'),
        'usage' => $this->t('drush dboptimize:analyze --tables="cache,cache_bootstrap"'),
      ],
      [
        'cmd' => 'drush dboptimize:check',
        'desc' => $this->t('Check tables (CHECK TABLE)'),
        'usage' => $this->t('drush dboptimize:check --tables="cache,cache_bootstrap"'),
      ],
      [
        'cmd' => 'drush dboptimize:repair',
        'desc' => $this->t('Repair tables (REPAIR TABLE)'),
        'usage' => $this->t('drush dboptimize:repair --tables="cache,cache_bootstrap"'),
      ],
      [
        'cmd' => 'drush dboptimize:cron-optimize',
        'desc' => $this->t('Run the optimize cron handler (honors frequency).'),
        'usage' => $this->t('drush dboptimize:cron-optimize --bypass-last-run'),
      ],
      [
        'cmd' => 'drush dboptimize:cron-analyze',
        'desc' => $this->t('Run the analyze cron handler (honors frequency).'),
        'usage' => $this->t('drush dboptimize:cron-analyze --bypass-last-run'),
      ],
      [
        'cmd' => 'drush dboptimize:cron-repair',
        'desc' => $this->t('Run the repair cron handler (honors frequency).'),
        'usage' => $this->t('drush dboptimize:cron-repair --bypass-last-run'),
      ],
      [
        'cmd' => 'drush dboptimize:cron-check',
        'desc' => $this->t('Run the check cron handler (honors frequency).'),
        'usage' => $this->t('drush dboptimize:cron-check --bypass-last-run'),
      ],
    ];

    $rows = [];
    foreach ($commands as $c) {
      $rows[] = [
        'data' => [
          ['data' => $c['cmd']],
          ['data' => $c['desc']],
          ['data' => $c['usage']],
        ],
      ];
    }

    $build = [];
    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
      '#attributes' => ['class' => ['dboptimize-commands-table']],
    ];

    $build['#title'] = $this->t('DbOptimize Drush commands');
    return $build;
  }

}
