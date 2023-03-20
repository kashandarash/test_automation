<?php

namespace Drupal\social_automation\ComponentResolver;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\social_automation\Engine\AutomationComponentResolverInterface;
use Drupal\social_automation\Entity\WorkflowComponentConfig;

/**
 * Resolves Automation component configs.
 */
class ComponentConfigResolver implements AutomationComponentResolverInterface {

  /**
   * The automation component entity storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $entityStorage;

  /**
   * Constructs the object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityStorage = $entity_type_manager->getStorage('automation_component');
  }

  /**
   * {@inheritdoc}
   */
  public function getMultiple(array $ids) {
    return array_map(function (WorkflowComponentConfig $config) {
      return $config->getComponent();
    }, $this->entityStorage->loadMultiple($ids));
  }

}
