<?php

namespace Drupal\social_automation\Engine;

use Drupal\social_automation\Context\ContextConfig;

/**
 * Contains condition expressions.
 */
interface ConditionExpressionContainerInterface extends ConditionExpressionInterface, ExpressionContainerInterface {

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

}
