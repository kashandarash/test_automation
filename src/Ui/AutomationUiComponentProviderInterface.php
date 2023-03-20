<?php

namespace Drupal\social_automation\Ui;

use Drupal\social_automation\Engine\AutomationComponent;

/**
 * Interface for objects providing components for editing.
 *
 * Usually, this would be implemented by a config entity storing the component.
 */
interface AutomationUiComponentProviderInterface {

  /**
   * Gets the Automation component to be edited.
   *
   * @return \Drupal\social_automation\Engine\AutomationComponent
   *   The Automation component.
   */
  public function getComponent();

  /**
   * Updates the configuration based upon the given component.
   *
   * @param \Drupal\social_automation\Engine\AutomationComponent $component
   *   The component containing the configuration to set.
   *
   * @return $this
   */
  public function updateFromComponent(AutomationComponent $component);

}
