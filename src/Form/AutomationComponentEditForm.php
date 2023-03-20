<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Ui\AutomationUiConfigHandler;

/**
 * Provides a form to edit a component.
 */
class AutomationComponentEditForm extends AutomationComponentFormBase {

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiConfigHandler
   */
  protected $automationUiHandler;

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiConfigHandler $social_automation_ui_handler = NULL) {
    // Overridden such we can receive further route parameters.
    $this->automationUiHandler = $social_automation_ui_handler;
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareEntity() {
    parent::prepareEntity();
    // Replace the config entity with the latest entity from temp store, so any
    // interim changes are picked up.
    $this->entity = $this->automationUiHandler->getConfig();
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    // CSS to make form easier to use. Load this at end so we can override
    // styles added by #theme table.
    $form['#attached']['library'][] = 'social_automation/social_automation_ui.styles';

    $form = $this->automationUiHandler->getForm()->buildForm($form, $form_state);
    return parent::form($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
    $this->automationUiHandler->getForm()->validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = $this->t('Save');
    $actions['cancel'] = [
      '#type' => 'submit',
      '#limit_validation_errors' => [['locked']],
      '#value' => $this->t('Cancel'),
      '#submit' => ['::cancel'],
    ];
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $this->automationUiHandler->getForm()->submitForm($form, $form_state);
    $component = $this->automationUiHandler->getComponent();
    $this->entity->updateFromComponent($component);

    // Persist changes by saving the entity.
    parent::save($form, $form_state);

    // Also remove the temporarily stored component, it has been persisted now.
    $this->automationUiHandler->clearTemporaryStorage();

    $this->messenger()->addMessage($this->t('Workflow event component %label has been updated.', ['%label' => $this->entity->label()]));
  }

  /**
   * Form submission handler for the 'cancel' action.
   */
  public function cancel(array $form, FormStateInterface $form_state) {
    $this->automationUiHandler->clearTemporaryStorage();
    $this->messenger()->addMessage($this->t('Canceled.'));
    $form_state->setRedirect('entity.automation_component.collection');
  }

  /**
   * Title callback: also display the workflowevent label.
   */
  public function getTitle($automation_component) {
    return $this->t('Edit automation component "@label"', ['@label' => $automation_component->label()]);
  }

}
