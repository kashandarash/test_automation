<?php

namespace Drupal\social_automation\Form\Expression;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Engine\AutomationExpressionInterface;

/**
 * Form view structure for workflowevent expressions.
 *
 * @see \Drupal\social_automation\Plugin\AutomationExpression\AutomationExpression
 */
class AutomationExpressionForm implements ExpressionFormInterface {
  use ExpressionFormTrait;

  /**
   * The workflowevent expression object this form is for.
   *
   * @var \Drupal\social_automation\Engine\AutomationExpressionInterface
   */
  protected $workflowevent;

  /**
   * Creates a new object of this class.
   */
  public function __construct(AutomationExpressionInterface $workflowevent) {
    $this->workflowevent = $workflowevent;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $conditions_form_handler = $this->workflowevent->getConditions()->getFormHandler();
    $form = $conditions_form_handler->form($form, $form_state);

    $actions_form_handler = $this->workflowevent->getActions()->getFormHandler();
    $form = $actions_form_handler->form($form, $form_state);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->workflowevent->getConditions()->getFormHandler()->submitForm($form, $form_state);
    $this->workflowevent->getActions()->getFormHandler()->submitForm($form, $form_state);
  }

}
