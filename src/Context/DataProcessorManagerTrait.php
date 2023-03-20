<?php

namespace Drupal\social_automation\Context;

/**
 * Trait for easily using the data processor service.
 *
 * @see \Drupal\social_automation\Context\DataProcessorManager
 */
trait DataProcessorManagerTrait {

  /**
   * The data processor manager.
   *
   * @var \Drupal\social_automation\Context\DataProcessorManager
   */
  protected $dataProcessorManager;

  /**
   * Sets the data processor manager.
   *
   * @param \Drupal\social_automation\Context\DataProcessorManager $dataProcessorManager
   *   The data processor manager.
   *
   * @return $this
   */
  public function setDataProcessorManager(DataProcessorManager $dataProcessorManager) {
    $this->dataProcessorManager = $dataProcessorManager;
    return $this;
  }

  /**
   * Gets the data processor manager.
   *
   * @return \Drupal\social_automation\Context\DataProcessorManager
   *   The data processor manager.
   */
  public function getDataProcessorManager() {
    if (empty($this->dataProcessorManager)) {
      $this->dataProcessorManager = \Drupal::service('plugin.manager.social_automation_data_processor');
    }
    return $this->dataProcessorManager;
  }

}
