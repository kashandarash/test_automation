<?php

namespace Drupal\social_automation\EventHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Core\AutomationConfigurableEventHandlerInterface;
use Drupal\social_automation\Core\AutomationDefaultEventHandler;

/**
 * Base class for event handler.
 */
abstract class ConfigurableEventHandlerBase extends AutomationDefaultEventHandler implements AutomationConfigurableEventHandlerInterface {

  /**
   * The event configuration.
   *
   * @var array
   */
  protected $configuration = [];

  /**
   * {@inheritdoc}
   */
  public function extractConfigurationFormValues(array &$form, FormStateInterface $form_state) {
    foreach ($this->defaultConfiguration() as $key => $configuration) {
      $this->configuration[$key] = $form_state->hasValue($key) ? $form_state->getValue($key) : $configuration;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

}
