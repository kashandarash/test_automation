<?php

namespace Drupal\social_automation\Plugin\AutomationExpression;

use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\social_automation\Engine\ConditionExpressionContainer;

/**
 * Evaluates a group of conditions with a logical AND.
 *
 * @AutomationExpression(
 *   id = "social_automation_and",
 *   label = @Translation("Condition set (AND)"),
 *   form_class = "\Drupal\social_automation\Form\Expression\ConditionContainerForm"
 * )
 */
class AndExpression extends ConditionExpressionContainer {

  /**
   * Returns whether there is a configured condition.
   *
   * @todo Remove this once we added the API to access configured conditions.
   *
   * @return bool
   *   TRUE if there are no conditions, FALSE otherwise.
   */
  public function isEmpty() {
    return empty($this->conditions);
  }

  /**
   * {@inheritdoc}
   */
  public function evaluate(ExecutionStateInterface $state) {
    // Use the iterator to ensure the conditions are sorted.
    foreach ($this as $condition) {
      /** @var \Drupal\social_automation\Engine\ExpressionInterface $condition */
      if (!$condition->executeWithState($state)) {
        $this->automationDebugLogger->info('%label evaluated to %result.', [
          '%label' => $this->getLabel(),
          '%result' => 'FALSE',
        ]);
        return FALSE;
      }
    }
    $this->automationDebugLogger->info('%label evaluated to %result.', [
      '%label' => $this->getLabel(),
      '%result' => 'TRUE',
    ]);
    // An empty AND should return FALSE. Otherwise, if all conditions evaluate
    // to TRUE we return TRUE.
    return !empty($this->conditions);
  }

  /**
   * {@inheritdoc}
   */
  protected function allowsMetadataAssertions() {
    // If the AND is not negated, all child-expressions must be executed - thus
    // assertions can be added it.
    return !$this->isNegated();
  }

}
