<?php

namespace Drupal\domain_config_ui\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\domain_config\Config\DomainConfigFactoryOverrideInterface;
use Drupal\domain_config_ui\Controller\DomainConfigUIController;
use Drupal\domain_config_ui\DomainConfigUIManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Form for deleting domain-specific configuration.
 */
class DeleteForm extends FormBase {

  /**
   * The entity storage.
   *
   * @var \Drupal\domain\DomainStorageInterface
   */
  protected $domainStorage;

  public function __construct(
    RequestStack $requestStack,
    EntityTypeManagerInterface $entityTypeManager,
    protected DomainConfigFactoryOverrideInterface $domainConfigFactoryOverride,
    protected DomainConfigUIManagerInterface $domainConfigUiManager,
  ) {
    $this->requestStack = $requestStack;
    $this->domainStorage = $entityTypeManager->getStorage('domain');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('entity_type.manager'),
      $container->get('domain.config_factory_override'),
      $container->get('domain_config_ui.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'domain_config_ui_delete';
  }

  /**
   * Build configuration form with metadata and values.
   */
  public function buildForm(array $form, FormStateInterface $form_state, $domain_id = NULL, $config_names = NULL) {
    if (empty($domain_id) || empty($config_names)) {
      $url = Url::fromRoute('domain_config_ui.list');
      return new RedirectResponse($url->toString());
    }

    $domain = $this->domainStorage->load($domain_id);
    $config_names_array = explode(',', $config_names);

    $configs = [];
    foreach ($config_names_array as $config_name) {
      $configs[$config_name] = $this->domainConfigFactoryOverride->getOverride($domain_id, $config_name)->getRawData();
    }

    $domain_label = $domain->label();
    $is_single = count($config_names_array) === 1;

    if ($is_single) {
      $config_name = reset($config_names_array);
      $help = $this->t('Delete the %config override for %domain?', [
        '%config' => $config_name,
        '%domain' => $domain_label,
      ]);
    }
    else {
      $help = $this->t('Select the overrides to delete for %domain.', ['%domain' => $domain_label]);
    }
    $form['help'] = [
      '#type' => 'item',
      '#markup' => $help,
      '#prefix' => '<p>',
      '#suffix' => '</p>',
    ];

    if ($is_single) {
      // A single override leaves nothing to choose, so skip the checkbox and
      // just carry the name through to the submit handler.
      $form['config_names'] = [
        '#type' => 'value',
        '#value' => $config_names_array,
      ];
    }
    else {
      // One checkbox per configuration name, all checked by default. The
      // default action therefore still removes everything the trigger
      // requested, but the user can keep individual overrides instead of
      // deleting the whole set at once. A configuration object such as
      // system.site can be shared by several forms, so its checkbox lets
      // the user avoid wiping values another form set on the same object.
      // See https://www.drupal.org/project/domain/issues/3593113
      $options = [];
      foreach ($config_names_array as $config_name) {
        $options[$config_name] = $config_name;
      }
      $form['config_names'] = [
        '#type' => 'checkboxes',
        '#title' => $this->t('Configuration overrides to delete'),
        '#description' => $this->t('Deleting an override removes all of its keys and the associated translations.'),
        '#options' => $options,
        '#default_value' => $config_names_array,
      ];
    }

    // Show the stored values, open by default so the affected configuration
    // is visible without an extra click.
    $form['review'] = [
      '#type' => 'details',
      '#title' => $this->t('Review settings'),
      '#open' => TRUE,
    ];
    if ($is_single) {
      // A single override needs no per-config grouping; the help line above
      // already names it, so show its values directly.
      $form['review']['values'] = [
        '#markup' => $this->reviewValues($configs[$config_name]),
      ];
    }
    else {
      // With several configurations, give each its own collapsible section,
      // expanded by default so every override (and every non-overridden
      // config) is visible at a glance; the user can collapse as needed.
      foreach ($config_names_array as $config_name) {
        $form['review'][$config_name] = [
          '#type' => 'details',
          '#title' => $config_name,
          '#open' => TRUE,
          'values' => [
            '#markup' => $this->reviewValues($configs[$config_name]),
          ],
        ];
      }
    }
    $form['domain_id'] = ['#type' => 'value', '#value' => $domain_id];
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Delete configuration'),
      '#button_type' => 'primary',
    ];
    $form['actions']['cancel'] = [
      '#type' => 'link',
      '#title' => $this->t('Cancel'),
      '#url' => $this->getCancelUrl(),
      '#attributes' => [
        'class' => [
          'button',
        ],
      ],
    ];
    return $form;
  }

  /**
   * Builds the review markup for one configuration's stored override.
   *
   * @param array $data
   *   The raw override data for the configuration, possibly empty.
   *
   * @return \Drupal\Component\Render\MarkupInterface|string
   *   A note when there is no override, otherwise the printed values.
   */
  protected function reviewValues(array $data) {
    return empty($data)
      ? $this->t('Not overridden for this domain.')
      : DomainConfigUIController::printArray($data);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (empty(array_filter($form_state->getValue('config_names')))) {
      $form_state->setErrorByName(
        'config_names',
        $this->t('Select at least one configuration override to delete.')
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $domain_id = $form_state->getValue('domain_id');
    // The checkboxes element returns the selected names as truthy values and
    // the unselected ones as 0; the single-override value element returns a
    // plain list. array_values(array_filter()) yields the kept names in both
    // cases.
    $config_names = array_values(array_filter($form_state->getValue('config_names')));
    $config_names_str = implode(', ', $config_names);
    $message = $this->t(
      'Overridden configuration %label for domain %domain has been deleted with all its translations.',
      ['%label' => $config_names_str, '%domain' => $domain_id]
    );
    $this->messenger()->addMessage($message);
    $this->logger('domain_config')->notice($message);
    foreach ($config_names as $config_name) {
      $this->domainConfigUiManager->deleteConfigurationOverridesForDomain(
        $domain_id, $config_name, $this->isRemove()
      );
    }
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  protected function getCancelUrl() {
    $query = $this->requestStack->getCurrentRequest()->query;
    $destination = $query->get('destination');
    if (empty($destination)) {
      return new Url('domain_config_ui.list');
    }
    else {
      return Url::fromUserInput($destination);
    }
  }

  /**
   * Get the remove parameter.
   */
  protected function isRemove() {
    $query = $this->requestStack->getCurrentRequest()->query;
    return $query->has('remove');
  }

}
