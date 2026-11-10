<?php

namespace Drupal\dboptimize\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Displays the results of database optimization operations.
 */
class DbOptimizeResultsForm extends FormBase {

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
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'dboptimize_results_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $results = $this->state->get('dboptimize.results', '<p>No results found.</p>');
    $origin_route = $this->state->get('dboptimize.results_origin');

    $form['first_back'] = [
      '#type' => 'link',
      '#title' => $this->t('← Back'),
      '#url' => Url::fromRoute($origin_route),
      '#attributes' => ['class' => ['button']],
    ];

    $form['results'] = [
      '#type' => 'markup',
      '#markup' => $results,
    ];

    $form['actions']['back'] = [
      '#type' => 'link',
      '#title' => $this->t('← Back'),
      '#url' => Url::fromRoute($origin_route),
      '#attributes' => ['class' => ['button']],
    ];

    $this->state->delete('dboptimize.results');

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {}

}
