<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user requests to join a group.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_request_to_join_group",
 *   label = "A user request to join any group",
 *   category="User Trigger (create content)",
 *   event="entity_insert:group_content",
 *   trigger_type="user",
 *   context_definitions = {
 *     "group_content" = @ContextDefinition("entity",
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
class UserRequestToJoinGroup extends AutomationTriggerBase {

}
