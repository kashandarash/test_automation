<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user has not logged in for X days.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_has_not_logged_in",
 *   label = "User has not logged in for X days",
 *   category="Time Trigger",
 *   event="cron",
 *   trigger_type="time",
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:user", label = @Translation("User"))
 *   },
 *   hidden="TRUE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class UserHasNotLoggedInForWeeks extends AutomationTriggerBase {

}
