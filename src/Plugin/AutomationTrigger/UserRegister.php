<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user registers.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_register",
 *   label = "A user registers or is registered",
 *   category="User Trigger (security)",
 *   event="entity_insert:user",
 *   trigger_type="user",
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user who did serch."),
 *       assignment_restriction = "selector"
 *     ),
 *   },
 *   hidden="FALSE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserRegister extends AutomationTriggerBase {

}
