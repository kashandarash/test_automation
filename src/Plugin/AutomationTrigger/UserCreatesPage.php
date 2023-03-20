<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a page.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_page",
 *   label = "A user creates a new page",
 *   category="User Trigger (create content)",
 *   event="entity_insert:node--page",
 *   trigger_type="user",
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity",
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
class UserCreatesPage extends AutomationTriggerBase {

}
