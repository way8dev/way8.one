(function (Drupal, once, $) {
  Drupal.behaviors.dboptimize = {
    attach(context) {
      once('dboptimize-select-all', '.dboptimize-select-all', context).forEach((el) => {
        el.addEventListener('click', (e) => {
          e.preventDefault();
          context.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
        });
      });

      once('dboptimize-clear', '.dboptimize-clear', context).forEach((el) => {
        el.addEventListener('click', (e) => {
          e.preventDefault();
          context.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        });
      });

      once('dboptimize-toggle', '.dboptimize-toggle', context).forEach((el) => {
        el.addEventListener('click', (e) => {
          e.preventDefault();
          context.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = !cb.checked);
        });
      });


      const $filterInput = $(context).find('.dboptimize-filter');
      const checkboxes = $(context).find('.dboptimize-table tbody [type="checkbox"]');

      $filterInput.on('input', function (e) {
        e.preventDefault();

        const search = $(this).val().toLowerCase();
        checkboxes.each(function () {
          const $checkbox = $(this);
          const labelText = $checkbox.val().toLowerCase();

          if (labelText.includes(search)) {
            $checkbox.closest('tr').show();
          } else {
            $checkbox.closest('tr').hide();
          }
        });
      });

    }
  };
})(Drupal, once, jQuery);
