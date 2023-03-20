<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user creates a book.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_creates_book",
 *   label = "A user creates a new book",
 *   category="User Trigger (create content)",
 *   event="entity_insert:node--book",
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
class UserCreatesBook extends AutomationTriggerBase {

}
