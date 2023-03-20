<?php

namespace Drupal\social_automation\Plugin;

use Drupal\Component\Plugin\CategorizingPluginManagerInterface;
use Drupal\Core\Plugin\CategorizingPluginManagerTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\social_automation\Annotation\AutomationTrigger;

/**
 * Provides the Automation trigger plugin manager.
 */
class AutomationTriggerManager extends DefaultPluginManager implements CategorizingPluginManagerInterface {

  use CategorizingPluginManagerTrait;

  /**
   * Constructs a new AutomationTriggerManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/AutomationTrigger', $namespaces, $module_handler, AutomationTriggerInterface::class, AutomationTrigger::class);

    $this->alterInfo('social_automation_automation_trigger_info');
    $this->setCacheBackend($cache_backend, 'social_automation_automation_trigger_plugins');
  }

  /**
   * Gets the base name of a configured event name.
   *
   * @param string $event_name
   *   The name of the triggered event.
   *
   * @return string
   *   The name of the triggered event.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *
   * @see \Drupal\social_automation\Core\ConfigurableEventHandlerInterface::getEventNameSuffix()
   */
  public function getTriggerEventName(string $event_name): ?string {
    // Cut off any suffix from a configured event name.
    $event = $this->getDefinition($event_name);

    if (NULL === $event || !isset($event['event'])) {
      return NULL;
    }

    $trigger_name = sprintf('social_automation_%s', $event['event']);

    // Cut off any suffix from a configured event name.
    if (strpos($trigger_name, '--') !== FALSE) {
      $parts = explode('--', $trigger_name, 2);
      return $parts[0];
    }
    return $trigger_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getGroupedDefinitions(array $definitions = NULL, $label_key = 'label'): array {
    /** @var \Drupal\Core\Plugin\CategorizingPluginManagerTrait|\Drupal\Component\Plugin\PluginManagerInterface $this */
    $definitions = $this->getSortedDefinitions($definitions ?? $this->getDefinitions(), $label_key);
    $grouped_definitions = [];
    foreach ($definitions as $id => $definition) {
      // Remove hidden triggers.
      if (isset($definition['hidden']) && $definition['hidden'] === "TRUE") {
        continue;
      }
      $grouped_definitions[(string) $definition['category']][$id] = $definition;
    }
    return $grouped_definitions;
  }

}
