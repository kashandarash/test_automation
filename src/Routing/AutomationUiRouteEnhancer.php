<?php

namespace Drupal\social_automation\Routing;

use Drupal\Core\Routing\EnhancerInterface;
use Drupal\social_automation\Ui\AutomationUiManagerInterface;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Enhances routes with the specified AutomationUI.
 *
 * Routes have the plugin ID of the active AutomationUI instance set on the
 * _social_automation_ui option. Based upon that information, this enhances
 *  adds the following parameters to the routes:
 * - social_automation_ui_handler:
 *     The AutomationUI handler, as specified by the plugin.
 * - automation_component:
 *     The automation component being edited, as provided by the handler.
 */
class AutomationUiRouteEnhancer implements EnhancerInterface {

  /**
   * The social_automation_ui plugin manager.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiManagerInterface
   */
  protected $automationUiManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\social_automation\Ui\AutomationUiManagerInterface $social_automation_ui_manager
   *   The social_automation_ui plugin manager.
   */
  public function __construct(AutomationUiManagerInterface $social_automation_ui_manager) {
    $this->automationUiManager = $social_automation_ui_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function enhance(array $defaults, Request $request) {
    /** @var \Symfony\Component\Routing\Route $route */
    $route = $defaults[RouteObjectInterface::ROUTE_OBJECT];
    if ($plugin_id = $route->getOption('_social_automation_ui')) {
      $defaults['social_automation_ui_handler'] = $this->automationUiManager->createInstance($plugin_id);
    }
    return $defaults;
  }

}
