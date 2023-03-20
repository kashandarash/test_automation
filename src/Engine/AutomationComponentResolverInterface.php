<?php

namespace Drupal\social_automation\Engine;

/**
 * Interface for automation component resolvers.
 *
 * A resolver is responsible for getting components for a certain provider. The
 * component resolvers are added to the repository via tagged services and
 * provider name is determined.
 */
interface AutomationComponentResolverInterface {

  /**
   * Gets multiple components.
   *
   * @param string[] $ids
   *   The list of IDs of the components to get.
   *
   * @return \Drupal\social_automation\Engine\AutomationComponent[]
   *   The array of components that could be resolved, keyed by ID.
   */
  public function getMultiple(array $ids);

}
