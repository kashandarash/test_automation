<?php

namespace Drupal\social_automation\Plugin\AutomationAction;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\social_automation\Engine\ExpressionManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Derives Rules component action plugin definitions from config entities.
 *
 * @see \Drupal\social_automation\Plugin\AutomationAction\AutomationComponentAction
 */
class AutomationComponentActionDeriver extends DeriverBase implements ContainerDeriverInterface {
  use StringTranslationTrait;

  /**
   * The config entity storage that holds Rules components.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected EntityStorageInterface $storage;

  /**
   * The Rules expression manager.
   *
   * @var \Drupal\social_automation\Engine\ExpressionManagerInterface
   */
  protected ExpressionManagerInterface $expressionManager;

  /**
   * Creates a new RulesComponentActionDeriver object.
   *
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   *   The entity storage.
   * @param \Drupal\social_automation\Engine\ExpressionManagerInterface $expression_manager
   *   The Rules expression manager.
   */
  public function __construct(EntityStorageInterface $storage, ExpressionManagerInterface $expression_manager) {
    $this->storage = $storage;
    $this->expressionManager = $expression_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('entity_type.manager')->getStorage('automation_component'),
      $container->get('plugin.manager.automation_expression')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition): array {
    $automation_components = $this->storage->loadMultiple();
    /** @var  \Drupal\social_automation\Entity\WorkflowComponentConfig $automation_component */
    foreach ($automation_components as $automation_component) {
      $automation_id = $automation_component->id();

      /** @var \Drupal\social_automation\Entity\WorkflowComponentConfig $automation_component */
      $component_config = $automation_component->get('component');
      $expression_definition = $this->expressionManager->getDefinition($component_config['expression']['id']);

      $this->derivatives[$automation_id] = [
        'label' => $this->t('@expression_type: @label', [
          '@expression_type' => $expression_definition['label'],
          '@label' => $automation_component->label(),
        ]),
        'category' => $this->t('Components'),
        'component_id' => $automation_id,
        'context_definitions' => $automation_component->getContextDefinitions(),
        'provides' => $automation_component->getProvidedContextDefinitions(),
      ] + $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
