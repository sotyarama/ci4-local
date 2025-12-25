(function ($) {
  $(function () {
    if (typeof $.fn.DataTable !== 'function') return;

    const $table = $('#rawMaterialsTable');
    if (!$table.length || $.fn.DataTable.isDataTable($table)) return;

    const dt = $table.DataTable({
      pageLength: 10,
      order: [[1, 'asc']],
      responsive: false,
      dom: 'lrtip',
      columnDefs: [{ orderable: false, targets: [0, 9] }],
    });

    const $search = $('#rm-filter');
    if ($search.length) {
      $search.on('input', function () {
        dt.search(this.value).draw();
      });
    }

    function escapeHtml(value) {
      return String(value || '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
    }

    function getPrecision(rowEl) {
      const value = rowEl.getAttribute('data-precision');
      const parsed = parseInt(value || '0', 10);
      if (Number.isNaN(parsed) || parsed < 0) return 0;
      return Math.min(parsed, 3);
    }

    function renderVariants(items, precision) {
      if (!items.length) {
        return '<div style="padding:8px 12px; font-size:12px; color:var(--tr-muted-text);">Belum ada varian aktif.</div>';
      }

      const qtyFormat = new Intl.NumberFormat('id-ID', {
        minimumFractionDigits: precision,
        maximumFractionDigits: precision,
      });

      const rows = items
        .map((item) => {
          const brand = escapeHtml(item.brand_name || '-');
          const variant = escapeHtml(item.variant_name || '-');
          const sku = escapeHtml(item.sku_code || '-');
          const stock = qtyFormat.format(Number(item.current_stock || 0));
          const minStock = qtyFormat.format(Number(item.min_stock || 0));
          const status = item.is_active ? 'Aktif' : 'Nonaktif';
          const statusClass = item.is_active ? 'badge badge--active' : 'badge badge--inactive';

          return (
            '<tr>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">' + brand + '</td>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">' + variant + '</td>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">' + sku + '</td>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">' + stock + '</td>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">' + minStock + '</td>' +
            '<td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:center;">' +
            '<span class="' + statusClass + '">' + status + '</span>' +
            '</td>' +
            '</tr>'
          );
        })
        .join('');

      return (
        '<div style="padding:8px 12px;">' +
        '<div style="font-size:12px; color:var(--tr-muted-text); margin-bottom:6px;">Rincian Varian</div>' +
        '<div style="border:1px solid var(--tr-border); border-radius:8px; overflow:hidden; background:var(--tr-secondary-beige);">' +
        '<table style="width:100%; border-collapse:collapse; font-size:12px;">' +
        '<thead>' +
        '<tr>' +
        '<th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Brand</th>' +
        '<th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Varian</th>' +
        '<th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">SKU</th>' +
        '<th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Stok</th>' +
        '<th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Min</th>' +
        '<th style="text-align:center; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Status</th>' +
        '</tr>' +
        '</thead>' +
        '<tbody>' +
        rows +
        '</tbody>' +
        '</table>' +
        '</div>' +
        '</div>'
      );
    }

    function parseVariants(rowEl) {
      const raw = rowEl.getAttribute('data-variants') || '[]';
      try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
      } catch (err) {
        return [];
      }
    }

    $table.on('click', '.rm-toggle', function () {
      const $btn = $(this);
      const $tr = $btn.closest('tr');
      const row = dt.row($tr);
      if (!row.length) return;

      if (row.child.isShown()) {
        row.child.hide();
        $tr.removeClass('is-expanded');
        $btn.text('+').attr('aria-expanded', 'false');
        return;
      }

      const rowEl = $tr.get(0);
      const variants = parseVariants(rowEl);
      const precision = getPrecision(rowEl);
      row.child(renderVariants(variants, precision)).show();
      $tr.addClass('is-expanded');
      $btn.text('-').attr('aria-expanded', 'true');
    });
  });
})(jQuery);
