(function ($, Drupal) {
  // Resolve a função once compatível com a versão do core.
  var onceUtil = (typeof once !== 'undefined') ? once : (typeof $.fn.once === 'function' ? function (key, selector, context) {
    // wrapper que emula API do once: retorna array de elements
    var $items = $(context).find(selector).once(key);
    var arr = [];
    $items.each(function () { arr.push(this); });
    return arr;
  } : null);

  Drupal.behaviors.dboptimizeSettingsButtons = {
    attach: function (context) {
      if (!onceUtil) {
        // fallback simples: bind diretamente (podendo duplicar handlers em AJAX).
        $(context).find('details').each(function () {
          var $fieldset = $(this);
          var tableSelectors = [
            "input[name^='optimize[cron_tables]']",
            "input[name^='analyze[cron_tables_analyze]']",
            "input[name^='repair[cron_tables_repair]']",
            "input[name^='check[cron_tables_check]']",
            "input[name*='cron_tables']"
          ];
          var selector = null;
          for (var i = 0; i < tableSelectors.length; i++) {
            if ($fieldset.find(tableSelectors[i]).length) {
              selector = tableSelectors[i];
              break;
            }
          }
          if (!selector) {
            return;
          }
          function $tableCheckboxes() {
            return $fieldset.find(selector).not(':disabled');
          }
          $fieldset.find('.dboptimize-select-all').off('click.dboptimize').on('click.dboptimize', function (e) {
            e.preventDefault();
            $tableCheckboxes().prop('checked', true).trigger('change');
          });
          $fieldset.find('.dboptimize-toggle').off('click.dboptimize').on('click.dboptimize', function (e) {
            e.preventDefault();
            $tableCheckboxes().each(function () {
              $(this).prop('checked', !$(this).prop('checked')).trigger('change');
            });
          });
          $fieldset.find('.dboptimize-clear').off('click.dboptimize').on('click.dboptimize', function (e) {
            e.preventDefault();
            $tableCheckboxes().prop('checked', false).trigger('change');
          });
        });
        return;
      }

      onceUtil('dboptimize-buttons', 'details', context).forEach(function (el) {
        var $fieldset = $(el);

        var tableSelectors = [
          "input[name^='optimize[cron_tables]']",
          "input[name^='analyze[cron_tables_analyze]']",
          "input[name^='repair[cron_tables_repair]']",
          "input[name^='check[cron_tables_check]']",
          "input[name*='cron_tables']"
        ];
        var selector = null;
        for (var i = 0; i < tableSelectors.length; i++) {
          if ($fieldset.find(tableSelectors[i]).length) {
            selector = tableSelectors[i];
            break;
          }
        }
        if (!selector) {
          return;
        }

        function $tableCheckboxes() {
          return $fieldset.find(selector).not(':disabled');
        }

        $fieldset.find('.dboptimize-select-all').on('click', function (e) {
          e.preventDefault();
          $tableCheckboxes().prop('checked', true).trigger('change');
        });
        $fieldset.find('.dboptimize-toggle').on('click', function (e) {
          e.preventDefault();
          $tableCheckboxes().each(function () {
            $(this).prop('checked', !$(this).prop('checked')).trigger('change');
          });
        });
        $fieldset.find('.dboptimize-clear').on('click', function (e) {
          e.preventDefault();
          $tableCheckboxes().prop('checked', false).trigger('change');
        });
      });
    }
  };
})(jQuery, Drupal);
