<?php

namespace Drupal\social_automation\Plugin\AutomationExpression;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\social_automation\Context\DataProcessorManager;
use Drupal\social_automation\Context\ExecutionMetadataStateInterface;
use Drupal\social_automation\Context\ExecutionStateInterface;
use Drupal\social_automation\Core\AutomationActionManagerInterface;
use Drupal\social_automation\Engine\ActionExpressionInterface;
use Drupal\social_automation\Engine\ExpressionBase;
use Drupal\social_automation\Engine\ExpressionInterface;
use Drupal\social_automation\Context\ContextHandlerIntegrityTrait;
use Drupal\social_automation\Engine\IntegrityViolationList;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an executable action expression.
 *
 * This plugin is used to wrap action plugins and is responsible to setup all
 * the context necessary, instantiate the action plugin and to execute it.
 *
 * @AutomationExpression(
 *   id = "automation_action",
 *   label = @Translation("Action"),
 *   form_class = "\Drupal\social_automation\Form\Expression\ActionForm"
 * )
 */
class ActionExpression extends ExpressionBase implements ContainerFactoryPluginInterface, ActionExpressionInterface {
  use ContextHandlerIntegrityTrait;

  /**
   * The action manager used to instantiate the action plugin.
   *
   * @var \Drupal\social_automation\Core\AutomationActionManagerInterface
   */
  protected $actionManager;

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
   *   Contains the following entries:
   *   - action_id: The action plugin ID.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\social_automation\Core\AutomationActionManagerInterface $action_manager
   *   The Automation action manager.
   * @param \Drupal\social_automation\Context\DataProcessorManager $processor_manager
   *   The data processor plugin manager.
   * @param \Drupal\Core\Logger\LoggerChannelInterface $logger
   *   The Automation debug logger channel.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AutomationActionManagerInterface $action_manager, DataProcessorManager $processor_manager, LoggerChannelInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->actionManager = $action_manager;
    $this->processorManager = $processor_manager;
    $this->automationDebugLogger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.automation_action'),
      $container->get('plugin.manager.social_automation_data_processor'),
      $container->get('logger.channel.social_automation_debug')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    // If the plugin id has been set already, keep it if not specified.
    if (isset($this->configuration['action_id'])) {
      $configuration += [
        'action_id' => $this->configuration['action_id'],
      ];
    }
    return parent::setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function executeWithState(ExecutionStateInterface $state) {
    $this->automationDebugLogger->info('Evaluating the action %name.', [
      '%name' => $this->getLabel(),
      'element' => $this,
    ]);
    $action = $this->actionManager->createInstance($this->configuration['action_id']);

    $this->prepareContext($action, $state);
    $action->execute();

    $auto_saves = $action->autoSaveContext();
    foreach ($auto_saves as $context_name) {
      // Mark parameter contexts for auto saving in the Automation state.
      $state->saveChangesLater($this->configuration['context_mapping'][$context_name]);
    }

    // Now that the action has been executed it can provide additional
    // context which we will have to pass back in the evaluation state.
    $this->addProvidedContext($action, $state);
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    if (!empty($this->configuration['action_id'])) {
      $definition = $this->actionManager->getDefinition($this->configuration['action_id']);
      return $definition['label'];
    }
    return parent::getLabel();
  }

  /**
   * {@inheritdoc}
   */
  public function getFormHandler() {
    if (isset($this->pluginDefinition['form_class'])) {
      $class_name = $this->pluginDefinition['form_class'];
      return new $class_name($this, $this->actionManager);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function checkIntegrity(ExecutionMetadataStateInterface $metadata_state, $apply_assertions = TRUE) {
    $violation_list = new IntegrityViolationList();
    if (empty($this->configuration['action_id'])) {
      $violation_list->addViolationWithMessage($this->t('Action plugin ID is missing'), $this->getUuid());
      return $violation_list;
    }
    if (!$this->actionManager->hasDefinition($this->configuration['action_id'])) {
      $violation_list->addViolationWithMessage($this->t('Action plugin %plugin_id does not exist', [
        '%plugin_id' => $this->configuration['action_id'],
      ]), $this->getUuid());
      return $violation_list;
    }

    $action = $this->actionManager->createInstance($this->configuration['action_id']);

    // Prepare and refine the context before checking integrity, such that any
    // context definition changes are respected while checking.
    $this->prepareContextWithMetadata($action, $metadata_state);
    $result = $this->checkContextConfigIntegrity($action, $metadata_state);
    $this->prepareExecutionMetadataState($metadata_state, NULL, $apply_assertions);
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareExecutionMetadataState(ExecutionMetadataStateInterface $metadata_state, ExpressionInterface $until = NULL, $apply_assertions = TRUE) {
    if ($until && $this->getUuid() === $until->getUuid()) {
      return TRUE;
    }
    $action = $this->actionManager->createInstance($this->configuration['action_id']);
    // Make sure to refine context first, such that possibly refined definitions
    // of provided context are respected.
    $this->prepareContextWithMetadata($action, $metadata_state);
    $this->addProvidedContextDefinitions($action, $metadata_state);
    if ($apply_assertions) {
      $this->assertMetadata($action, $metadata_state);
    }
  }

}
