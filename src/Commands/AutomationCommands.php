<?php

namespace Drupal\social_automation\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drupal\Core\Config\CachedStorage;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Serialization\Yaml;
use Drupal\social_automation\Plugin\AutomationTriggerManager;
use Drush\Commands\DrushCommands;

/**
 * Drush 9+ commands for the Automation module.
 */
class AutomationCommands extends DrushCommands {

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The config storage service.
   *
   * @var \Drupal\Core\Config\CachedStorage
   */
  protected $configStorage;

  /**
   * The automation event manager.
   *
   * @var \Drupal\social_automation\Plugin\AutomationTriggerManager
   */
  protected $triggerManager;

  /**
   * AutomationCommands constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   * @param \Drupal\Core\Config\CachedStorage $config_storage
   *   The config storage service.
   * @param \Drupal\social_automation\Plugin\AutomationTriggerManager $trigger_manager
   *   The automation trigger manager.
   */
  public function __construct(ConfigFactoryInterface $config_factory, CachedStorage $config_storage, AutomationTriggerManager $trigger_manager) {
    parent::__construct();
    $this->configFactory = $config_factory;
    $this->configStorage = $config_storage;
    $this->triggerManager = $trigger_manager;
  }

  /**
   * Lists all the active and inactive automation for your site.
   *
   * @param string $type
   *   (optional) Either 'workflowevent' or 'component'. Any other value (or
   *   no value) will list both Reaction Automation and Automation Components.
   * @param array $options
   *   (optional) The options.
   *
   * @command automation:list
   * @aliases rlst,automation-list
   *
   * @usage drush automation:list
   *   Lists both Reaction Automation and Automation Components.
   * @usage drush automation:list --type=component
   *   Lists only Automation Components.
   * @usage drush automation:list --fields=machine-name
   *   Lists just the machine names.
   * @usage drush automation:list --fields=machine-name --pipe
   *   Outputs machine names in a format suitable for piping.
   *
   * @table-style default
   * @field-labels
   *   machine-name: Workflow event
   *   label: Label
   *   event: Event
   *   active: Active
   * @default-fields machine-name,label,event,active
   *
   * @return \Consolidation\OutputFormatters\StructuredData\RowsOfFields
   *   The data.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function listAll($type = '', array $options = [
    'format' => 'table',
    'fields' => '',
  ]) {
    // Type is 'workflowevent', or 'component'. Any other value (or no value)
    // will list both Reaction Automation and Automation Components.
    switch ($type) {
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
    foreach ($types as $item) {
      $automation = $this->configFactory->listAll('social_automation.' . $item);
      // Loop over configuration entities for this $item.
      foreach ($automation as $config) {
        $workflowevent = $this->configFactory->get($config);
        if (!empty($workflowevent->get('id')) && !empty($workflowevent->get('label'))) {
          $events = [];
          $active = '';
          // Components don't have events and can't be enabled/disabled.
          if ($item == 'reaction') {
            foreach ($workflowevent->get('events') as $event) {
              $plugin = $this->automationEventManager->getDefinition($event['event_name']);
              $events[] = (string) $plugin['label'];
            }
            $active = $workflowevent->get('status') ? dt('Enabled') : dt('Disabled');
          }
          $rows[(string) $workflowevent->get('id')] = [
            'machine-name' => (string) $workflowevent->get('id'),
            'label' => (string) $workflowevent->get('label'),
            'event' => implode(', ', $events),
            'active' => (string) $active,
          ];
        }
      }
    }

    return new RowsOfFields($rows);
  }

  /**
   * Enables a Workflow event on your site.
   *
   * @param string $workflowevent
   *   Workflow event name (machine name) to enable.
   *
   * @command automation:enable
   * @aliases renb,automation-enable
   *
   * @usage drush automation:enable test_workflowevent
   *   Enables the workflowevent with machine name 'test_workflowevent'.
   *
   * @throws \Exception
   */
  public function enable($workflowevent) {
    // The $workflowevent argument must be a Workflow event.
    if ($this->configStorage->exists('social_automation.reaction.' . $workflowevent)) {
      $config = $this->configFactory->getEditable('social_automation.reaction.' . $workflowevent);
    }
    else {
      throw new \Exception(dt('Could not find a Workflow event named @name', ['@name' => $workflowevent]));
    }

    if (!$config->get('status')) {
      $config->set('status', TRUE);
      $config->save();
      $this->logger->success(dt('The workflowevent @name has been enabled.', ['@name' => $workflowevent]));
    }
    else {
      $this->logger->warning(dt('The workflowevent @name is already enabled', ['@name' => $workflowevent]));
    }
  }

