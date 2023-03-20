<?php

namespace Drupal\social_automation\Engine;

use Drupal\Core\Plugin\PluginBase;
use Drupal\social_automation\Context\ExecutionState;

/**
 * Base class for automation expressions.
 */
abstract class ExpressionBase extends PluginBase implements ExpressionInterface {

  /**
   * The plugin configuration.
   *
   * @var array
   */
  protected $configuration;

  /**
   * The root expression if this object is nested.
   *
   * @var \Drupal\social_automation\Engine\ExpressionInterface
   */
  protected $root;

  /**
   * The config entity this expression is associated with, if any.
   *
   * @var string
   */
  protected $configEntityId;

  /**
   * The UUID of this expression.
   *
   * @var string
   */
  protected $uuid;

  /**
   * The weight (list order) of this expression.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * Constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
  }

  /**
   * Executes a automation expression.
   */
  public function execute() {
    // If there is no state given, we have to assume no required context.
    $state = ExecutionState::create();
    $result = $this->executeWithState($state);
    // Save specifically registered variables in the end after execution.
    $state->autoSave();
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return [
      'id' => $this->getPluginId(),
      'uuid' => $this->uuid,
      'weight' => $this->weight,
    ] + $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    if (isset($configuration['uuid'])) {
      $this->uuid = $configuration['uuid'];
    }
    if (isset($configuration['weight'])) {
      $this->weight = $configuration['weight'];
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormHandler() {
    if (isset($this->pluginDefinition['form_class'])) {
      $class_name = $this->pluginDefinition['form_class'];
      return new $class_name($this);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRoot() {
    if (isset($this->root)) {
      // @todo This seems to be the parent, not root.
      return $this->root->getRoot();
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setRoot(ExpressionInterface $root) {
    $this->root = $root;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLabel() {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getUuid() {
    return $this->uuid;
  }

  /**
   * {@inheritdoc}
   */
  public function setUuid($uuid) {
    $this->uuid = $uuid;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * {@inheritdoc}
   */
  public function setWeight($weight) {
    $this->weight = $weight;
    return $this;
  }

}
