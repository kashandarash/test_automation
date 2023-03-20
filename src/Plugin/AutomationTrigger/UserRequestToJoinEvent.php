<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user requests to enroll in an event.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_request_to_join_event",
 *   label = "A user request to join any event",
 *   category="User Trigger (create content)",
 *   event="entity_insert:event_enrollment",
 *   trigger_type="user",
 *   context_definitions = {
 *     "event_enrollment" = @ContextDefinition("entity",
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
class UserRequestToJoinEvent extends AutomationTriggerBase {

}
