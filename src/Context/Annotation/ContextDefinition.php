<?php

namespace Drupal\social_automation\Context\Annotation;

use Drupal\Core\Annotation\ContextDefinition as CoreContextDefinition;
use Drupal\Core\Annotation\Translation;
use Drupal\social_automation\Context\ContextDefinition as AutomationContextDefinition;

/**
 * Extends the core context definition annotation object for social_automation.
 *
 * Ensures context definitions use
 * \Drupal\social_automation\Context\ContextDefinitionInterface.
 *
 * @Annotation
 *
 * @ingroup plugin_context
 */
class ContextDefinition extends CoreContextDefinition {

  /**
   * The ContextDefinitionInterface object.
   *
   * @var \Drupal\social_automation\Context\ContextDefinitionInterface
   */
  protected $definition;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values) {
    // Filter out any @Translation annotation objects.
    foreach ($values as $key => $value) {
      if ($value instanceof Translation) {
        $values[$key] = $value->get();
      }
    }
    $this->definition = AutomationContextDefinition::createFromArray($values);
  }

  /**
   * Returns the value of an annotation.
   *
   * @return \Drupal\social_automation\Context\ContextDefinitionInterface
   *   Return the Automation version of the ContextDefinitionInterface.
   */
  public function get() {
    return $this->definition;
  }

}
