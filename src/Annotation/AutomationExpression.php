<?php

namespace Drupal\social_automation\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines the AutomationExpression annotation class.
 *
 * @Annotation
 */
class AutomationExpression extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The human-readable name of the automation plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * The class name of the form for displaying/editing this expression.
   *
   * @var string
   */
  public $form_class;

}
