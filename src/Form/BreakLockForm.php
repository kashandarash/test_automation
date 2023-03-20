<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\social_automation\Ui\AutomationUiHandlerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Builds the form to break the lock of an edited workflowevent.
 */
class BreakLockForm extends ConfirmFormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The rendering service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $renderer;

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   */
  protected $automationUiHandler;

  /**
   * Constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RendererInterface $renderer) {
    $this->entityTypeManager = $entity_type_manager;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('renderer')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_automation_break_lock_confirm';
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Do you want to break the lock on %label?', ['%label' => $this->automationUiHandler->getComponentLabel()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    $locked = $this->automationUiHandler->getLockMetaData();
    $account = $this->entityTypeManager->getStorage('user')->load($locked->getOwnerId());
    $username = [
      '#theme' => 'username',
      '#account' => $account,
    ];
    return $this->t('By breaking this lock, any unsaved changes made by @user will be lost.', [
      '@user' => $this->renderer->render($username),
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->automationUiHandler->getBaseRouteUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Break lock');
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiHandlerInterface $social_automation_ui_handler = NULL) {
    $this->automationUiHandler = $social_automation_ui_handler;
    if (!$social_automation_ui_handler->isLocked()) {
      $form['message']['#markup'] = $this->t('There is no lock on %label to break.', ['%label' => $social_automation_ui_handler->getComponentLabel()]);
      return $form;
    }
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->automationUiHandler->clearTemporaryStorage();
    $form_state->setRedirectUrl($this->automationUiHandler->getBaseRouteUrl());
    $this->messenger()->addMessage($this->t('The lock has been broken and you may now edit this @component_type.', [
      '@component_type' => $this->automationUiHandler->getPluginDefinition()->component_type_label,
    ]));
  }

}
