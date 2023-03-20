<?php

namespace Drupal\social_automation\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\social_automation\Context\ContextDefinition;
use Drupal\social_automation\Automation;
use Drupal\social_automation\Ui\AutomationUiComponentProviderInterface;
use Drupal\social_automation\Engine\ExpressionInterface;
use Drupal\social_automation\Engine\AutomationComponent;

/**
 * Automation component configuration entity to store configuration.
 *
 * @ConfigEntityType(
 *   id = "automation_component",
 *   label = @Translation("Automation component"),
 *   label_collection = @Translation("Automation components"),
 *   label_singular = @Translation("automation component"),
 *   label_plural = @Translation("automation components"),
 *   label_count = @PluralTranslation(
 *     singular = "@count automation component",
 *     plural = "@count automation components",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\social_automation\Controller\AutomationComponentListBuilder",
 *     "form" = {
 *        "add" = "\Drupal\social_automation\Form\AutomationComponentAddForm",
 *        "edit" = "\Drupal\social_automation\Form\AutomationComponentEditForm",
 *        "delete" = "\Drupal\Core\Entity\EntityDeleteForm",
 *      },
 *   },
 *   admin_permission = "administer automation",
 *   config_prefix = "component",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "status" = "status",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *     "tags",
 *     "config_version",
 *     "component",
 *   },
 *   links = {
 *     "collection" = "/admin/config/workflow/social_automation/components",
 *     "edit-form" = "/admin/config/workflow/social_automation/components/edit/{automation_component}",
 *     "delete-form" = "/admin/config/workflow/social_automation/components/delete/{automation_component}",
 *   }
 * )
 */
class WorkflowComponentConfig extends ConfigEntityBase implements AutomationUiComponentProviderInterface {

  /**
   * The unique ID of the Automation component.
   *
   * @var string
   */
  public $id = NULL;

  /**
   * The label of the Automation component.
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
   * The "tags" of a Automation component.
   *
   * @var string[]
   */
  protected $tags = [];

  /**
   * The config version the Automation component was created for.
   *
   * @var int
   */
  protected $config_version = Automation::CONFIG_VERSION;

  /**
   * The component configuration as nested array.
   *
   * @var array
   *
   * @see \Drupal\social_automation\Engine\AutomationComponent::getConfiguration()
   */
  protected $component = [];

  /**
   * Stores a reference to the component object.
   *
   * @var \Drupal\social_automation\Engine\AutomationComponent
   */
  protected $componentObject;

  /**
   * Gets a Automation expression instance for this Automation component.
   *
   * @return \Drupal\social_automation\Engine\ExpressionInterface
   *   A Automation expression instance.
   */
  public function getExpression() {
    return $this->getComponent()->getExpression();
  }

  /**
   * Sets a Automation expression instance for this Automation component.
   *
   * @param \Drupal\social_automation\Engine\ExpressionInterface $expression
   *   The expression to set.
   *
   * @return $this
   */
  public function setExpression(ExpressionInterface $expression) {
    $this->component['expression'] = $expression->getConfiguration();
    unset($this->componentObject);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getComponent() {
    if (!isset($this->componentObject)) {
      $this->componentObject = AutomationComponent::createFromConfiguration($this->component);
    }
    return $this->componentObject;
  }

  /**
   * {@inheritdoc}
   */
  public function updateFromComponent(AutomationComponent $component) {
    $this->component = $component->getConfiguration();
    $this->componentObject = $component;
    return $this;
  }

  /**
   * Gets the definitions of the used context.
   *
   * @return \Drupal\social_automation\Context\ContextDefinitionInterface[]
   *   The array of context definition, keyed by context name.
   */
  public function getContextDefinitions() {
    $definitions = [];
    if (!empty($this->component['context_definitions'])) {
      foreach ($this->component['context_definitions'] as $name => $definition) {
        $definitions[$name] = ContextDefinition::createFromArray($definition);
      }
    }
    return $definitions;
  }

  /**
   * Sets the definitions of the used context.
   *
   * @param \Drupal\social_automation\Context\ContextDefinitionInterface[] $definitions
   *   The array of context definitions, keyed by context name.
   *
   * @return $this
   */
  public function setContextDefinitions(array $definitions) {
    $this->component['context_definitions'] = [];
    foreach ($definitions as $name => $definition) {
      $this->component['context_definitions'][$name] = $definition->toArray();
    }
    return $this;
  }

  /**
   * Gets the definitions of the provided context.
   *
   * @return \Drupal\social_automation\Context\ContextDefinitionInterface[]
   *   The array of context definition, keyed by context name.
   */
  public function getProvidedContextDefinitions() {
    $definitions = [];
    if (!empty($this->component['provided_context_definitions'])) {
      foreach ($this->component['provided_context_definitions'] as $name => $definition) {
        $definitions[$name] = ContextDefinition::createFromArray($definition);
      }
    }
    return $definitions;
  }

  /**
   * Sets the definitions of the provided context.
   *
   * @param \Drupal\social_automation\Context\ContextDefinitionInterface[] $definitions
   *   The array of context definitions, keyed by context name.
   *
   * @return $this
   */
  public function setProvidedContextDefinitions(array $definitions) {
    $this->component['provided_context_definitions'] = [];
    foreach ($definitions as $name => $definition) {
      $this->component['provided_context_definitions'][$name] = $definition->toArray();
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function createDuplicate() {
    $duplicate = parent::createDuplicate();
    unset($duplicate->componentObject);
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
    unset($this->expression);
  }

}
