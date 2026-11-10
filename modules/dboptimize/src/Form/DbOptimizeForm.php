<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Database\Connection;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Provides a form for selecting and optimizing database tables.
 */
class DbOptimizeForm extends FormBase {

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  /**
   * Constructs a new \Drupal\dboptimize\Form\DbOptimizeForm object.
   *
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   The request stack.
   */
  public function __construct(Connection $database, RequestStack $request_stack) {
    $this->database = $database;
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('database'),
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $header = [
      'select' => ['data' => 'Select', 'class' => ['checkbox']],
      'name' => [
        'data' => 'Table name',
        'field' => 'Name',
        'sort' => 'asc',
      ],
      'rows' => [
        'data' => 'Rows',
        'field' => 'Rows',
      ],
      'size' => [
        'data' => 'Size (MB)',
        'field' => 'size_calculated',
      ],
      'free' => [
        'data' => 'Free space (MB)',
        'field' => 'Data_free',
      ],
    ];

    // Get current sort parameters from URL.
    $request = $this->requestStack->getCurrentRequest();
    $sort_field = $request->query->get('order') ?? 'Name';
    $sort_direction = $request->query->get('sort') ?? 'asc';

    // Map header labels to field names for proper sorting.
    $label_to_field_mapping = [
      'Table name' => 'Name',
      'Rows' => 'Rows',
      'Size (MB)' => 'size_calculated',
      'Free space (MB)' => 'Data_free',
    ];

    // Map header keys to field names as fallback.
    $key_to_field_mapping = [
      'name' => 'Name',
      'rows' => 'Rows',
      'size' => 'size_calculated',
      'free' => 'Data_free',
    ];

    // First try to map by header label, then by key, otherwise use as-is.
    if (isset($label_to_field_mapping[$sort_field])) {
      $sort_field = $label_to_field_mapping[$sort_field];
    }
    elseif (isset($key_to_field_mapping[$sort_field])) {
      $sort_field = $key_to_field_mapping[$sort_field];
    }

    $tables = $this->database->query('SHOW TABLE STATUS')->fetchAll();

    // Convert to array for sorting.
    $tables_array = [];
    foreach ($tables as $table) {
      if (!is_object($table)) {
        continue;
      }

      $size_mb = round(($table->Data_length + $table->Index_length) / 1048576, 2);
      $free_mb = round($table->Data_free / 1048576, 2);

      $tables_array[] = [
        'object' => $table,
        'Name' => $table->Name,
        'Rows' => $table->Rows,
        'size_calculated' => $size_mb,
        'Data_free' => $table->Data_free,
      ];
    }

    // Apply sorting.
    usort($tables_array, function ($a, $b) use ($sort_field, $sort_direction) {
      $val_a = $a[$sort_field] ?? 0;
      $val_b = $b[$sort_field] ?? 0;

      // Handle numeric sorting for rows, size, and free space.
      if (in_array($sort_field, ['Rows', 'size_calculated', 'Data_free'])) {
        $result = $val_a <=> $val_b;
      }
      else {
        $result = strcasecmp($val_a, $val_b);
      }

      return $sort_direction === 'desc' ? -$result : $result;
    });

    $search = $form_state->getValue('search', '');

    $description = $this->t('<strong>OPTIMIZE Command</strong>: Defragments the data file and reclaims unused space to improve table performance.');
    $form['form'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Optimize Database Tables'),
      '#description' => $description,
    ];
    $form['form']['search'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Search'),
      '#default_value' => $search,
      '#attributes' => [
        'placeholder' => $this->t('Table name'),
        'class' => ['dboptimize-filter'],
      ],
    ];

    $form['form']['button_actions'] = [
      'select_all' => [
        '#type' => 'button',
        '#value' => $this->t('Select All'),
        '#attributes' => ['class' => ['dboptimize-select-all']],
      ],
      'toggle' => [
        '#type' => 'button',
        '#value' => $this->t('Toggle'),
        '#attributes' => ['class' => ['dboptimize-toggle']],
      ],
      'clear' => [
        '#type' => 'button',
        '#value' => $this->t('Clear All'),
        '#attributes' => ['class' => ['dboptimize-clear']],
      ],
    ];

    $form['actions_submit_header'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button', 'button--primary', 'dboptimize-submit'],
      ],
      '#value' => $this->t('Optimize selected tables'),
    ];

    $form['tables'] = [
      '#type' => 'tableselect',
      '#header' => $header,
      '#options' => [],
      '#multiple' => TRUE,
      '#attributes' => ['class' => ['dboptimize-table']],
      '#sticky' => TRUE,
      '#empty' => $this->t('No tables found.'),
    ];

    foreach ($tables_array as $table_data) {
      $table = $table_data['object'];

      if (!empty($search) && stripos($table->Name, $search) === FALSE) {
        continue;
      }

      $size_mb = $table_data['size_calculated'];
      $free_mb = round($table->Data_free / 1048576, 2);

      $form['tables']['#options'][$table->Name] = [
        'select' => '',
        'name' => $table->Name,
        'rows' => number_format($table->Rows),
        'size' => $size_mb . ' MB',
        'free' => $free_mb . ' MB',
      ];
    }

    $form['actions_submit'] = [
      '#type' => 'submit',
      '#attributes' => [
        'class' => ['button', 'button--primary', 'dboptimize-submit'],
      ],
      '#value' => $this->t('Optimize selected tables'),
    ];

    $form['#attached']['library'][] = 'dboptimize/dboptimize';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $selected_tables = array_filter($form_state->getValue('tables'));

    // Use dependency injection for session.
    $this->requestStack->getCurrentRequest()->getSession()->set('selected_tables', array_keys($selected_tables));
    $form_state->setRedirect('dboptimize.confirm');
  }

}
