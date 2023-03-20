<?php

namespace Drupal\social_automation\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Event that is fired when cron maintenance tasks are performed.
 *
 * @see social_automation_cron()
 */
class SystemCronEvent extends GenericEvent {

  const EVENT_NAME = 'social_automation_system_cron';

}
