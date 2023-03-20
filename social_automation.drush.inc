<?php

/**
 * @file
 * Social Automation module integration with Drush 8 and earlier.
 */

use Drupal\Core\Serialization\Yaml;
use Drupal\social_automation\Event\AutomationDrushEvent;
use Symfony\Component\EventDispatcher\Event;

/**
 * Implements hook_drush_init().
 */
function social_automation_drush_init() {
  if (\Drupal::hasService('event_dispatcher')) {
    \Drupal::service('event_dispatcher')->dispatch(AutomationDrushEvent::DRUSHINIT, new Event());
  }
}

/**
 * Implements hook_drush_command().
 */
function social_automation_drush_command() {
  $items = [];
  $items['automation-list'] = [
    'aliases' => ['automation'],
    'description' => dt('Lists all the active and inactive automation for your site.'),
    'drupal dependencies' => ['social_automation'],
    'core' => ['8+'],
    'options' => [
      'type' => [
        'description' => dt('Restricts output to either "workflowevent" or "component". Default is to list both.'),
        'example-value' => 'workflowevent, component',
      ],
    ],
    'examples' => [
      'drush automation-list' => dt('Lists both Reaction Automation and Automation Components.'),
      'drush automation-list --type=component' => dt('Lists only Automation Components.'),
      'drush automation-list --fields=workflowevent' => dt('Lists just the machine names.'),
      'drush automation-list --pipe' => dt('Outputs machine names in a format suitable for piping.'),
    ],
    'outputformat' => [
      'default' => 'table',
      'pipe-format' => 'list',
      'field-labels' => [
        'workflowevent' => (string) dt('Workflow event'),
        'label' => (string) dt('Label'),
        'event' => (string) dt('Event'),
        'active' => (string) dt('Active'),
        /* 'status' => (string) dt('Status'), */
      ],
      'output-data-type' => 'format-table',
    ],
  ];
  $items['automation-enable'] = [
    'aliases' => ['re'],
    'description' => dt('Enables a workflowevent on your site.'),
    'arguments' => [
      'workflowevent' => dt('Workflow event name (machine id) to enable.'),
    ],
    'required-arguments' => TRUE,
    'drupal dependencies' => ['social_automation'],
    'core' => ['8+'],
    'examples' => [
      'drush automation-enable test_workflowevent' => dt('Enables the workflowevent with machine name "test_workflowevent".'),
    ],
  ];
  $items['automation-disable'] = [
    'aliases' => ['rd'],
    'description' => dt('Disables a workflowevent on your site.'),
    'arguments' => [
      'workflowevent' => dt('Workflow event name (machine id) to disable.'),
    ],
    'required-arguments' => TRUE,
    'drupal dependencies' => ['social_automation'],
    'core' => ['8+'],
    'examples' => [
      'drush automation-disable test_workflowevent' => dt('Disables the workflowevent with machine name "test_workflowevent".'),
    ],
  ];
  $items['automation-delete'] = [
    'description' => dt('Deletes a workflowevent on your site.'),
    'arguments' => [
      'workflowevent' => dt('Workflow event name (machine id) to delete.'),
    ],
    'required-arguments' => TRUE,
    'drupal dependencies' => ['social_automation'],
    'core' => ['8+'],
    'examples' => [
      'drush automation-delete test_workflowevent' => dt('Permanently deletes the workflowevent with machine name "test_workflowevent".'),
    ],
  ];
  $items['automation-export'] = [
    'description' => dt('Exports a Workflow event in YAML format.'),
    'arguments' => [
      'workflowevent' => dt('Workflow event name (machine id) to export.'),
    ],
    'required-arguments' => TRUE,
    'examples' => [
      'drush automation-export test_workflowevent > social_automation.reaction.test_workflowevent.yml' => dt('Exports the Workflow event with machine name "test_workflowevent" and saves it in a .yml file.'),
      'drush --pipe --type=component automation-list | xargs -I{}  sh -c "drush automation-export \'{}\' > \'social_automation.component.{}.yml\'"' => dt('Exports all Automation Components into individual YAML files.'),
    ],
    'drupal dependencies' => ['social_automation'],
    'core' => ['8+'],
  ];
  /*
   * @codingStandardsIgnoreStart
   *
   * @todo Not yet implemented.
   *
   * $items['automation-revert'] = [
   *   'description' => dt('Reverts a workflowevent to its original state on your site.'),
   *   'arguments' => [
   *     'workflowevent' => dt('Workflow event name (machine id) to revert.'),
   *   ],
   *   'required-arguments' => TRUE,
   *   'drupal dependencies' => ['social_automation'],
   *   'core' => ['8+'],
   *   'examples' => [
   *     'drush automation-revert test_workflowevent' => dt('Restores a module-provided Workflow event to its original state. If the Workflow event hasn\'t been customized on the site, this has no effect.'),
   *   ],
   * ];
   * @codingStandardsIgnoreEnd
   */

  return $items;
}

/**
 * Implements hook_drush_help().
 */
function social_automation_drush_help($section) {
  switch ($section) {
    case 'meta:automation:title':
      return dt('Automation commands');

    case 'meta:automation:summary':
      return dt('Developer-specific commands for the Automation module.');

    case 'drush:automation-list':
      return dt('Lists all the automation on your site.');

    case 'drush:automation-enable':
      return dt('Enables/activates a Workflow event on your site.');

    case 'drush:automation-disable':
      return dt('Disables/deactivates a Workflow event on your site.');

    /*
     * @codingStandardsIgnoreStart
     *
     * @todo Not yet implemented.
     *
     * case 'drush:automation-revert':
     *   return dt('Reverts a module-provided workflowevent to its original state on your site.');
     * @codingStandardsIgnoreEnd
     */

    case 'drush:automation-delete':
      return dt('Deletes a workflowevent on your site.');

    case 'drush:automation-export':
      return dt('Exports a workflowevent.');
  }
}

