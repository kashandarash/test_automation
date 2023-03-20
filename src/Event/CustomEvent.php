<?php

namespace Drupal\social_automation\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Event that is fired when a user use search.
 *
 * @see social_automation_search_api_results_alter()
 */
class CustomEvent extends GenericEvent {

  const EVENT_NAME = 'social_automation_custom';

}
