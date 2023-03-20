<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user leaves a group.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_leaves_group",
 *   label = "A user leaves any group",
 *   category="User Trigger (delete content)",
 *   event="entity_delete:group_content",
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
class UserLeavesGroup extends AutomationTriggerBase {

}
