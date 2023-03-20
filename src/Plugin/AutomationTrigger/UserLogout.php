<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * UserLogin.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_logout",
 *   label = "A user logs out",
 *   category="User Trigger (security)",
 *   event="user_logout",
 *   trigger_type="user",
 *   context_definitions = {
 *     "account" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user who logged out."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserLogout extends AutomationTriggerBase {

}
