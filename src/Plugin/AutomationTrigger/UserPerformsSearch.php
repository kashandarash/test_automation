<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user performs a search.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_performs_search",
 *   label = "A user performs a search",
 *   category="User Trigger (search)",
 *   event="search",
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
class UserPerformsSearch extends AutomationTriggerBase {

}