  /**
   * Disables a Workflow event on your site.
   *
   * @param string $workflowevent
   *   Workflow event name (machine name) to disable.
   *
   * @command automation:disable
   * @aliases rdis,automation-disable
   *
   * @usage drush automation:disable test_workflowevent
   *   Disables the workflowevent with machine name 'test_workflowevent'.
   *
   * @throws \Exception
   */
  public function disable($workflowevent) {
    // The $workflowevent argument must be a Workflow event.
    if ($this->configStorage->exists('social_automation.reaction.' . $workflowevent)) {
      $config = $this->configFactory->getEditable('social_automation.reaction.' . $workflowevent);
    }
    else {
      throw new \Exception(dt('Could not find a Workflow event named @name', ['@name' => $workflowevent]));
    }

    if ($config->get('status')) {
      $config->set('status', FALSE);
      $config->save();
      $this->logger->success(dt('The workflowevent @name has been disabled.', ['@name' => $workflowevent]));
    }
    else {
      $this->logger->warning(dt('The workflowevent @name is already disabled', ['@name' => $workflowevent]));
    }
  }

  /**
   * Deletes a workflowevent on your site.
   *
   * @param string $workflowevent
   *   Workflow event name (machine id) to delete.
   *
   * @command automation:delete
   * @aliases rdel,automation-delete
   *
   * @usage drush automation:delete test_workflowevent
   *   Permanently deletes the workflowevent
   *   with machine name 'test_workflowevent'.
   *
   * @throws \Exception
   */
  public function delete($workflowevent) {
    // The workflowevent argument could refer to a
    // Workflow event or a Automation Component.
    if ($this->configStorage->exists('social_automation.reaction.' . $workflowevent)) {
      $config = $this->configFactory->getEditable('social_automation.reaction.' . $workflowevent);
    }
    elseif ($this->configStorage->exists('social_automation.component.' . $workflowevent)) {
      $config = $this->configFactory->getEditable('social_automation.component.' . $workflowevent);
    }
    else {
      throw new \Exception(dt('Could not find a Workflow event or a Automation Component named @name', ['@name' => $workflowevent]));
    }

    if ($this->confirm(dt('Are you sure you want to delete the workflowevent named "@name"? This action cannot be undone.', ['@name' => $workflowevent]))) {
      $config->delete();
      $this->logger->success(dt('The workflowevent @name has been deleted.', ['@name' => $workflowevent]));
    }

  }

  /**
   * Exports a single workflowevent configuration, in YAML format.
   *
   * @param string $workflowevent
   *   Workflow event name (machine id) to export.
   *
   * @command automation:export
   * @aliases rexp,automation-export
   *
   * @codingStandardsIgnoreStart
   * @usage drush automation:export test_workflowevent > social_automation.reaction.test_workflowevent.yml
   *   Exports the Workflow event with machine name 'test_workflowevent' and saves it in a .yml file.
   * @usage drush automation:list --pipe --type=component | xargs -I{}  sh -c "drush automation:export '{}' > 'social_automation.component.{}.yml'"
   *   Exports all Automation Components into individual YAML files.
   * @codingStandardsIgnoreEnd
   *
   * @throws \Exception
   */
  public function export($workflowevent) {
    // The workflowevent argument could refer to a
    // Workflow event or a Automation Component.
    $config = $this->configStorage->read('social_automation.reaction.' . $workflowevent);
    if (empty($config)) {
      $config = $this->configStorage->read('social_automation.component.' . $workflowevent);
      if (empty($config)) {
        throw new \Exception(dt('Could not find a Workflow event or a Automation Component named @name', ['@name' => $workflowevent]));
      }
    }

    $this->output->write(Yaml::encode($config), FALSE);
    $this->logger->success(dt('The workflowevent @name has been exported.', ['@name' => $workflowevent]));
  }

  /**
   * Reverts a workflowevent to its original state on your site.
   *
   * @param string $workflowevent
   *   Workflow event name (machine id) to revert.
   *
   * @command automation:revert
   * @aliases rrev,automation-revert
   *
   * @usage drush automation:revert test_workflowevent
   *   Restores the module-provided Workflow event with machine id
   *   'test_workflowevent' to its original state. If the Workflow event
   *   hasn't been customized on the site, this has no effect.
   *
   * @throws \Exception
   */
  public function revert($workflowevent) {
    // @todo Implement this function.
    // The workflowevent argument could refer to a
    // Workflow event or a Automation Component.
    $config = $this->configStorage->read('social_automation.reaction.' . $workflowevent);
    if (empty($config)) {
      $config = $this->configStorage->read('social_automation.component.' . $workflowevent);
      if (empty($config)) {
        throw new \Exception(dt('Could not find a Workflow event or a Automation Component named @name', ['@name' => $workflowevent]));
      }
    }

    if (($workflowevent->status & ENTITY_OVERRIDDEN) == ENTITY_OVERRIDDEN) {
      if ($this->confirm(dt('Are you sure you want to revert the workflowevent named "@name"? This action cannot be undone.', ['@name' => $workflowevent]))) {
        // $config->delete();
        $this->logger->success(dt('The workflowevent @name has been reverted to its default state.', ['@name' => $workflowevent]));
      }
    }
    else {
      $this->logger->warning(dt('The workflowevent "@name" has not been overridden and can\'t be reverted.', ['@name' => $workflowevent]));
    }
  }

}
