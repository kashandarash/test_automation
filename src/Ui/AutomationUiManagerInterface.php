<?php

namespace Drupal\social_automation\Ui;

use Drupal\Component\Plugin\PluginManagerInterface;

/**
 * Interface for the 'social_automation_ui' plugin manager.
 *
 * AutomationUI plugins allow the definition of multiple AutomationUIs
 * instances, possibly being included in some other UI.
 */
interface AutomationUiManagerInterface extends PluginManagerInterface {

  /**
   * Creates a pre-configured instance of a plugin.
   *
   * @param string $plugin_id
   *   The ID of the plugin being instantiated.
   * @param array $configuration
   *   An array of configuration relevant to the plugin instance.
   *
   * @return \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   *   A fully configured plugin instance.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   *   If the instance cannot be created, such as if the ID is invalid.
   */
  public function createInstance($plugin_id, array $configuration = []);

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\social_automation\Ui\AutomationUiDefinition|null
   *   A plugin definition, or NULL if the plugin ID is invalid and
   *   $exception_on_invalid is FALSE.
   */
  public function getDefinition($plugin_id, $exception_on_invalid = TRUE);

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\social_automation\Ui\AutomationUiDefinition[]
   *   An array of plugin definitions (empty array if no definitions were
   *   found). Keys are plugin IDs.
   */
  public function getDefinitions();

}
