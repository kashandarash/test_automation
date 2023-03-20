<?php

namespace Drupal\social_automation\Engine;

/**
 * Interface for the component repository.
 *
 * The component repository provides the API for fetching executable Automation
 * components from cache.
 */
interface AutomationComponentRepositoryInterface {

  /**
   * Adds a component resolver.
   *
   * @param \Drupal\social_automation\Engine\AutomationComponentResolverInterface $resolver
   *   The resolver.
   * @param string $resolver_name
   *   The name under which to add the resolver.
   *
   * @return $this
   */
  public function addComponentResolver(AutomationComponentResolverInterface $resolver, $resolver_name);

  /**
   * Gets the component for the given ID.
   *
   * @param string $id
   *   The ID of the component to get. The supported IDs depend on the given
   *   provider. For the default provider 'automation'
   *   the entity IDs of component configs may be passed.
   * @param string $resolver
   *   The resolver of the component. Supported values are:
   *   - automation_component: (Default) The component configs identified
   *     by their ID.
   *   - automation_event: The aggregated components of all reaction
   *     automation configured for an event, identified by the event name; e.g.,
   *     'social_automation_entity_presave'.
   *   Note, that modules may add further resolvers via tagged services. Check
   *   the social_automation.services.yml for an example.
   *
   * @return \Drupal\social_automation\Engine\AutomationComponent|null
   *   The component, or NULL if it is not existing.
   *
   * @throws \Drupal\social_automation\Exception\InvalidArgumentException
   *   Thrown if an unsupported provider is given.
   */
  public function get($id, $resolver = 'automation_component');

  /**
   * Gets the components for the given IDs.
   *
   * @param string[] $ids
   *   The IDs of the components to get. The supported IDs depend on the given
   *   provider. For the default provider 'automation' the entity IDs
   *   of component configs may be passed.
   * @param string $resolver
   *   The resolver of the component. See ::get() for a list of supported
   *   resolvers.
   *
   * @return \Drupal\social_automation\Engine\AutomationComponent[]
   *   An array of components, keyed by component ID.
   *
   * @throws \Drupal\social_automation\Exception\InvalidArgumentException
   *   Thrown if an unsupported provider is given.
   */
  public function getMultiple(array $ids, $resolver = 'automation_component');

}
