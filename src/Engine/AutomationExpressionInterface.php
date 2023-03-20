<?php

namespace Drupal\social_automation\Engine;

use Drupal\social_automation\Context\ContextConfig;

/**
 * Defines a workflowevent.
 */
interface AutomationExpressionInterface extends ExpressionContainerInterface, ActionExpressionInterface {

  /**
   * Creates a condition expression and adds it to the container.
   *
   * @param string $condition_id
   *   The condition plugin id.
   * @param \Drupal\social_automation\Context\ContextConfig $config
   *   (optional) The configuration for the specified plugin.
   *
   * @return \Drupal\social_automation\Core\AutomationConditionInterface
   *   The created condition.
   */
  public function addCondition($condition_id, ContextConfig $config = NULL);

  /**
   * Returns the conditions container of this workflowevent.
   *
   * @return \Drupal\social_automation\Engine\ConditionExpressionContainerInterface
   *   The condition container of this workflowevent.
   */
  public function getConditions();

  /**
   * Sets the condition container.
   *
   * @param \Drupal\social_automation\Engine\ConditionExpressionContainerInterface $conditions
   *   The condition container to set.
   *
   * @return $this
   */
  public function setConditions(ConditionExpressionContainerInterface $conditions);

  /**
   * Creates an action expression and adds it to the container.
   *
   * @param string $action_id
   *   The action plugin id.
   * @param \Drupal\social_automation\Context\ContextConfig $config
   *   (optional) The configuration for the specified plugin.
   *
   * @return $this
   */
  public function addAction($action_id, ContextConfig $config = NULL);

  /**
   * Returns the actions of this workflowevent.
   *
   * @return \Drupal\social_automation\Engine\ActionExpressionContainerInterface
   *   The action container of this workflowevent.
   */
  public function getActions();

  /**
   * Sets the action container.
   *
   * @param \Drupal\social_automation\Engine\ActionExpressionContainerInterface $actions
   *   The action container to set.
   *
   * @return $this
   */
  public function setActions(ActionExpressionContainerInterface $actions);

}
