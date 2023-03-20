<?php

namespace Drupal\social_automation\Context;

/**
 * Interface for Automation data processor plugins.
 */
interface DataProcessorInterface {

  /**
   * Process the given value.
   *
   * @param mixed $value
   *   The value to process.
   * @param \Drupal\social_automation\Context\ExecutionStateInterface $social_automation_state
   *   The current Automation execution state containing all context variables.
   *
   * @return mixed
   *   The processed value. Since the value can also be a primitive data type
   *   (a string for example) this function must return the value.
   *
   * @throws \Drupal\social_automation\Exception\EvaluationException
   *   Thrown when the data cannot be processed.
   */
  public function process($value, ExecutionStateInterface $social_automation_state);

}
