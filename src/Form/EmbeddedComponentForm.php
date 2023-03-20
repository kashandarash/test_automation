<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Ui\AutomationUiHandlerInterface;

/**
 * Components form, ready to be embedded in some other form.
 *
 * Note, that there is no SubformInterface or such in core (yet), thus we
 * implement FormInterface instead.
 */
class EmbeddedComponentForm implements FormInterface {

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   */
  protected $automationUiHandler;

  /**
   * Constructs the object.
   *
   * @param \Drupal\social_automation\Ui\AutomationUiHandlerInterface $social_automation_ui_handler
   *   The UI handler of the edited component.
   */
  public function __construct(AutomationUiHandlerInterface $social_automation_ui_handler) {
    $this->automationUiHandler = $social_automation_ui_handler;
  }

  /**
   * Gets the form handler for the component's expression.
   *
   * @return \Drupal\social_automation\Form\Expression\ExpressionFormInterface|null
   *   The form handling object if there is one, NULL otherwise.
   */
  protected function getFormHandler() {
    return $this->automationUiHandler
      ->getComponent()
      ->getExpression()
      ->getFormHandler();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_automation_embedded_component_' . $this->automationUiHandler->getPluginId();
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['locked'] = $this->automationUiHandler->addLockInformation();
    return $this->getFormHandler()->form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->automationUiHandler->validateLock($form, $form_state);
    $this->getFormHandler()->validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->getFormHandler()->submitForm($form, $form_state);
  }

}
