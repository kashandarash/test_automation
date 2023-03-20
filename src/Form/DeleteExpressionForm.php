<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Ui\AutomationUiHandlerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Removes an expression from a workflowevent.
 */
class DeleteExpressionForm extends ConfirmFormBase {

  /**
   * The UUID of the expression in the workflowevent.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   */
  protected $automationUiHandler;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'social_automation_delete_expression';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiHandlerInterface $social_automation_ui_handler = NULL, $uuid = NULL) {
    $this->automationUiHandler = $social_automation_ui_handler;
    $this->uuid = $uuid;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    $automation_expression = $this->automationUiHandler->getComponent()->getExpression();
    $expression_inside = $automation_expression->getExpression($this->uuid);
    if (!$expression_inside) {
      throw new NotFoundHttpException();
    }

    return $this->t('Are you sure you want to delete %title from %workflowevent?', [
      '%title' => $expression_inside->getLabel(),
      '%workflowevent' => $this->automationUiHandler->getComponentLabel(),
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
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $component = $this->automationUiHandler->getComponent();
    $component->getExpression()->deleteExpression($this->uuid);
    $this->automationUiHandler->updateComponent($component);
    $form_state->setRedirectUrl($this->getCancelUrl());
  }

}
