<?php

namespace Drupal\social_automation\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\social_automation\Automation;
use Drupal\social_automation\Ui\AutomationUiComponentProviderInterface;
use Drupal\social_automation\Engine\ExpressionInterface;
use Drupal\social_automation\Engine\AutomationComponent;

/**
 * Workflow event configuration entity to persistently store configuration.
 *
 * @ConfigEntityType(
 *   id = "social_automation_workflow_event",
 *   label = @Translation("Workflow event"),
 *   label_collection = @Translation("Reaction Automation"),
 *   label_singular = @Translation("workflow event"),
 *   label_plural = @Translation("reaction automation"),
 *   label_count = @PluralTranslation(
 *     singular = "@count workflow event",
 *     plural = "@count reaction automation",
 *   ),
 *   handlers = {
 *     "storage" = "Drupal\social_automation\Entity\WorkflowEventStorage",
 *     "list_builder" = "Drupal\social_automation\Controller\AutomationReactionListBuilder",
 *     "form" = {
 *        "add" = "\Drupal\social_automation\Form\WorkflowEventAddForm",
 *        "edit" = "\Drupal\social_automation\Form\WorkflowEventEditForm",
 *        "delete" = "\Drupal\Core\Entity\EntityDeleteForm",
 *      },
 *   },
 *   admin_permission = "administer automation",
 *   config_prefix = "reaction",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "events",
 *     "description",
 *     "tags",
 *     "config_version",
 *     "expression",
 *   },
 *   links = {
 *     "collection" = "/admin/config/workflow/automation",
 *     "edit-form" = "/admin/config/workflow/social_automation/reactions/edit/{social_automation_workflow_event}",
 *     "delete-form" = "/admin/config/workflow/social_automation/reactions/delete/{social_automation_workflow_event}",
 *     "enable" = "/admin/config/workflow/social_automation/reactions/enable/{social_automation_workflow_event}",
 *     "disable" = "/admin/config/workflow/social_automation/reactions/disable/{social_automation_workflow_event}",
 *     "break-lock-form" = "/admin/config/workflow/social_automation/reactions/edit/break-lock/{social_automation_workflow_event}",
 *   }
 * )
 */
class WorkflowEventConfig extends ConfigEntityBase implements AutomationUiComponentProviderInterface {

  /**
   * The unique ID of the Workflow event.
   *
   * @var string
   */
  public $id = NULL;

  /**
   * The label of the Workflow event.
   *
   * @var string
   */
  protected $label;

  /**
   * The description of the workflowevent.
   *
   * @var string
   */
  protected $description = '';

  /**
   * The "tags" of a Workflow event.
   *
   * @var string[]
   */
  protected $tags = [];

  /**
   * The version the Workflow event was created for.
   *
   * @var int
   */
  protected $config_version = Automation::CONFIG_VERSION;

  /**
   * The expression plugin specific configuration as nested array.
   *
   * @var array
   */
  protected $expression = [
    'id' => 'automation_expression',
  ];

  /**
   * Stores a reference to the executable expression version of this component.
   *
   * @var \Drupal\social_automation\Engine\ExpressionInterface
   */
  protected $expressionObject;

  /**
   * The events this workflow event is reacting on.
   *
   * Events array. The array is numerically indexed and contains arrays with the
   * following structure:
   *   - event_name: String with the event machine name.
   *   - configuration: An array containing the event configuration.
   *
   * @var array
   */
  protected $events = [];

  /**
   * Sets a Automation expression instance for this Workflow event.
   *
   * @param \Drupal\social_automation\Engine\ExpressionInterface $expression
   *   The expression to set.
   *
   * @return $this
   */
  public function setExpression(ExpressionInterface $expression) {
    $this->expressionObject = $expression;
    $this->expression = $expression->getConfiguration();
    return $this;
  }

  /**
   * Gets a Automation expression instance for this Workflow event.
   *
   * @return \Drupal\social_automation\Engine\ExpressionInterface
   *   A Automation expression instance.
   */
  public function getExpression() {
    // Ensure that an executable Automation expression is available.
    if (!isset($this->expressionObject)) {
      $this->expressionObject = $this->getExpressionManager()->createInstance($this->expression['id'], $this->expression);
    }
    return $this->expressionObject;
  }

  /**
   * {@inheritdoc}
   *
   * Gets the Automation component that is invoked when the
   * events are dispatched. The returned component has the definitions
   * of the available event context set.
   */
  public function getComponent() {
    $component = AutomationComponent::create($this->getExpression());
    $component->addContextDefinitionsForEvents($this->getEventNames());
    return $component;
  }

  /**
   * {@inheritdoc}
   */
  public function updateFromComponent(AutomationComponent $component) {
    // Note that the available context definitions stem from the configured
    // events, which are handled separately.
    $this->setExpression($component->getExpression());
    return $this;
  }

  /**
   * Returns the Automation expression manager.
   *
   * @todo Actually we should use dependency injection here, but is that even
   *   possible with config entities? How?
   *
   * @return \Drupal\social_automation\Engine\ExpressionManager
   *   The Automation expression manager.
   */
  protected function getExpressionManager() {
    return \Drupal::service('plugin.manager.automation_expression');
  }

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    unset($duplicate->expressionObject);
    return $duplicate;
  }

  /**
   * Overrides \Drupal\Core\Entity\Entity::label().
   *
   * When a certain component does not have a label return the ID.
   */
  public function label() {
    if (!$label = $this->get('label')) {
      $label = $this->id();
    }
    return $label;
  }

  /**
   * Returns the description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Checks if there are tags associated with this config.
   *
   * @return bool
   *   TRUE if the config has tags.
   */
  public function hasTags() {
    return !empty($this->tags);
  }

  /**
   * Returns the tags associated with this config.
   *
   * @return string[]
   *   The numerically indexed array of tag names.
   */
  public function getTags() {
    return $this->tags;
  }

  /**
   * Gets configuration of all events the workflowevent is reacting on.
   *
   * @return array
   *   The events array. The array is numerically indexed and contains arrays
   *   with the following structure:
   *     - event_name: String with the event machine name.
   *     - configuration: An array containing the event configuration.
   */
  public function getEvents() {
    return $this->events;
  }

  /**
   * Gets machine names of all events the workflowevent is reacting on.
   *
   * @return string[]
   *   The array of fully qualified event names of the workflowevent.
   */
  public function getEventNames() {
    $names = [];
    foreach ($this->events as $event) {
      $names[] = $event['event_name'];
    }
    return $names;
  }

  /**
   * {@inheritdoc}
   */
  public function calculateDependencies() {
    parent::calculateDependencies();
    $this->addDependencies($this->getComponent()->calculateDependencies());
    return $this->dependencies;
  }

  /**
   * Magic clone method.
   */
  public function __clone() {
    // Remove the reference to the expression object in the clone so that the
    // expression object tree is created from scratch.
    unset($this->expressionObject);
  }

}
