<?php

namespace Drupal\social_automation\Plugin\AutomationExpression;

use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\social_automation\Engine\ConditionExpressionContainer;

/**
 * Evaluates a group of conditions with a logical OR.
 *
 * @AutomationExpression(
 *   id = "social_automation_or",
 *   label = @Translation("Condition set (OR)")
 * )
 */
class OrExpression extends ConditionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  public function evaluate(ExecutionStateInterface $state) {
    // Use the iterator to ensure the conditions are sorted.
    foreach ($this as $condition) {
      /** @var \Drupal\social_automation\Engine\ExpressionInterface $condition */
      if ($condition->executeWithState($state)) {
        $this->automationDebugLogger->info('%label evaluated to %result.', [
          '%label' => $this->getLabel(),
          '%result' => 'TRUE',
        ]);
        return TRUE;
      }
    }
    $this->automationDebugLogger->info('%label evaluated to %result.', [
      '%label' => $this->getLabel(),
      '%result' => 'FALSE',
    ]);
    // An empty OR should return TRUE. Otherwise, if all conditions evaluate
    // to FALSE we return FALSE.
    return empty($this->conditions);
  }

  /**
   * {@inheritdoc}
   */
  protected function allowsMetadataAssertions() {
    // We cannot guarantee child expressions are executed, thus we cannot allow
    // metadata assertions.
    return FALSE;
  }

}
