<?php

namespace Drupal\social_automation\Plugin\AutomationExpression;

use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\social_automation\Engine\ActionExpressionContainer;

/**
 * Holds a set of actions and executes all of them.
 *
 * @AutomationExpression(
 *   id = "automation_action_set",
 *   label = @Translation("Action set"),
 *   form_class = "\Drupal\social_automation\Form\Expression\ActionContainerForm"
 * )
 */
class ActionSetExpression extends ActionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  protected function allowsMetadataAssertions() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function executeWithState(ExecutionStateInterface $state) {
    // Use the iterator to ensure the actions are sorted.
    foreach ($this as $action) {
      /** @var \Drupal\social_automation\Engine\ExpressionInterface $action */
      $action->executeWithState($state);
    }
  }

}
