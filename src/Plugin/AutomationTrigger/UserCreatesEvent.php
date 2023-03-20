<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates an event.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_event",
 *   label = "A user creates a new event",
 *   category="User Trigger (create content)",
 *   event="entity_insert:node--event",
 *   trigger_type="user",
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity",
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
class UserCreatesEvent extends AutomationTriggerBase {

}
