<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user starts following content.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_follows_content",
 *   label = "A user follows content",
 *   category="User Trigger (create content)",
 *   context_definitions = {
 *     "flagging" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   event="entity_insert:flagging",
 *   trigger_type="user",
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserFollowsContent extends AutomationTriggerBase {

}
