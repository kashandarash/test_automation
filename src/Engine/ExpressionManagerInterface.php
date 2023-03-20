<?php

namespace Drupal\social_automation\Engine;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\social_automation\Context\ContextConfig;

/**
 * Defines an interface for the expression plugin manager.
 */
interface ExpressionManagerInterface extends PluginManagerInterface {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\social_automation\Engine\ExpressionInterface
   *   A fully configured plugin instance.
   */
  public function createInstance($plugin_id, array $configuration = []);

  /**
   * Creates a new workflowevent.
   *
   * @param \Drupal\social_automation\Context\ContextConfig $configuration
   *   (optional) The context configuration used to create the plugin instance.
   *
   * @return \Drupal\social_automation\Engine\AutomationExpressionInterface
   *   The created workflowevent.
   */
  public function createWorkflowEvent(ContextConfig $configuration = NULL);

  /**
   * Creates a new action set.
   *
   * @param \Drupal\social_automation\Context\ContextConfig $configuration
   *   (optional) The context configuration used to create the plugin instance.
   *
   * @return \Drupal\social_automation\Plugin\AutomationExpression\ActionSetExpression
   *   The created action set.
   */
  public function createActionSet(ContextConfig $configuration = NULL);

  /**
   * Creates a new action expression.
   *
   * @param string $id
   *   The action plugin id.
   * @param \Drupal\social_automation\Context\ContextConfig $configuration
   *   (optional) The context configuration used to create the plugin instance.
   *
   * @return \Drupal\social_automation\Engine\ActionExpressionInterface
   *   The created action expression.
   */
  public function createAction($id, ContextConfig $configuration = NULL);

  /**
   * Creates a new condition expression.
   *
   * @param string $id
   *   The condition plugin id.
   * @param \Drupal\social_automation\Context\ContextConfig $configuration
   *   (optional) The context configuration used to create the plugin instance.
   *
   * @return \Drupal\social_automation\Engine\ConditionExpressionInterface
   *   The created condition expression.
   */
  public function createCondition($id, ContextConfig $configuration = NULL);

  /**
   * Creates a new 'and' condition container.
   *
   * @return \Drupal\social_automation\Engine\ConditionExpressionContainerInterface
   *   The created 'and' condition container.
   */
  public function createAnd();

  /**
   * Creates a new 'or' condition container.
   *
   * @return \Drupal\social_automation\Engine\ConditionExpressionContainerInterface
   *   The created 'or' condition container.
   */
  public function createOr();

}
