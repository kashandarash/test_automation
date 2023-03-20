<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a post.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_post",
 *   label = "A user creates a new post",
 *   category="User Trigger (create content)",
 *   context_definitions = {
 *     "post" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   event="entity_insert:post",
 *   trigger_type="user",
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserCreatesPost extends AutomationTriggerBase {

}
