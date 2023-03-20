<?php

namespace Drupal\social_automation\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Event that is fired when a logger-item is created.
 */
class SystemLoggerEvent extends GenericEvent {

  const EVENT_NAME = 'social_automation_system_logger_event';

}
