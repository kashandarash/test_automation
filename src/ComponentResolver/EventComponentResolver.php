<?php

namespace Drupal\social_automation\ComponentResolver;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\social_automation\Engine\ExpressionManagerInterface;
use Drupal\social_automation\Engine\AutomationComponent;
use Drupal\social_automation\Engine\AutomationComponentResolverInterface;
use Drupal\social_automation\Entity\WorkflowComponentConfig;
use Drupal\social_automation\Entity\WorkflowEventConfig;

/**
 * Resolves components that hold all reaction automation for a given event.
 */
class EventComponentResolver implements AutomationComponentResolverInterface {

  /**
   * The automation component entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * The Automation expression manager.
   *
   * @var \Drupal\social_automation\Engine\ExpressionManagerInterface
   */
  protected $expressionManager;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\social_automation\Engine\ExpressionManagerInterface $expression_manager
   *   The automation expression plugin manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ExpressionManagerInterface $expression_manager) {
    $this->entityStorage = $entity_type_manager->getStorage('social_automation_workflow_event');
    $this->expressionManager = $expression_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(array $event_ids) {
    // @todo Improve this by adding a custom expression plugin that clones
    // the state after each workflowevent, such that added variables added by
    // one workflowevent are not interfering with the variables of another
    // workflowevent.
    $results = [];
    foreach ($event_ids as $event_id) {
      $action_set = $this->expressionManager->createActionSet();
      // Only load active reaction automation - inactive (disabled)
      // Automation should not be executed, so we shouldn't even load them.
      $configs = $this->entityStorage->loadByProperties([
        'events.*.event_name' => $event_id,
        'status' => TRUE,
      ]);
      if ($configs) {
        // We should only produce $results
        // if there are loaded reaction automation.
        foreach ($configs as $config) {
          assert($config instanceof WorkflowEventConfig || $config instanceof WorkflowComponentConfig);
          $action_set->addExpressionObject($config->getExpression());
        }
        $results[$event_id] = AutomationComponent::create($action_set);
      }
    }
    return $results;
  }

}
