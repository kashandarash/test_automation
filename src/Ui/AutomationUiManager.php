<?php

namespace Drupal\social_automation\Ui;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\Core\Plugin\Factory\ContainerFactory;

/**
 * Plugin manager for Automation Ui instances.
 *
 * Automation UIs are primarily defined in *.social_automation_ui.yml files.
 * Usually, there is no need to specify a 'class' as there is a suiting default
 * handler class in place. However, if done see the class must implement
 * \Drupal\social_automation\Ui\AutomationUiHandlerInterface.
 *
 * @see \Drupal\social_automation\Ui\AutomationUiHandlerInterface
 */
class AutomationUiManager extends DefaultPluginManager implements AutomationUiManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function __construct(ModuleHandlerInterface $module_handler) {
    $this->alterInfo('social_automation_ui');
    $this->discovery = new ContainerDerivativeDiscoveryDecorator(new YamlDiscovery('social_automation_ui', $module_handler->getModuleDirectories()));
    $this->factory = new ContainerFactory($this, AutomationUiHandlerInterface::class);
    $this->moduleHandler = $module_handler;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    $definition = new AutomationUiDefinition($definition);
    $definition->validate();
  }

}
