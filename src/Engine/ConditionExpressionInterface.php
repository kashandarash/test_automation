<?php

namespace Drupal\social_automation\Engine;

/**
 * Defines the interface for Automation expressions to be used as conditions.
 */
interface ConditionExpressionInterface extends ExpressionInterface {

  /**
   * Negates the result after evaluating this condition.
   *
   * @param bool $negate
   *   TRUE to indicate that the condition should be negated, FALSE otherwise.
   *
   * @return $this
   */
  public function negate($negate = TRUE);

  /**
   * Determines whether condition result will be negated.
   *
   * @return bool
   *   Whether the condition result will be negated.
   */
  public function isNegated();

}
