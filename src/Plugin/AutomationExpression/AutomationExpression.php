<?php

namespace Drupal\social_automation\Plugin\AutomationExpression;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_automation\Context\ContextConfig;
use Drupal\social_automation\Context\ExecutionMetadataStateInterface;
use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\social_automation\Engine\ActionExpressionContainerInterface;
use Drupal\social_automation\Engine\ActionExpressionInterface;
use Drupal\social_automation\Engine\ConditionExpressionContainerInterface;
use Drupal\social_automation\Engine\ConditionExpressionInterface;
use Drupal\social_automation\Engine\ExpressionBase;
use Drupal\social_automation\Engine\ExpressionInterface;
use Drupal\social_automation\Engine\ExpressionManagerInterface;
use Drupal\social_automation\Engine\AutomationExpressionInterface;
use Drupal\social_automation\Exception\InvalidExpressionException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a workflowevent, executing actions when conditions are met.
 *
 * Actions added to a workflowevent can also be automation themselves,
 * so it is possible to nest several automation into one workflowevent.
 * This is the functionality of so called "workflowevent sets" in Drupal 7.
 *
 * @AutomationExpression(
 *   id = "automation_expression",
 *   label = @Translation("Workflow event"),
 *   form_class = "\Drupal\social_automation\Form\Expression\AutomationExpressionForm"
 * )
 */
class AutomationExpression extends ExpressionBase implements AutomationExpressionInterface, ContainerFactoryPluginInterface {

  /**
   * The automation expression plugin manager.
   *
   * @var \Drupal\social_automation\Engine\ExpressionManagerInterface
   */
  protected $expressionManager;

  /**
   * List of conditions that must be met before actions are executed.
   *
   * @var \Drupal\social_automation\Engine\ConditionExpressionContainerInterface
   */
  protected $conditions;

  /**
   * List of actions that get executed if the conditions are met.
   *
   * @var \Drupal\social_automation\Engine\ActionExpressionContainerInterface
   */
  protected $actions;

  /**
   * The automation debug logger channel.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $automationDebugLogger;

  /**
   * Constructs a new class instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\social_automation\Engine\ExpressionManagerInterface $expression_manager
   *   The automation expression plugin manager.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The Automation debug logger channel.
   */
  public function __construct(array $configuration, $plugin_id, array $plugin_definition, ExpressionManagerInterface $expression_manager, LoggerChannelInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $configuration += ['conditions' => [], 'actions' => []];
    // Per default the outer condition container of a workflowevent is
    // initialized as conjunction (AND), meaning that all conditions in it must
    // evaluate to TRUE to fire the actions.
    $this->conditions = $expression_manager->createInstance('social_automation_and', $configuration['conditions']);
    $this->conditions->setRoot($this->getRoot());
    $this->actions = $expression_manager->createInstance('automation_action_set', $configuration['actions']);
    $this->actions->setRoot($this->getRoot());
    $this->expressionManager = $expression_manager;
    $this->automationDebugLogger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition,
      $container->get('plugin.manager.automation_expression'),
      $container->get('logger.channel.social_automation_debug')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function executeWithState(ExecutionStateInterface $state) {
    // Evaluate the workflowevent's conditions.
    $this->automationDebugLogger->info('Evaluating conditions of workflowevent %label.', [
      '%label' => $this->getLabel(),
      'element' => $this,
    ]);
    if (!$this->conditions->isEmpty() && !$this->conditions->executeWithState($state)) {
      // Do not run the actions if the conditions are not met.
      return;
    }
    $this->automationDebugLogger->info('Workflow event %label fires.', [
      '%label' => $this->getLabel(),
      'element' => $this,
      'scope' => TRUE,
    ]);
    $this->actions->executeWithState($state);
    $this->automationDebugLogger->info('Workflow event %label has fired.', [
      '%label' => $this->getLabel(),
      'element' => $this,
      'scope' => FALSE,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function addCondition($condition_id, ContextConfig $config = NULL) {
    return $this->conditions->addCondition($condition_id, $config);
  }

  /**
   * {@inheritdoc}
   */
  public function getConditions() {
    return $this->conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function setConditions(ConditionExpressionContainerInterface $conditions) {
    $this->conditions = $conditions;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addAction($action_id, ContextConfig $config = NULL) {
    return $this->actions->addAction($action_id, $config);
  }

  /**
   * {@inheritdoc}
   */
  public function getActions() {
    return $this->actions;
  }

  /**
   * {@inheritdoc}
   */
  public function setActions(ActionExpressionContainerInterface $actions) {
    $this->actions = $actions;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addExpressionObject(ExpressionInterface $expression) {
    if ($expression instanceof ConditionExpressionInterface) {
      $this->conditions->addExpressionObject($expression);
    }
    elseif ($expression instanceof ActionExpressionInterface) {
      $this->actions->addExpressionObject($expression);
    }
    else {
      throw new InvalidExpressionException();
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function addExpression($plugin_id, ContextConfig $config = NULL) {
    return $this->addExpressionObject(
      $this->expressionManager->createInstance($plugin_id, $config ? $config->toArray() : [])
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    $configuration = parent::getConfiguration();
    // We need to update the configuration in case actions/conditions have been
    // added or changed.
    $configuration['conditions'] = $this->conditions->getConfiguration();
    $configuration['actions'] = $this->actions->getConfiguration();
    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator(): \Traversable {
    // Just pass up the actions for iterating over.
    return $this->actions->getIterator();
  }

  /**
   * {@inheritdoc}
   */
  public function getExpression($uuid) {
    $condition = $this->conditions->getExpression($uuid);
    if ($condition) {
      return $condition;
    }
    return $this->actions->getExpression($uuid);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteExpression($uuid) {
    $deleted = $this->conditions->deleteExpression($uuid);
    if (!$deleted) {
      $deleted = $this->actions->deleteExpression($uuid);
    }
    return $deleted;
  }

  /**
   * {@inheritdoc}
   */
  public function checkIntegrity(ExecutionMetadataStateInterface $metadata_state, $apply_assertions = TRUE) {
    $violation_list = $this->conditions->checkIntegrity($metadata_state, $apply_assertions);
    $violation_list->addAll($this->actions->checkIntegrity($metadata_state, $apply_assertions));
    return $violation_list;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareExecutionMetadataState(ExecutionMetadataStateInterface $metadata_state, ExpressionInterface $until = NULL, $apply_assertions = TRUE) {
    // @todo If the workflowevent is nested, we may not pass assertions to following
    // expressions as we do not know whether the workflowevent fires at all.
    // Should we clone the metadata state to ensure modifications stay local?
    $found = $this->conditions->prepareExecutionMetadataState($metadata_state, $until, $apply_assertions);
    if ($found) {
      return TRUE;
    }
    return $this->actions->prepareExecutionMetadataState($metadata_state, $until, $apply_assertions);
  }

  /**
   * PHP magic __clone function.
   */
  public function __clone() {
    $this->actions = clone $this->actions;
    $this->actions->setRoot($this->getRoot());
    $this->conditions = clone $this->conditions;
    $this->conditions->setRoot($this->getRoot());
  }

}
