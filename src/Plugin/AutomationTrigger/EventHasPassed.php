<?php

namespace Drupal\social_automation\Plugin\AutomationTrigger;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\social_automation\Plugin\AutomationTriggerBase;

/**
 * An event has passed X days.
 *
 * @AutomationTrigger(
 *   id = "social_automation_event_has_passed",
 *   label = "Event has passed for X days",
 *   category="Time Trigger",
 *   event="cron",
 *   trigger_type="time",
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node", label = @Translation("Node"))
 *   },
 *   hidden="TRUE",
 *   requiresCondition="FALSE",
 *   definition={},
 * )
 */
class EventHasPassed extends AutomationTriggerBase implements
    ConfigurableInterface {

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
    // @todo Implement getConfiguration() method.
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    // @todo Implement setConfiguration() method.
    $this->configuration = $configuration;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    // @todo Implement defaultConfiguration() method.
  }

}
