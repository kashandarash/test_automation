<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a comment.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_comment",
 *   label = "A user creates a new comment",
 *   category="User Trigger (create content)",
 *   context_definitions = {
 *     "comment" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   event="entity_insert:comment",
 *   trigger_type="user",
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserCreatesComment extends AutomationTriggerBase {

}
