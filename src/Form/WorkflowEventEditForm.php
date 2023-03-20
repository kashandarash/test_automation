<?php

namespace Drupal\social_automation\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\social_automation\Engine\ExpressionManagerInterface;
use Drupal\social_automation\Plugin\AutomationTriggerManager;
use Drupal\social_automation\Ui\AutomationUiConfigHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form to edit a workflow event.
 */
class WorkflowEventEditForm extends AutomationComponentFormBase {

  /**
   * The event plugin manager.
   *
   * @var \Drupal\social_automation\Plugin\AutomationTriggerManager
   */
  protected $triggerManager;

  /**
   * The AutomationUI handler of the currently active UI.
   *
   * @var \Drupal\social_automation\Ui\AutomationUiConfigHandler
   */
  protected $automationUiHandler;

  /**
   * Constructs a new object of this class.
   *
   * @param \Drupal\social_automation\Engine\ExpressionManagerInterface $expression_manager
   *   The expression manager.
   * @param \Drupal\social_automation\Plugin\AutomationTriggerManager $trigger_manager
   *   The event plugin manager.
   */
  public function __construct(ExpressionManagerInterface $expression_manager, AutomationTriggerManager $trigger_manager) {
    parent::__construct($expression_manager);
    $this->triggerManager = $trigger_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.automation_expression'),
      $container->get('plugin.manager.automation_trigger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, AutomationUiConfigHandler $social_automation_ui_handler = NULL) {
    // Overridden so that we can receive further route parameters.
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
    $form['events'] = [
      '#type' => 'container',
      '#attributes' => ['class' => ['edit-events']],
    ];

    $form['events']['table'] = [
      '#theme' => 'table',
      '#header' => [$this->t('Events'), $this->t('Operations')],
      '#empty' => $this->t('None'),
    ];

    foreach ($this->entity->getEventNames() as $key => $event_name) {
      $event_definition = $this->triggerManager->getDefinition($event_name);
      $form['events']['table']['#rows'][$key]['element'] = [
        'data' => [
          '#type' => 'item',
          '#plain_text' => $event_definition['label'],
          '#suffix' => '<div class="description">' . $this->t('Machine name: @name', ['@name' => $event_name]) . '</div>',
        ],
      ];
      $form['events']['table']['#rows'][$key]['element']['colspan'] = 2;
    }

    // Put action buttons in the table footer.
    $links['add-event'] = [];
    $form['events']['table']['#footer'][] = [
      [
        'data' => [
          '#prefix' => '<ul class="action-links">',
          'local-action-links' => $links,
          '#suffix' => '</ul>',
        ],
        'colspan' => 2,
      ],
    ];

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

    // Remove the temporarily stored component; it has been persisted now.
    $this->automationUiHandler->clearTemporaryStorage();

    $this->messenger()->addMessage($this->t('Workflow event %label has been updated.', ['%label' => $this->entity->label()]));
  }

  /**
   * Form submission handler for the 'cancel' action.
   */
  public function cancel(array $form, FormStateInterface $form_state) {
    $this->automationUiHandler->clearTemporaryStorage();
    $this->messenger()->addMessage($this->t('Canceled.'));
    $form_state->setRedirect('entity.social_automation_workflow_event.collection');
  }

  /**
   * Title callback: also display the workflowevent label.
   */
  public function getTitle($social_automation_workflow_event) {
    return $this->t('Edit workflow event "@label"', ['@label' => $social_automation_workflow_event->label()]);
  }

}
