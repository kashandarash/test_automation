<?php

namespace Drupal\social_automation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\social_automation\Entity\WorkflowEventConfig;

/**
 * Controller methods for Reaction social_automation.
 */
class AutomationReactionController extends ControllerBase {

  /**
   * Enables a workflow event.
   *
   * @param \Drupal\social_automation\Entity\WorkflowEventConfig $social_automation_workflow_event
   *   The workflow event configuration entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the reaction automation listing page.
   */
  public function enableConfig(WorkflowEventConfig $social_automation_workflow_event) {
    $social_automation_workflow_event->enable()->save();

    $this->getLogger('social_automation')->notice('The workflow event %label has been enabled.', [
      '%label' => $social_automation_workflow_event->label(),
    ]);
    $this->messenger()->addMessage($this->t('The workflow event %label has been enabled.', [
      '%label' => $social_automation_workflow_event->label(),
    ]));

    return $this->redirect('entity.social_automation_workflow_event.collection');
  }

  /**
   * Disables a workflow event.
   *
   * @param \Drupal\social_automation\Entity\WorkflowEventConfig $social_automation_workflow_event
   *   The workflow event configuration entity.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect response to the reaction automation listing page.
   */
  public function disableConfig(WorkflowEventConfig $social_automation_workflow_event) {
    $social_automation_workflow_event->disable()->save();

    $this->getLogger('social_automation')->notice('The workflow event %label has been disabled.', [
      '%label' => $social_automation_workflow_event->label(),
    ]);
    $this->messenger()->addMessage($this->t('The workflow event %label has been disabled.', [
      '%label' => $social_automation_workflow_event->label(),
    ]));

    return $this->redirect('entity.social_automation_workflow_event.collection');
  }

}
