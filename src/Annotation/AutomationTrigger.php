<?php

namespace Drupal\social_automation\Annotation;

use Drupal\Component\Annotation\Plugin;
use Drupal\Component\Plugin\Definition\ContextAwarePluginDefinitionTrait;

/**
 * Defines a Automation trigger item annotation object.
 *
 * @see \Drupal\social_automation\Plugin\AutomationTriggerManager
 * @see plugin_api
 *
 * @Annotation
 */
class AutomationTrigger extends Plugin {

  use ContextAwarePluginDefinitionTrait;
  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The category the plugin is in.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $category;

  /**
   * The event that is triggered by this plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $event;

  /**
   * The trigger type (time/user).
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $trigger_type;

  /**
   * Wether this plugin is hidden in administrative overviews or not.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $hidden;

  /**
   * Wether this plugin requires conditions or not.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $requiresCondition;

}
