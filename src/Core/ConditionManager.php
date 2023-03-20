<?php

namespace Drupal\social_automation\Core;

use Drupal\Core\Condition\ConditionManager as CoreConditionManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\social_automation\Context\AnnotatedClassDiscovery;

/**
 * Extends core condition manager to add in Automation' context improvements.
 */
class ConditionManager extends CoreConditionManager {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\social_automation\Core\AutomationConditionInterface|\Drupal\Core\Condition\ConditionInterface
   *   A fully configured plugin instance.
   */
  // phpcs:ignore
  public function createInstance($plugin_id, array $configuration = []) {
    return parent::createInstance($plugin_id, $configuration);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!$this->discovery) {
      // Swap out the annotated class discovery used, so we can control the
      // annotation classes picked.
      $discovery = new AnnotatedClassDiscovery($this->subdir, $this->namespaces, $this->pluginDefinitionAnnotationName);
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $definitions = parent::getDefinitions();
    // Make sure that all definitions have a category to avoid PHP notices in
    // CategorizingPluginManagerTrait.
    // @todo Fix this in core in CategorizingPluginManagerTrait.
    foreach ($definitions as &$definition) {
      if (!isset($definition['category'])) {
        $definition['category'] = $this->t('Other');
      }
    }
    return $definitions;
  }

}
