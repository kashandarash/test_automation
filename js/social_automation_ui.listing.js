/**
 * @file
 * Automation list builders search behavior.
 */

(function ($, Drupal) {
  /**
   * Filters the automation list builder tables by a text input search string.
   *
   * Text search input: input.automation-filter-text
   * Target table:      input.automation-filter-text[data-table]
   * Source text:       [data-drupal-selector="automation-table-filter-text-source"]
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the filter functionality to the automation admin text search field.
   */
  Drupal.behaviors.automationTableFilterByText = {
    attach: function attach(context, settings) {
      var $input = $('input.automation-filter-text').once('automation-filter-text');
      var $table = $($input.attr('data-table'));
      var $rows = void 0;

      function filterViewList(e) {
        var query = $(e.target).val().toLowerCase();

        function showViewRow(index, row) {
          var $row = $(row);
          var $sources = $row.find('[data-drupal-selector="automation-table-filter-text-source"]');
          var textMatch = $sources.text().toLowerCase().indexOf(query) !== -1;
          $row.closest('tr').toggle(textMatch);
        }

        // Filter if the length of the query is at least 2 characters.
        if (query.length >= 2) {
          $rows.each(showViewRow);
        } else {
          $rows.show();
        }
      }

      if ($table.length) {
        $rows = $table.find('tbody tr');
        $input.on('keyup', filterViewList);
      }
    }
  };
})(jQuery, Drupal);
