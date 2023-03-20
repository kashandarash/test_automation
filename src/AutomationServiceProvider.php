<?php

namespace Drupal\social_automation;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Drupal\social_automation\Core\ConditionManager;

/**
 * Swaps out the core condition manager.
 */
class AutomationServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Overrides the core condition plugin manager service with our own.
    $definition = $container->getDefinition('plugin.manager.condition');
    $definition->setClass(ConditionManager::class);
  }

}
