<?php

namespace Drupal\social_automation\Core;

use Drupal\Component\Plugin\CategorizingPluginManagerInterface;
use Drupal\Core\Plugin\Context\ContextAwarePluginManagerInterface;

/**
 * Interface the Automation Action plugin manager of the Automation actions API.
 *
 * @see \Drupal\Core\Annotation\Action
 * @see \Drupal\Core\Action\ActionInterface
 * @see \Drupal\Core\Action\ActionBase
 * @see plugin_api
 */
interface AutomationActionManagerInterface extends CategorizingPluginManagerInterface, ContextAwarePluginManagerInterface {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\social_automation\Core\AutomationActionInterface
   *   A fully configured plugin instance.
   */
  public function createInstance($plugin_id, array $configuration = []);

}
