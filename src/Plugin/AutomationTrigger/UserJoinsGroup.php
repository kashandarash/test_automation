<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user joins a group.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_joins_group",
 *   label = "A user joins any group",
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
class UserJoinsGroup extends AutomationTriggerBase {

}