/**
 * Gets a list of all social_automation.
 */
function drush_social_automation_list() {
  // Type is 'workflowevent', or 'component'. Any other value (or no value) will
  // list both Reaction Automation and Automation Components.
  switch (drush_get_option('type')) {
    case 'workflowevent':
      $types = ['reaction'];
      break;

    case 'component':
      $types = ['component'];
      break;

    default:
      $types = ['reaction', 'component'];
      break;
  }

  // Loop over type option.
  $rows = [];
  foreach ($types as $type) {
    $automation = \Drupal::configFactory()->listAll('social_automation.' . $type);
    $event_manager = \Drupal::service('plugin.manager.social_automation_event');

    // Loop over configuration entities for this $type.
    foreach ($automation as $config) {
      $workflowevent = \Drupal::configFactory()->get($config);
      if (!empty($workflowevent->get('id')) && !empty($workflowevent->get('label'))) {
        $events = [];
        $active = '';
        // Components don't have events and can't be enabled/disabled.
        if ($type == 'reaction') {
          foreach ($workflowevent->get('events') as $event) {
            $plugin = $event_manager->getDefinition($event['event_name']);
            $events[] = (string) $plugin['label'];
          }
          $active = $workflowevent->get('status') ? dt('Enabled') : dt('Disabled');
        }
        $rows[(string) $workflowevent->get('id')] = [
          'workflowevent' => (string) $workflowevent->get('id'),
          'label' => (string) $workflowevent->get('label'),
          'event' => implode(', ', $events),
          'active' => (string) $active,
          /*
           * 'status' => what goes here ?
           *
           * @todo Need to figure out how to determine the value for the
           * 'status' column. That is, whether the workflowevent is defined
           * by a module ('Default'), or is defined by a module then
           * modified in a site-specific way ('Overridden'), or is a custom
           * workflowevent built for this site ('Custom').
           * Maybe $workflowevent->has Overrides() tells us if this is Custom?
           */
        ];
      }
    }
  }

  return $rows;
}

/**
 * Enables a Workflow event on the site.
 */
function drush_social_automation_enable($workflowevent) {
  // The $workflowevent argument must be a Workflow event.
  if (\Drupal::service('config.storage')->exists('social_automation.reaction.' . $workflowevent)) {
    $config = \Drupal::configFactory()->getEditable('social_automation.reaction.' . $workflowevent);
  }
  else {
    return drush_set_error('', dt('Could not find a Workflow event named "@name".', ['@name' => $workflowevent]));
  }

  if (!$config->get('status')) {
    $config->set('status', TRUE);
    $config->save();
    drush_log(dt('The workflowevent "@name" has been enabled.', ['@name' => $workflowevent]), 'success');
  }
  else {
    drush_log(dt('The workflowevent "@name" is already enabled.', ['@name' => $workflowevent]), 'warning');
  }
}

/**
 * Disables a Workflow event on the site.
 */
function drush_social_automation_disable($workflowevent) {
  // The $workflowevent argument must be a Workflow event.
  if (\Drupal::service('config.storage')->exists('social_automation.reaction.' . $workflowevent)) {
    $config = \Drupal::configFactory()->getEditable('social_automation.reaction.' . $workflowevent);
  }
  else {
    return drush_set_error('', dt('Could not find a Workflow event named "@name".', ['@name' => $workflowevent]));
  }

  if ($config->get('status')) {
    $config->set('status', FALSE);
    $config->save();
    drush_log(dt('The workflowevent "@name" has been disabled.', ['@name' => $workflowevent]), 'success');
  }
  else {
    drush_log(dt('The workflowevent "@name" is already disabled.', ['@name' => $workflowevent]), 'warning');
  }
}

/**
 * Deletes a workflowevent on the site.
 */
function drush_social_automation_delete($workflowevent) {
  // The event argument could refer to an event or a component.
  if (\Drupal::service('config.storage')->exists('social_automation.reaction.' . $workflowevent)) {
    $config = \Drupal::configFactory()->getEditable('social_automation.reaction.' . $workflowevent);
  }
  elseif (\Drupal::service('config.storage')->exists('social_automation.component.' . $workflowevent)) {
    $config = \Drupal::configFactory()->getEditable('social_automation.component.' . $workflowevent);
  }
  else {
    return drush_set_error('', dt('Could not find a Workflow event or a Automation Component named "@name".', ['@name' => $workflowevent]));
  }

  if (drush_confirm(dt('Are you sure you want to delete the workflowevent named "@name"? This action cannot be undone.', ['@name' => $workflowevent]))) {
    $config->delete();
    drush_log(dt('The workflowevent "@name" has been deleted.', ['@name' => $workflowevent]), 'success');
  }
  else {
    drush_user_abort();
  }
}

/**
 * Exports a single workflowevent.
 */
function drush_social_automation_export($workflowevent) {
  // The event argument could refer to an event or a component.
  $config = \Drupal::service('config.storage')->read('social_automation.reaction.' . $workflowevent);
  if (empty($config)) {
    $config = \Drupal::service('config.storage')->read('social_automation.component.' . $workflowevent);
    if (empty($config)) {
      return drush_set_error('', dt('Could not find a Workflow event or a Automation Component named "@name".', ['@name' => $workflowevent]));
    }
  }

  drush_print(Yaml::encode($config), 0, NULL, FALSE);
  drush_log(dt('The workflowevent "@name" has been exported.', ['@name' => $workflowevent]), 'success');
}
