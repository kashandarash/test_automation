<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * A user downloads a file.
 *
 * @AutomationTrigger(
 *   id = "social_automation_user_downloads_file",
 *   label = "A user downloads a file",
 *   category="User Trigger (create content)",
 *   event="file_download:file",
 *   trigger_type="user",
 *   context_definitions = {
 *     "file" = @ContextDefinition("entity",
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
class UserDownloadsFile extends AutomationTriggerBase {

}
