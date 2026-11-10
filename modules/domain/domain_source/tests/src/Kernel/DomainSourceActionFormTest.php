<?php

namespace Drupal\Tests\domain_source\Kernel;

use Drupal\Core\Action\ConfigurableActionBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormState;
use Drupal\Core\Form\FormStateInterface;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\domain\Traits\DomainTestTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;

/**
 * Tests the configuration form of the domain source actions.
 *
 * The set domain source action stores the id of the one domain it assigns as
 * the source: its configuration schema types domain_id as a string, and
 * execute() and calculateDependencies() read it as one id. The form must
 * therefore preselect that saved id when it is rebuilt, and hand exactly one id
 * back when it is submitted. A form element that submits a list, such as
 * checkboxes, stores an array of every option instead, and running the action
 * from a bulk form then fails on the array offset.
 *
 * The test is itself the form, so the action's configuration form is submitted
 * through the form builder the way a bulk operations form submits it.
 *
 * @group domain_source
 */
#[Group('domain_source')]
#[RunTestsInSeparateProcesses]
class DomainSourceActionFormTest extends KernelTestBase implements FormInterface {

  use DomainTestTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'domain',
    'domain_source',
    'field',
    'node',
    'user',
    'system',
  ];

  /**
   * The action whose configuration form is under test.
   *
   * @var \Drupal\Core\Action\ConfigurableActionBase
   */
  protected ConfigurableActionBase $action;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->installEntitySchema('domain');
    $this->installEntitySchema('user');
    $this->installEntitySchema('node');
    $this->installConfig(['domain', 'domain_source', 'system']);
    $this->setBaseHostname();
    $this->domainCreateTestDomains(2);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'domain_source_action_form_test';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    return $this->action->buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->action->submitConfigurationForm($form, $form_state);
  }

  /**
   * Builds the set domain source action with the given configuration.
   *
   * @param array $configuration
   *   The action configuration.
   */
  protected function createSetAction(array $configuration = []): void {
    $this->action = $this->container->get('plugin.manager.action')
      ->createInstance('domain_source_set_action', $configuration);
  }

  /**
   * The saved domain source is preselected when the action form is rebuilt.
   */
  public function testSavedDomainSourceIsPreselected(): void {
    $this->createSetAction(['domain_id' => 'example_com']);

    $form = $this->action->buildConfigurationForm([], new FormState());

    $this->assertSame('example_com', $form['domain_id']['#default_value']);
  }

  /**
   * Submitting the form stores the one chosen domain id.
   */
  public function testSubmittedDomainSourceIsOneId(): void {
    $this->createSetAction();
    $form_state = new FormState();
    $form_state->setValues(['domain_id' => 'one_example_com']);

    $this->container->get('form_builder')->submitForm($this, $form_state);

    $this->assertSame([], $form_state->getErrors());
    $this->assertSame('one_example_com', $this->action->getConfiguration()['domain_id']);
    $this->assertSame(['config' => ['domain.record.one_example_com']], $this->action->calculateDependencies());
  }

}
