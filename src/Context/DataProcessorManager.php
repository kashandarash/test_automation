<?php

namespace Drupal\social_automation\Context;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\social_automation\Annotation\AutomationDataProcessor;

/**
 * Plugin manager for Automation data processors.
 *
 * @see \Drupal\social_automation\Context\DataProcessorInterface
 */
class DataProcessorManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler, $plugin_definition_annotation_name = AutomationDataProcessor::class) {
    $this->alterInfo('social_automation_data_processor');
    parent::__construct('Plugin/AutomationDataProcessor', $namespaces, $module_handler, DataProcessorInterface::class, $plugin_definition_annotation_name);
  }

}
