<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a like.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_like",
 *   label = "A user creates a new like",
 *   category="User Trigger (create content)",
 *   event="entity_insert:vote",
 *   trigger_type="user",
 *   context_definitions = {
 *     "vote" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserCreatesLike extends AutomationTriggerBase {

}
