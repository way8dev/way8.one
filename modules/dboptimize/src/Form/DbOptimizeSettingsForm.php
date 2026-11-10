<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure database optimization settings.
 */
class DbOptimizeSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['dboptimize.settings'];
  }

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->state = $container->get('state');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('dboptimize.settings');

    // Single fieldset that wraps all settings.
    $form['settings'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('DbOptimize Settings'),
      '#attributes' => ['class' => ['dboptimize-form-settings']],
    ];

    // Get the list of database tables.
    $tables = Database::getConnection()->query('SHOW TABLES')->fetchCol();
    $options = array_combine($tables, $tables);

    // Global execution method (single option for all operations).
    $form['settings']['execution_method'] = [
      '#type' => 'radios',
      '#title' => $this->t('Execution method for cron operations'),
      '#description' => $this->t('Choose whether operations are run directly in hook_cron or enqueued to queue workers (background). This is a global setting applied to optimize, analyze, repair and check.'),
      '#options' => [
        'cron' => $this->t('Run in hook_cron'),
        'queue' => $this->t('Enqueue to queue worker'),
      ],
      '#default_value' => $config->get('execution_method') ?? 'cron',
    ];

    // Optimize fieldset.
    $form['settings']['optimize'] = [
      '#type' => 'details',
      '#title' => $this->t('Optimize Settings'),
    ];

    $form['settings']['optimize']['enabled_optimize'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Optimize on cron'),
      '#default_value' => (bool) ($config->get('enabled_optimize') ?? TRUE),
    ];

    $form['settings']['optimize']['cron_frequency'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron frequency'),
      '#description' => $this->t('How often should the cron optimize the tables?'),
      '#options' => [
        3600 => $this->t('Hourly'),
        86400 => $this->t('Daily'),
        604800 => $this->t('Weekly'),
        2629746 => $this->t('Monthly'),
        7889238 => $this->t('Trimester (3 months)'),
        15778476 => $this->t('Semester (6 months)'),
        31556952 => $this->t('Yearly'),
      ],
      '#default_value' => $config->get('cron_frequency') ?? 86400,
      // Show only when enabled.
      '#states' => [
        'visible' => [
          ":input[name='enabled_optimize']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['optimize']['button_actions'] = [
      '#type' => 'container',
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => [
          'class' => ['dboptimize-select-all'],
          'type' => 'button',
        ],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => [
          'class' => ['dboptimize-toggle'],
          'type' => 'button',
        ],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => [
          'class' => ['dboptimize-clear'],
          'type' => 'button',
        ],
      ],
      '#states' => [
        'visible' => [
          ":input[name='enabled_optimize']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['optimize']['cron_tables'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tables to optimize during cron'),
      '#description' => $this->t('Select the tables that should be optimized. If none are selected, all database tables will be optimized.'),
      '#options' => $options,
      '#default_value' => $config->get('cron_tables') ?? [],
      '#states' => [
        'visible' => [
          ":input[name='enabled_optimize']" => ['checked' => TRUE],
        ],
      ],
    ];

    $form['settings']['optimize']['message_note'] = [
      '#markup' => '<p><strong>Note:</strong> If you specify one or more table names above, the cron will optimize only those tables. If you leave this field empty, all database tables will be optimized during cron execution.</p>',
    ];

    // Show last run info.
    $last_run = $this->state->get('dboptimize.last_run.optimize');
    $last_duration = $this->state->get('dboptimize.last_duration.optimize');
    $last_error = $this->state->get('dboptimize.last_error.optimize');
    $form['settings']['optimize']['status'] = [
      '#markup' => $this->t('<p>Last run: @t, duration: @d s, error: @e', [
        '@t' => $last_run ? date('Y-m-d H:i:s', $last_run) : $this->t('never'),
        '@d' => $last_duration ?? '-',
        '@e' => $last_error ? $last_error : $this->t('none'),
      ]) . '</p>',
    ];

    // Analyze fieldset.
    $form['settings']['analyze'] = [
      '#type' => 'details',
      '#title' => $this->t('Analyze Settings'),
    ];

    $form['settings']['analyze']['enabled_analyze'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Analyze on cron'),
      '#default_value' => (bool) ($config->get('enabled_analyze') ?? FALSE),
    ];

    $form['settings']['analyze']['cron_frequency_analyze'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron frequency'),
      '#description' => $this->t('How often should the cron analyze the tables?'),
      '#options' => [
        3600 => $this->t('Hourly'),
        86400 => $this->t('Daily'),
        604800 => $this->t('Weekly'),
        2629746 => $this->t('Monthly'),
        7889238 => $this->t('Trimester (3 months)'),
        15778476 => $this->t('Semester (6 months)'),
        31556952 => $this->t('Yearly'),
      ],
      '#default_value' => $config->get('cron_frequency_analyze') ?? 86400,
      '#states' => [
        'visible' => [
          ":input[name='enabled_analyze']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['analyze']['button_actions'] = [
      '#type' => 'container',
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => [
          'class' => ['dboptimize-select-all'],
          'type' => 'button',
        ],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => [
          'class' => ['dboptimize-toggle'],
          'type' => 'button',
        ],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => [
          'class' => ['dboptimize-clear'],
          'type' => 'button',
        ],
      ],
      '#states' => [
        'visible' => [
          ":input[name='enabled_analyze']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['analyze']['cron_tables_analyze'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tables to analyze during cron'),
      '#description' => $this->t('Select the tables that should be analyzed. If none are selected, all database tables will be analyzed during cron execution.'),
      '#options' => $options,
      '#default_value' => $config->get('cron_tables_analyze') ?? [],
      '#states' => [
        'visible' => [
          ":input[name='enabled_analyze']" => ['checked' => TRUE],
        ],
      ],
    ];
    // Show last run info.
    $last_run = $this->state->get('dboptimize.last_run.analyze');
    $last_duration = $this->state->get('dboptimize.last_duration.analyze');
    $last_error = $this->state->get('dboptimize.last_error.analyze');
    $form['settings']['analyze']['status'] = [
      '#markup' => $this->t('<p>Last run: @t, duration: @d s, error: @e', [
        '@t' => $last_run ? date('Y-m-d H:i:s', $last_run) : $this->t('never'),
        '@d' => $last_duration ?? '-',
        '@e' => $last_error ? $last_error : $this->t('none'),
      ]) . '</p>',
    ];

    // Repair fieldset.
    $form['settings']['repair'] = [
      '#type' => 'details',
      '#title' => $this->t('Repair Settings'),
    ];

    $form['settings']['repair']['enabled_repair'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Repair on cron'),
      '#default_value' => (bool) ($config->get('enabled_repair') ?? FALSE),
    ];

    $form['settings']['repair']['cron_frequency_repair'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron frequency'),
      '#description' => $this->t('How often should the cron repair the tables?'),
      '#options' => [
        3600 => $this->t('Hourly'),
        86400 => $this->t('Daily'),
        604800 => $this->t('Weekly'),
        2629746 => $this->t('Monthly'),
        7889238 => $this->t('Trimester (3 months)'),
        15778476 => $this->t('Semester (6 months)'),
        31556952 => $this->t('Yearly'),
      ],
      '#default_value' => $config->get('cron_frequency_repair') ?? 604800,
      '#states' => [
        'visible' => [
          ":input[name='enabled_repair']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['repair']['button_actions'] = [
      '#type' => 'container',
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => [
          'class' => ['dboptimize-select-all'],
          'type' => 'button',
        ],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => [
          'class' => ['dboptimize-toggle'],
          'type' => 'button',
        ],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => [
          'class' => ['dboptimize-clear'],
          'type' => 'button',
        ],
      ],
      '#states' => [
        'visible' => [
          ":input[name='enabled_repair']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['repair']['cron_tables_repair'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tables to repair during cron'),
      '#description' => $this->t('Select the tables that should be repaired. If none are selected, all database tables will be repaired during cron execution.'),
      '#options' => $options,
      '#default_value' => $config->get('cron_tables_repair') ?? [],
      '#states' => [
        'visible' => [
          ":input[name='enabled_repair']" => ['checked' => TRUE],
        ],
      ],
    ];
    // Show last run info.
    $last_run = $this->state->get('dboptimize.last_run.repair');
    $last_duration = $this->state->get('dboptimize.last_duration.repair');
    $last_error = $this->state->get('dboptimize.last_error.repair');
    $form['settings']['repair']['status'] = [
      '#markup' => $this->t('<p>Last run: @t, duration: @d s, error: @e', [
        '@t' => $last_run ? date('Y-m-d H:i:s', $last_run) : $this->t('never'),
        '@d' => $last_duration ?? '-',
        '@e' => $last_error ? $last_error : $this->t('none'),
      ]) . '</p>',
    ];

    // Check fieldset.
    $form['settings']['check'] = [
      '#type' => 'details',
      '#title' => $this->t('Check Settings'),
    ];

    $form['settings']['check']['enabled_check'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable Check on cron'),
      '#default_value' => (bool) ($config->get('enabled_check') ?? FALSE),
    ];

    $form['settings']['check']['cron_frequency_check'] = [
      '#type' => 'select',
      '#title' => $this->t('Cron frequency'),
      '#description' => $this->t('How often should the cron check the tables?'),
      '#options' => [
        3600 => $this->t('Hourly'),
        86400 => $this->t('Daily'),
        604800 => $this->t('Weekly'),
        2629746 => $this->t('Monthly'),
        7889238 => $this->t('Trimester (3 months)'),
        15778476 => $this->t('Semester (6 months)'),
        31556952 => $this->t('Yearly'),
      ],
      '#default_value' => $config->get('cron_frequency_check') ?? 604800,
      '#states' => [
        'visible' => [
          ":input[name='enabled_check']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['check']['button_actions'] = [
      '#type' => 'container',
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => [
          'class' => ['dboptimize-select-all'],
          'type' => 'button',
        ],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => [
          'class' => ['dboptimize-toggle'],
          'type' => 'button',
        ],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => [
          'class' => ['dboptimize-clear'],
          'type' => 'button',
        ],
      ],
      '#states' => [
        'visible' => [
          ":input[name='enabled_check']" => ['checked' => TRUE],
        ],
      ],
    ];
    $form['settings']['check']['cron_tables_check'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Tables to check during cron'),
      '#description' => $this->t('Select the tables that should be checked. If none are selected, all database tables will be checked during cron execution.'),
      '#options' => $options,
      '#default_value' => $config->get('cron_tables_check') ?? [],
      '#states' => [
        'visible' => [
          ":input[name='enabled_check']" => ['checked' => TRUE],
        ],
      ],
    ];
    // Show last run info.
    $last_run = $this->state->get('dboptimize.last_run.check');
    $last_duration = $this->state->get('dboptimize.last_duration.check');
    $last_error = $this->state->get('dboptimize.last_error.check');
    $form['settings']['check']['status'] = [
      '#markup' => $this->t('<p>Last run: @t, duration: @d s, error: @e', [
        '@t' => $last_run ? date('Y-m-d H:i:s', $last_run) : $this->t('never'),
        '@d' => $last_duration ?? '-',
        '@e' => $last_error ? $last_error : $this->t('none'),
      ]) . '</p>',
    ];

    // Attach JS behavior for per-fieldset actions.
    $form['settings']['#attached']['library'][] = 'dboptimize/settings_buttons';

    // Global actions: reset statistics for operations.
    $form['settings']['actions']['reset_stats'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset operation statistics'),
      '#limit_validation_errors' => [],
      '#submit' => ['::resetOperationStates'],
      '#attributes' => ['class' => ['button', 'button--warning']],
      '#weight' => 100,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * Submit handler to reset stored operation metrics in state.
   */
  public function resetOperationStates(array &$form, FormStateInterface $form_state) {
    $ops = ['optimize', 'analyze', 'repair', 'check'];
    foreach ($ops as $op) {
      $this->state->delete("dboptimize.last_run.{$op}");
      $this->state->delete("dboptimize.last_duration.{$op}");
      $this->state->delete("dboptimize.last_error.{$op}");
    }
    $this->messenger()->addStatus($this->t('Operation statistics reset for all operations.'));
    $form_state->setRebuild(TRUE);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Validate start_after fields if provided.
    $groups = ['optimize', 'analyze', 'repair', 'check'];
    foreach ($groups as $group) {
      $parent = $form_state->getValue($group);
      if (!is_array($parent)) {
        continue;
      }
      $value = $parent['start_after'] ?? '';
      if (!empty($value)) {
        $ts = @strtotime($value);
        if ($ts === FALSE || $ts === NULL) {
          $form_state->setErrorByName($group . '][start_after', $this->t('Invalid date/time for @field. Use format YYYY-MM-DD HH:MM', ['@field' => $group]));
        }
        else {
          // Normalize and store back into the parent array.
          $parent['start_after'] = date('Y-m-d H:i', $ts);
          $form_state->setValue($group, $parent);
        }
      }
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Optimize.
    $selected_tables = array_filter($form_state->getValue('cron_tables'));
    $cron_frequency = $form_state->getValue('cron_frequency');
    // Analyze.
    $selected_tables_analyze = array_filter($form_state->getValue('cron_tables_analyze'));
    $cron_frequency_analyze = $form_state->getValue('cron_frequency_analyze');
    // Repair.
    $selected_tables_repair = array_filter($form_state->getValue('cron_tables_repair'));
    $cron_frequency_repair = $form_state->getValue('cron_frequency_repair');
    // Check.
    $selected_tables_check = array_filter($form_state->getValue('cron_tables_check'));
    $cron_frequency_check = $form_state->getValue('cron_frequency_check');

    // Read values directly from the form state to ensure accurate values.
    $enabled_optimize = (bool) $form_state->getValue('enabled_optimize');
    $enabled_analyze = (bool) $form_state->getValue('enabled_analyze');
    $enabled_repair = (bool) $form_state->getValue('enabled_repair');
    $enabled_check = (bool) $form_state->getValue('enabled_check');

    // Read global execution method.
    $execution_method = $form_state->getValue('execution_method') ?? 'cron';

    // Save enabled flags and execution method values.
    $this->config('dboptimize.settings')
      ->set('cron_tables', $selected_tables)
      ->set('cron_frequency', $cron_frequency)
      ->set('cron_tables_analyze', $selected_tables_analyze)
      ->set('cron_frequency_analyze', $cron_frequency_analyze)
      ->set('cron_tables_repair', $selected_tables_repair)
      ->set('cron_frequency_repair', $cron_frequency_repair)
      ->set('cron_tables_check', $selected_tables_check)
      ->set('cron_frequency_check', $cron_frequency_check)
      ->set('enabled_optimize', $enabled_optimize)
      ->set('enabled_analyze', $enabled_analyze)
      ->set('enabled_repair', $enabled_repair)
      ->set('enabled_check', $enabled_check)
      ->set('execution_method', $execution_method)
      ->save();

    parent::submitForm($form, $form_state);
  }

}
