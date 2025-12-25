(function ($) {
  $(function () {
    if (typeof $.fn.DataTable !== 'function') return;

    const $table = $('#stockMovementsTable');
    if (!$table.length || $.fn.DataTable.isDataTable($table)) return;

    const dt = $table.DataTable({
      pageLength: 10,
      order: [[0, 'desc']],
      responsive: false,
      dom: 'lrtip',
    });

    const $search = $('#mov-filter');
    if ($search.length) {
      $search.on('input', function () {
        dt.search(this.value).draw();
      });
    }
  });
})(jQuery);
