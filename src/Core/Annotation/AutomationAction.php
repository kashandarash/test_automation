<?php

namespace Drupal\social_automation\Core\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Automation Action annotation object.
 *
 * Plugin Namespace: Plugin\AutomationAction.
 *
 * For a working example see:
 *   \Drupal\social_automation\Plugin\AutomationAction\BanIP
 *
 * @see \Drupal\social_automation\Core\AutomationActionInterface
 * @see \Drupal\social_automation\Core\AutomationActionManagerInterface
 * @see \Drupal\social_automation\Core\AutomationActionBase
 * @see plugin_api
 *
 * @Annotation
 */
class AutomationAction extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the action plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The category under which the action should be listed in the UI.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $category;

  /**
   * The permission required to access the configuration UI for this plugin.
   *
   * @var string[]
   *   Array of permission strings as declared in a *.permissions.yml file. If
   *   any one of these permissions apply for the relevant user, we allow
   *   access.
   */
  public $configure_permission;

  /**
   * An array of context definitions describing the context used by the plugin.
   *
   * Array keys are the names of the context variables and values are the
   * context definitions.
   *
   * @var \Drupal\Core\Annotation\ContextDefinition[]
   */
  public $context_definitions = [];

  /**
   * Defines the provided context_definitions of the action plugin.
   *
   * Array keys are the names of the context variables and values are the
   * context definitions.
   *
   * @var \Drupal\Core\Annotation\ContextDefinition[]
   */
  public $provides = [];

}
