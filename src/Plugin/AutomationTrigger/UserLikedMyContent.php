<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a like.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_liked_my_content",
 *   label = "A user liked my content",
 *   category="User Trigger (create content)",
 *   event="custom",
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
class UserLikedMyContent extends AutomationTriggerBase {

}
