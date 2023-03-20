<?php

namespace Drupal\social_automation\Plugin\AutomationDataProcessor;

use Drupal\Core\Plugin\PluginBase;
use Drupal\social_automation\Context\DataProcessorInterface;
use Drupal\social_automation\Context\ExecutionStateInterface;

/**
 * A data processor for applying numerical offsets.
 *
 * The plugin configuration must contain the following entry:
 * - offset: the value that should be added.
 *
 * @AutomationDataProcessor(
 *   id = "social_automation_numeric_offset",
 *   label = @Translation("Apply numeric offset")
 * )
 */
class NumericOffset extends PluginBase implements DataProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process($value, ExecutionStateInterface $social_automation_state) {
    return $value + $this->configuration['offset'];
  }

}
