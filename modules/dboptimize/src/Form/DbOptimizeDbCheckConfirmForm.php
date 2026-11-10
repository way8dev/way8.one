<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Confirmation form for checking database tables.
 */
class DbOptimizeDbCheckConfirmForm extends ConfirmFormBase {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $requestStack;

  public function __construct(RequestStack $request_stack) {
    $this->requestStack = $request_stack;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_db_check_confirm_form';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to CHECK the selected tables?');
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return Url::fromRoute('dboptimize.check_form');
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Confirm CHECK');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $tables = [];
    $request = $this->requestStack->getCurrentRequest();
    if ($request->query->has('tables')) {
      $tables = explode(',', $request->query->get('tables'));
    }

    $form['fieldsets'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Tables to check'),
      '#description' => $this->t('The following tables will be checked:'),
    ];

    $form['fieldsets']['tables'] = [
      '#type' => 'item',
      '#markup' => implode(', ', $tables),
    ];

    $form['fieldsets']['actions']['confirm'] = [
      '#type' => 'submit',
      '#value' => $this->t('Confirm and run check'),
      '#button_type' => 'primary',
    ];

    $form['fieldsets']['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => Url::fromRoute('dboptimize.check_form'),
      '#attributes' => ['class' => ['button']],
    ];

    // Store tables for submit.
    $form_state->set('tables', $tables);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $tables = $form_state->get('tables');
    $base_form_class = '\Drupal\dboptimize\Form\DbOptimizeCheckForm';
    if (class_exists($base_form_class)) {
      $base_form = new $base_form_class(\Drupal::service('database'), \Drupal::service('request_stack'));
      if (method_exists($base_form, 'runBatch')) {
        $base_form->runBatch($tables);
      }
    }
    // Set the origin for the results form back button.
    \Drupal::state()->set('dboptimize.results_origin', 'dboptimize.check_form');
    $form_state->setRedirect('dboptimize.results');
  }

}
