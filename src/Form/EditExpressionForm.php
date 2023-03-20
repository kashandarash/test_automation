<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Ui\AutomationUiHandlerInterface;
use Drupal\social_automation\Engine\AutomationComponent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * UI form to edit an expression like a condition or action in a workflowevent.
 */
class EditExpressionForm extends FormBase {

  /**
   * The edited component.
   *
   * @var \Drupal\social_automation\Engine\AutomationComponent
   */
  protected $component;

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   */
  protected $automationUiHandler;

  /**
   * The UUID of the edited expression in the workflowevent.
   *
   * @var string
   */
  protected $uuid;

  /**
   * Gets the currently edited expression from the given component.
   *
   * @param \Drupal\social_automation\Engine\AutomationComponent $component
   *   The component from which to get the expression.
   *
   * @return \Drupal\social_automation\Engine\ExpressionInterface|null
   *   The expression object.
   */
  protected function getEditedExpression(AutomationComponent $component) {
    $automation_expression = $component->getExpression();
    return $automation_expression->getExpression($this->uuid);
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiHandlerInterface $social_automation_ui_handler = NULL, $uuid = NULL) {
    $this->automationUiHandler = $social_automation_ui_handler;
    $this->component = is_object($form_state->get('component')) ? $form_state->get('component') : $this->automationUiHandler->getComponent();
    $this->uuid = $form_state->get('uuid') ?: $uuid;

    // During form rebuilds, keep track of changes using form state.
    $form_state->set('social_automation_ui_handler', $this->automationUiHandler);
    $form_state->set('component', $this->component);
    $form_state->set('uuid', $this->uuid);

    $expression = $this->getEditedExpression($this->component);

    if (!$expression) {
      throw new NotFoundHttpException();
    }
    $form_handler = $expression->getFormHandler();
    $form = $form_handler->form($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'automation_expression_edit';
  }

  /**
   * Builds an updated component object based upon the submitted form values.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return \Drupal\social_automation\Engine\AutomationComponent
   *   The updated component.
   */
  protected function buildComponent(array $form, FormStateInterface $form_state) {
    $component = clone $this->component;

    // In order to update the whole component we need to invoke the submission
    // handler of the expression form. That way the expression gets changed
    // accordingly.
    $expression = $this->getEditedExpression($component);
    $form_handler = $expression->getFormHandler();
    $form_handler->submitForm($form, $form_state);
    return $component;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Ensure the object properties are initialized, see
    // https://www.drupal.org/node/2669032.
    $this->automationUiHandler = $form_state->get('social_automation_ui_handler');
    $this->component = is_object($form_state->get('component')) ? $form_state->get('component') : $this->automationUiHandler->getComponent();
    $this->uuid = $form_state->get('uuid');

    $this->automationUiHandler->validateLock($form, $form_state);

    // @todo This ignores ExpressionFormInterface::validateForm().
    $component = $this->buildComponent($form, $form_state);
    $violations = $component->checkIntegrity();

    // Only show the violations caused by the edited expression.
    foreach ($violations->getFor($this->uuid) as $violation) {
      $form_state->setError($form, $violation->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->component = $this->buildComponent($form, $form_state);
    $this->automationUiHandler->updateComponent($this->component);
    $form_state->setRedirectUrl($this->automationUiHandler->getBaseRouteUrl());
  }

  /**
   * Provides the page title on the form.
   */
  public function getTitle(AutomationUiHandlerInterface $social_automation_ui_handler, $uuid) {
    $this->uuid = $uuid;
    $expression = $this->getEditedExpression($social_automation_ui_handler->getComponent());
    return $this->t('Edit @expression', ['@expression' => $expression->getLabel()]);
  }

}
