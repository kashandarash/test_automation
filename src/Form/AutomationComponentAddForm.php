<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\social_automation\Entity\WorkflowComponentConfig;
use Drupal\social_automation\Entity\WorkflowEventConfig;

/**
 * Provides a form to add a component.
 */
class AutomationComponentAddForm extends AutomationComponentFormBase {

  /**
   * {@inheritdoc}
   */
  public function getEntityFromRouteMatch(RouteMatchInterface $route_match, $entity_type_id) {
    // Overridden to customize creation of new entities.
    if ($route_match->getRawParameter($entity_type_id) !== NULL) {
      $entity = $route_match->getParameter($entity_type_id);
    }
    else {
      $values = [];
      // @todo Create the right expression depending on the route.
      /** @var \Drupal\social_automation\Entity\WorkflowComponentConfig $entity */
      $entity = $this->entityTypeManager->getStorage($entity_type_id)->create($values);
      assert($entity instanceof WorkflowEventConfig || $entity instanceof WorkflowComponentConfig);
      $entity->setExpression($this->expressionManager->createWorkflowEvent());

    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save');
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    parent::save($form, $form_state);

    $this->messenger()->addMessage($this->t('Component %label has been created.', ['%label' => $this->entity->label()]));
    $form_state->setRedirect('entity.automation_component.edit_form', ['automation_component' => $this->entity->id()]);
  }

}
