<?php

namespace Drupal\social_automation\Engine;

use Drupal\social_automation\Context\ContextConfig;

/**
 * Contains action expressions.
 */
interface ActionExpressionContainerInterface extends ActionExpressionInterface, ExpressionContainerInterface {

  /**
   * Creates an action expression and adds it to the container.
   *
   * @param string $action_id
   *   The action plugin id.
   * @param \Drupal\social_automation\Context\ContextConfig $config
   *   (optional) The configuration for the specified plugin.
   *
   * @return $this
   */
  public function addAction($action_id, ContextConfig $config = NULL);

}
