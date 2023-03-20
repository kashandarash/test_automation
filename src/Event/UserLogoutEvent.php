<?php

namespace Drupal\social_automation\Event;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Event that is fired when a user logs out.
 *
 * @see social_automation_user_logout()
 */
class UserLogoutEvent extends GenericEvent {

  const EVENT_NAME = 'social_automation_user_logout';

}
