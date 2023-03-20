<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Ui\AutomationUiHandlerInterface;
use Drupal\social_automation\Engine\ExpressionContainerInterface;
use Drupal\social_automation\Engine\ExpressionManagerInterface;
use Drupal\social_automation\Engine\AutomationComponent;
use Drupal\social_automation\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * UI form to add an expression like a condition or action to a workflowevent.
 */
class AddExpressionForm extends EditExpressionForm {

  /**
   * The Automation expression manager to get expression plugins.
   *
   * @var \Drupal\social_automation\Engine\ExpressionManagerInterface
   */
  protected $expressionManager;

  /**
   * The expression ID that is added, example: 'automation_action'.
   *
   * @var string
   */
  protected $expressionId;

  /**
   * Creates a new object of this class.
   */
  public function __construct(ExpressionManagerInterface $expression_manager) {
    $this->expressionManager = $expression_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('plugin.manager.automation_expression'));
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiHandlerInterface $social_automation_ui_handler = NULL, $uuid = NULL, $expression_id = NULL) {
    $this->expressionId = $expression_id;
    $this->uuid = $uuid;

    // When initially adding the expression, we have to initialize the object
    // and add the expression.
    if (!$this->uuid) {
      // Before we add our edited expression to the component's expression,
      // we clone it such that we do not change the source component until
      // the form has been successfully submitted.
      $component = clone $social_automation_ui_handler->getComponent();
      $this->uuid = $this->getEditedExpression($component)->getUuid();
      $form_state->set('component', $component);
      $form_state->set('uuid', $this->uuid);
    }

    return parent::buildForm($form, $form_state, $social_automation_ui_handler, $this->uuid);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditedExpression(AutomationComponent $component) {
    $component_expression = $component->getExpression();
    if (!$component_expression instanceof ExpressionContainerInterface) {
      throw new LogicException('Cannot add expression to expression of type ' . $component_expression->getPluginId());
    }
    if ($this->uuid && $expression = $component_expression->getExpression($this->uuid)) {
      return $expression;
    }
    else {
      $expression = $this->expressionManager->createInstance($this->expressionId);
      $automation_expression = $component->getExpression();
      $automation_expression->addExpressionObject($expression);
      return $expression;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $form_state->setRedirectUrl($this->automationUiHandler->getBaseRouteUrl());
  }

  /**
   * Provides the page title on the form.
   */
  public function getTitle(AutomationUiHandlerInterface $social_automation_ui_handler, $expression_id) {
    $this->expressionId = $expression_id;
    $expression = $this->expressionManager->createInstance($this->expressionId);
    return $this->t('Add @expression', ['@expression' => $expression->getLabel()]);
  }

}
