<?php

namespace Drupal\social_automation\Ui;

/**
 * Get the social_automation_ui_handler attribute of the current request.
 *
 * Note that the current route must have the _social_automation_ui option set
 * for the handler being available. This is done automatically for routes
 * generated for the social_automation_ui
 * (via \Drupal\social_automation\Routing\AutomationUiRouteSubscriber).
 */
trait AutomationUiHandlerTrait {

  /**
   * The automation UI handler.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiHandlerInterface
   */
  protected $automationUiHandler;

  /**
   * Gets the automation UI handler of the current route.
   *
   * @return \Drupal\social_automation\Ui\AutomationUiHandlerInterface|null
   *   The handler, or NULL if this is no social_automation_ui enabled route.
   */
  public function getAutomationUiHandler() {
    if (!isset($this->automationUiHandler)) {
      $this->automationUiHandler = \Drupal::request()->attributes->get('social_automation_ui_handler');
    }
    return $this->automationUiHandler;
  }

  /**
   * Sets the Automation UI handler.
   *
   * @param \Drupal\social_automation\Ui\AutomationUiHandlerInterface $social_automation_ui_handler
   *   The Automation UI handler to set.
   *
   * @return $this
   */
  public function setAutomationUiHandler(AutomationUiHandlerInterface $social_automation_ui_handler) {
    $this->automationUiHandler = $social_automation_ui_handler;
    return $this;
  }

}
