/**
 * @file
 * Adds the collapsible functionality to the automation debug log.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Adds ability to "Open all" and "Close all" Automation debug log elements.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.social_automation_debug_log = {
    attach: function(context) {
      // Handle clicks on Open/Close text.
      $(context).find('.automation-debug-open-all').once('automation-open-all-details').click(function (event) {
        // Don't let the parent details element handle this event.
        event.preventDefault();
        // Don't let our other click handler, below, handle this event.
        event.stopPropagation();
        if ($(this).text() == Drupal.t('-Open all-')) {
          $(this).text(Drupal.t('-Close all-'));
          $('details').attr('open', '');
        }
        else {
          $(this).text(Drupal.t('-Open all-'));
          $('details').removeAttr('open');
        }
      });

      // Toggle Open/Close text when the top level details element is opened or
      // closed via its native handler.
      $(context).find('.automation-debug-log').once('automation-debug-log-details').click(function (event) {
        if (typeof( $(this).parent().attr('open') ) == 'undefined') {
          $('.automation-debug-open-all').text(Drupal.t('-Close all-'));
        }
        else {
          $('.automation-debug-open-all').text(Drupal.t('-Open all-'));
        }
      });
    }
  };

})(jQuery, Drupal);
