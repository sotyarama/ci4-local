<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px;">
        <div>
            <h2 style="margin:0; font-size:18px;"><?= esc($title); ?></h2>
            <p style="margin:2px 0 0; font-size:12px; color:var(--tr-muted-text);">
                <?= esc($subtitle); ?>
            </p>
        </div>
        <?php
            $csvParams = [
                'start'      => $startDate,
                'end'        => $endDate,
                'allday'     => $allDay ? 1 : 0,
                'start_time' => $startTime,
                'end_time'   => $endTime,
                'group'      => $group,
                'export'     => 'csv',
            ];
            $csvQuery = http_build_query(array_filter($csvParams, function($v) {
                return $v !== null && $v !== '';
            }));
            $csvUrl = current_url() . ($csvQuery ? '?' . $csvQuery : '');
        ?>
        <a href="<?= $csvUrl; ?>"
           style="font-size:11px; padding:6px 10px; border-radius:999px; border:1px solid var(--tr-muted-text); background:var(--tr-border); color:var(--tr-text); text-decoration:none;">
            Export CSV
        </a>
    </div>

    <!-- Range Bar ala Moka -->
    <form id="filter-form" method="get" action="<?= current_url(); ?>" style="margin-bottom:12px;">
        <input type="hidden" name="start" id="input-start" value="<?= esc($startDate); ?>">
        <input type="hidden" name="end" id="input-end" value="<?= esc($endDate); ?>">
        <input type="hidden" name="allday" id="input-allday" value="<?= $allDay ? '1' : '0'; ?>">
        <input type="hidden" name="start_time" id="input-start-time" value="<?= esc($startTime); ?>">
        <input type="hidden" name="end_time" id="input-end-time" value="<?= esc($endTime); ?>">

        <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px; padding:8px 10px; border:1px solid var(--tr-border); border-radius:10px; background:#fff; box-shadow:0 4px 12px rgba(0,0,0,0.04);">
                <button type="button" id="range-prev" style="border:none; background:var(--tr-border); border-radius:8px; width:28px; height:28px; cursor:pointer;">‹</button>
                <div id="range-label" style="font-size:12px; color:var(--tr-text); min-width:200px;">
                    <!-- filled by JS -->
                </div>
                <button type="button" id="range-next" style="border:none; background:var(--tr-border); border-radius:8px; width:28px; height:28px; cursor:pointer;">›</button>
                <button type="button" id="open-picker" style="margin-left:6px; padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text); cursor:pointer;">
                    Pilih Range
                </button>
            </div>

            <div style="display:flex; gap:10px; align-items:center;">
                <div style="display:flex; flex-direction:column; font-size:12px;">
                    <label for="per_page" style="margin-bottom:2px; color:var(--tr-muted-text);">Baris per halaman</label>
                    <select name="per_page" id="per_page"
                            style="min-width:120px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                        <?php foreach ([20, 50, 100, 200] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= ((int)$perPage === $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="display:flex; gap:6px; align-items:flex-end; padding-top:14px;">
                    <button type="submit"
                            style="padding:6px 10px; border-radius:999px; border:none; font-size:12px; background:var(--tr-primary); color:#fff; cursor:pointer;">
                        Terapkan Filter
                    </button>
                    <a href="<?= current_url(); ?>" style="font-size:12px; color:var(--tr-muted-text); text-decoration:none;">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <!-- Filter -->
    <!-- Picker Modal -->
    <div id="picker-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.35); z-index:9999; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:12px; padding:16px; width:100%; max-width:720px; box-shadow:0 18px 48px rgba(0,0,0,0.18); display:grid; grid-template-columns:180px 1fr; gap:12px;">
            <div>
                <div style="font-weight:700; font-size:13px; margin-bottom:8px; color:var(--tr-text);">Preset</div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    <?php
                        $presets = [
                            'today'      => 'Today',
                            'yesterday'  => 'Yesterday',
                            'this_week'  => 'This Week',
                            'last_week'  => 'Last Week',
                            'this_month' => 'This Month',
                            'last_month' => 'Last Month',
                            'this_year'  => 'This Year',
                            'last_year'  => 'Last Year',
                        ];
                    ?>
                    <?php foreach ($presets as $key => $label): ?>
                        <button type="button" class="btn btn-secondary preset-btn"
                                data-preset="<?= $key; ?>"
                                style="width:100%; justify-content:flex-start; padding:8px 10px; border-radius:8px; font-size:12px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text);">
                            <?= $label; ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
                    <div style="font-size:13px; font-weight:700; color:var(--tr-text);">Pilih Range</div>
                    <button type="button" id="picker-close" style="border:none; background:transparent; font-size:16px; cursor:pointer; color:var(--tr-muted-text);">×</button>
                </div>
                <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; margin-bottom:10px;">
                    <div style="display:flex; flex-direction:column; font-size:12px;">
                        <label style="margin-bottom:4px; color:var(--tr-muted-text);">Start Date</label>
                        <input type="date" id="picker-start" value="<?= esc($startDate); ?>" style="padding:8px 10px; border:1px solid var(--tr-border); border-radius:8px;">
                    </div>
                    <div style="display:flex; flex-direction:column; font-size:12px;">
                        <label style="margin-bottom:4px; color:var(--tr-muted-text);">End Date</label>
                        <input type="date" id="picker-end" value="<?= esc($endDate); ?>" style="padding:8px 10px; border:1px solid var(--tr-border); border-radius:8px;">
                    </div>
                </div>

                <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                    <div style="font-size:12px; color:var(--tr-text); font-weight:600;">All day</div>
                    <label style="position:relative; display:inline-block; width:40px; height:22px;">
                        <input type="checkbox" id="picker-allday" <?= $allDay ? 'checked' : ''; ?> style="opacity:0; width:0; height:0;">
                        <span style="position:absolute; cursor:pointer; inset:0; background:<?= $allDay ? 'var(--tr-primary)' : 'var(--tr-border)'; ?>; border-radius:999px; transition:all 0.2s;">
                            <span id="allday-knob" style="position:absolute; top:2px; left:<?= $allDay ? '20px' : '2px'; ?>; width:18px; height:18px; border-radius:50%; background:#fff; transition:all 0.2s;"></span>
                        </span>
                    </label>
                </div>

                <div id="time-row" style="display:<?= $allDay ? 'none' : 'grid'; ?>; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; margin-bottom:10px;">
                    <div style="display:flex; flex-direction:column; font-size:12px;">
                        <label style="margin-bottom:4px; color:var(--tr-muted-text);">Start Time</label>
                        <input type="time" id="picker-start-time" value="<?= esc($startTime); ?>" style="padding:8px 10px; border:1px solid var(--tr-border); border-radius:8px;">
                    </div>
                    <div style="display:flex; flex-direction:column; font-size:12px;">
                        <label style="margin-bottom:4px; color:var(--tr-muted-text);">End Time</label>
                        <input type="time" id="picker-end-time" value="<?= esc($endTime); ?>" style="padding:8px 10px; border:1px solid var(--tr-border); border-radius:8px;">
                    </div>
                </div>

                <div style="display:flex; justify-content:flex-end; gap:8px;">
                    <button type="button" id="picker-cancel" style="padding:8px 12px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text); cursor:pointer;">Batal</button>
                    <button type="button" id="picker-apply" style="padding:8px 12px; border-radius:8px; border:1px solid var(--tr-primary); background:var(--tr-primary); color:#fff; cursor:pointer;">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <p style="font-size:12px; color:var(--tr-muted-text);">Tidak ada data untuk filter ini.</p>
    <?php else: ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <div style="font-size:12px; color:var(--tr-muted-text);">Filter periode (mis. 2025-12 atau 2025-W50 atau tanggal):</div>
            <input type="text" id="time-filter" placeholder="Cari periode..." style="padding:6px 8px; font-size:12px; border:1px solid var(--tr-border); border-radius:8px; background:var(--tr-bg); color:var(--tr-text); min-width:200px;">
        </div>
        <table style="width:100%; border-collapse:collapse; font-size:12px;">
            <thead>
                <tr>
                    <th style="text-align:left; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Periode</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total Penjualan</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Total HPP</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin</th>
                    <th style="text-align:right; padding:6px 8px; border-bottom:1px solid var(--tr-border);">Margin %</th>
                </tr>
            </thead>
            <tbody id="time-table-body">
                <?php foreach ($rows as $r): ?>
                    <?php
                        $sales = (float) ($r['total_sales'] ?? 0);
                        $cost  = (float) ($r['total_cost'] ?? 0);
                        $margin = $sales - $cost;
                        $marginPct = $sales > 0 ? ($margin / $sales * 100.0) : 0;
                        $marginColor = $margin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                    ?>
                    <tr data-period="<?= esc(strtolower($r['period'] ?? '')); ?>">
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border);">
                            <?= esc($r['period'] ?? ''); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            Rp <?= number_format($sales, 0, ',', '.'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right;">
                            Rp <?= number_format($cost, 0, ',', '.'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                            Rp <?= number_format($margin, 0, ',', '.'); ?>
                        </td>
                        <td style="padding:6px 8px; border-bottom:1px solid var(--tr-border); text-align:right; color:<?= $marginColor; ?>;">
                            <?= number_format($marginPct, 1, ',', '.'); ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php
                    $grandSales = (float) $totalSalesAll;
                    $grandCost  = (float) $totalCostAll;
                    $grandMargin = $grandSales - $grandCost;
                    $grandMarginPct = $grandSales > 0 ? ($grandMargin / $grandSales * 100.0) : 0;
                    $grandColor = $grandMargin >= 0 ? 'var(--tr-primary-deep)' : 'var(--tr-accent-brown)';
                ?>
                <tr>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); font-weight:bold;">
                        TOTAL (filter)
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                        Rp <?= number_format($grandSales, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold;">
                        Rp <?= number_format($grandCost, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>">
                        Rp <?= number_format($grandMargin, 0, ',', '.'); ?>
                    </td>
                    <td style="padding:6px 8px; border-top:1px solid var(--tr-muted-text); text-align:right; font-weight:bold; color:<?= $grandColor; ?>">
                        <?= number_format($grandMarginPct, 1, ',', '.'); ?>%
                    </td>
                </tr>
                <tr id="time-noresult" style="display:none;">
                    <td colspan="5" style="padding:8px; text-align:center; color:var(--tr-muted-text);">Tidak ada hasil.</td>
                </tr>
            </tbody>
        </table>

        <?php
            $queryBase = [
                'start'     => $startDate,
                'end'       => $endDate,
                'allday'    => $allDay ? 1 : 0,
                'start_time'=> $startTime,
                'end_time'  => $endTime,
                'per_page'  => $perPage,
                'group'     => $group,
            ];
            $buildUrl = static function(int $targetPage) use ($queryBase): string {
                $params = array_merge($queryBase, ['page' => $targetPage]);
                $params = array_filter($params, static function($v) {
                    return $v !== null && $v !== '';
                });
                $qs = http_build_query($params);
                return current_url() . ($qs ? '?' . $qs : '');
            };
            $startRow = ($page - 1) * $perPage + 1;
            $endRow   = min($startRow + $perPage - 1, $totalRows);
        ?>
        <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--tr-muted-text);">
            <div>
                <?= $totalRows > 0
                    ? "Menampilkan {$startRow}-{$endRow} dari {$totalRows} periode"
                    : "Tidak ada data untuk filter ini"; ?>
            </div>
            <div style="display:flex; gap:6px;">
                <a href="<?= $buildUrl(max(1, $page - 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page > 1 ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page > 1 ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page > 1 ? 'auto' : 'none'; ?>;">
                    ƒ?û Prev
                </a>
                <span style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text);">
                    Halaman <?= $page; ?> / <?= max(1, $totalPages); ?>
                </span>
                <a href="<?= $buildUrl(min($totalPages, $page + 1)); ?>"
                   style="padding:6px 10px; border-radius:8px; border:1px solid var(--tr-border); background:<?= $page < $totalPages ? 'var(--tr-border)' : 'var(--tr-secondary-beige)'; ?>; color:<?= $page < $totalPages ? 'var(--tr-text)' : 'var(--tr-muted-text)'; ?>; text-decoration:none; pointer-events:<?= $page < $totalPages ? 'auto' : 'none'; ?>;">
                    Next ƒ?§
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function() {
        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return y + '-' + m + '-' + d;
        }

        function startOfWeek(date) { // Monday as first day
            const day = date.getDay(); // 0=Sun
            const diff = (day === 0 ? -6 : 1 - day);
            const res = new Date(date);
            res.setDate(date.getDate() + diff);
            return res;
        }

        function endOfWeek(date) { // Sunday as last day
            const start = startOfWeek(date);
            const res = new Date(start);
            res.setDate(start.getDate() + 6);
            return res;
        }

        function startOfMonth(date) {
            return new Date(date.getFullYear(), date.getMonth(), 1);
        }

        function endOfMonth(date) {
            return new Date(date.getFullYear(), date.getMonth() + 1, 0);
        }

        function startOfYear(date) {
            return new Date(date.getFullYear(), 0, 1);
        }

        function endOfYear(date) {
            return new Date(date.getFullYear(), 11, 31);
        }

        function formatDisplayRange(start, end) {
            const [y1, m1, d1] = start.split('-');
            const [y2, m2, d2] = end.split('-');
            return `${d1}/${m1}/${y1} - ${d2}/${m2}/${y2}`;
        }

        function applyPreset(key, startInput, endInput, pickerStart, pickerEnd) {
            const today = new Date();
            let start = today;
            let end   = today;

            switch (key) {
                case 'today':
                    start = end = today;
                    break;
                case 'yesterday':
                    start = end = new Date(today);
                    start.setDate(today.getDate() - 1);
                    break;
                case 'this_week':
                    start = startOfWeek(today);
                    end   = endOfWeek(today);
                    break;
                case 'last_week':
                    const lastWeekRef = new Date(today);
                    lastWeekRef.setDate(today.getDate() - 7);
                    start = startOfWeek(lastWeekRef);
                    end   = endOfWeek(lastWeekRef);
                    break;
                case 'this_month':
                    start = startOfMonth(today);
                    end   = endOfMonth(today);
                    break;
                case 'last_month':
                    const lastMonthRef = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    start = startOfMonth(lastMonthRef);
                    end   = endOfMonth(lastMonthRef);
                    break;
                case 'this_year':
                    start = startOfYear(today);
                    end   = endOfYear(today);
                    break;
                case 'last_year':
                    const lastYearRef = new Date(today.getFullYear() - 1, 0, 1);
                    start = startOfYear(lastYearRef);
                    end   = endOfYear(lastYearRef);
                    break;
                default:
                    start = end = today;
            }

            startInput.value = formatDate(start);
            endInput.value   = formatDate(end);
            if (pickerStart && pickerEnd) {
                pickerStart.value = startInput.value;
                pickerEnd.value   = endInput.value;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const form       = document.getElementById('filter-form');
            const startInput = document.getElementById('input-start');
            const endInput   = document.getElementById('input-end');
            const rangeLabel = document.getElementById('range-label');
            const pickerOverlay = document.getElementById('picker-overlay');
            const pickerStart = document.getElementById('picker-start');
            const pickerEnd   = document.getElementById('picker-end');
            const pickerStartTime = document.getElementById('picker-start-time');
            const pickerEndTime   = document.getElementById('picker-end-time');
            const pickerAllDayInput = document.getElementById('picker-allday');
            const timeRow = document.getElementById('time-row');
            const knob = document.getElementById('allday-knob');
            const track = pickerAllDayInput ? pickerAllDayInput.nextElementSibling : null;
            const filterInput = document.getElementById('time-filter');
            const tableRows = Array.from(document.querySelectorAll('#time-table-body tr[data-period]'));
            const noResultRow = document.getElementById('time-noresult');

            function refreshLabel() {
                rangeLabel.textContent = formatDisplayRange(startInput.value, endInput.value);
            }

            function toggleOverlay(show) {
                pickerOverlay.style.display = show ? 'flex' : 'none';
                if (show) {
                    pickerStart.value = startInput.value;
                    pickerEnd.value   = endInput.value;
                    pickerAllDayInput.checked = document.getElementById('input-allday').value === '1';
                    timeRow.style.display = pickerAllDayInput.checked ? 'none' : 'grid';
                    knob.style.left = pickerAllDayInput.checked ? '20px' : '2px';
                    if (track) {
                        track.style.background = pickerAllDayInput.checked ? 'var(--tr-primary)' : 'var(--tr-border)';
                    }
                    pickerStartTime.value = document.getElementById('input-start-time').value || '00:00';
                    pickerEndTime.value   = document.getElementById('input-end-time').value || '23:59';
                }
            }

            document.getElementById('open-picker').addEventListener('click', function() {
                toggleOverlay(true);
            });
            document.getElementById('picker-close').addEventListener('click', function() {
                toggleOverlay(false);
            });
            document.getElementById('picker-cancel').addEventListener('click', function() {
                toggleOverlay(false);
            });

            pickerOverlay.addEventListener('click', function(e) {
                if (e.target === pickerOverlay) {
                    toggleOverlay(false);
                }
            });

            document.querySelectorAll('.preset-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const key = btn.getAttribute('data-preset');
                    applyPreset(key, startInput, endInput, pickerStart, pickerEnd);
                    refreshLabel();
                });
            });

            pickerAllDayInput.addEventListener('change', function() {
                document.getElementById('input-allday').value = pickerAllDayInput.checked ? '1' : '0';
                timeRow.style.display = pickerAllDayInput.checked ? 'none' : 'grid';
                knob.style.left = pickerAllDayInput.checked ? '20px' : '2px';
                if (track) {
                    track.style.background = pickerAllDayInput.checked ? 'var(--tr-primary)' : 'var(--tr-border)';
                }
            });

            document.getElementById('picker-apply').addEventListener('click', function() {
                startInput.value = pickerStart.value;
                endInput.value   = pickerEnd.value;
                document.getElementById('input-allday').value = pickerAllDayInput.checked ? '1' : '0';
                if (!pickerAllDayInput.checked) {
                    document.getElementById('input-start-time').value = pickerStartTime.value || '00:00';
                    document.getElementById('input-end-time').value   = pickerEndTime.value || '23:59';
                } else {
                    document.getElementById('input-start-time').value = '00:00';
                    document.getElementById('input-end-time').value   = '23:59';
                }
                refreshLabel();
                form.submit();
            });

            document.getElementById('range-prev').addEventListener('click', function() {
                const startDate = new Date(startInput.value);
                const endDate   = new Date(endInput.value);
                const diffDays  = Math.round((endDate - startDate) / (1000*60*60*24)) + 1;
                startDate.setDate(startDate.getDate() - diffDays);
                endDate.setDate(endDate.getDate() - diffDays);
                startInput.value = formatDate(startDate);
                endInput.value   = formatDate(endDate);
                refreshLabel();
                form.submit();
            });

            document.getElementById('range-next').addEventListener('click', function() {
                const startDate = new Date(startInput.value);
                const endDate   = new Date(endInput.value);
                const diffDays  = Math.round((endDate - startDate) / (1000*60*60*24)) + 1;
                startDate.setDate(startDate.getDate() + diffDays);
                endDate.setDate(endDate.getDate() + diffDays);
                startInput.value = formatDate(startDate);
                endInput.value   = formatDate(endDate);
                refreshLabel();
                form.submit();
            });

            if (filterInput) {
                filterInput.addEventListener('input', function() {
                    const q = filterInput.value.trim().toLowerCase();
                    let shown = 0;
                    tableRows.forEach(function(tr) {
                        const period = (tr.getAttribute('data-period') || '').toLowerCase();
                        const match = period.indexOf(q) !== -1;
                        tr.style.display = match ? '' : 'none';
                        if (match) shown++;
                    });
                    if (noResultRow) {
                        noResultRow.style.display = shown === 0 ? '' : 'none';
                    }
                });
            }

            refreshLabel();
        });
    })();
</script>

<?= $this->endSection() ?>
