<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user logs in.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_login",
 *   label = "A user logs in",
 *   category="User Trigger (security)",
 *   event="user_login",
 *   trigger_type="user",
 *   context_definitions = {
 *     "account" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserLogin extends AutomationTriggerBase {

}
