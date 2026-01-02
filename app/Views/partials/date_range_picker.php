<form id="filter-form" method="get" action="<?= current_url(); ?>" style="margin-bottom:12px;">
    <?php
    // Defensive defaults — ensure the partial is safe when included anywhere
    $mode = $mode ?? 'datetime';
    // prefer controller-provided $dateFrom/$dateTo if present
    $startDate = $startDate ?? ($dateFrom ?? date('Y-m-01'));
    $endDate   = $endDate ?? ($dateTo ?? date('Y-m-d'));
    $allDay    = isset($allDay) ? (bool) $allDay : true;
    $startTime = $startTime ?? '00:00';
    $endTime   = $endTime ?? '23:59';
    // per-page: prefer explicit $perPage, then request GET param, else 20
    $perPage   = $perPage ?? (int) (request()->getGet('per_page') ?? 20);
    ?>
    <?php if ($mode === 'date'): ?>
        <?php $hiddenAllday = '1';
        $hiddenStartTime = '00:00';
        $hiddenEndTime = '23:59'; ?>
    <?php else: ?>
        <?php $hiddenAllday = $allDay ? '1' : '0';
        $hiddenStartTime = esc($startTime);
        $hiddenEndTime = esc($endTime); ?>
    <?php endif; ?>

    <input type="hidden" name="start" id="input-start" value="<?= esc($startDate); ?>">
    <input type="hidden" name="end" id="input-end" value="<?= esc($endDate); ?>">
    <input type="hidden" name="allday" id="input-allday" value="<?= $hiddenAllday; ?>">
    <input type="hidden" name="start_time" id="input-start-time" value="<?= $hiddenStartTime; ?>">
    <input type="hidden" name="end_time" id="input-end-time" value="<?= $hiddenEndTime; ?>">

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
            <?php if (($mode ?? 'datetime') !== 'date'): ?>
                <div style="display:flex; flex-direction:column; font-size:12px;">
                    <label for="per_page" style="margin-bottom:2px; color:var(--tr-muted-text);">Baris per halaman</label>
                    <select name="per_page" id="per_page"
                        style="min-width:120px; padding:5px 8px; border-radius:6px; border:1px solid var(--tr-border); background:var(--tr-bg); color:var(--tr-text); font-size:12px;">
                        <?php foreach ([20, 50, 100, 200] as $opt): ?>
                            <option value="<?= $opt; ?>" <?= ((int)$perPage === $opt) ? 'selected' : ''; ?>><?= $opt; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

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

            <?php if ($mode !== 'date'): ?>
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
            <?php endif; ?>

            <div style="display:flex; justify-content:flex-end; gap:8px;">
                <button type="button" id="picker-cancel" style="padding:8px 12px; border-radius:8px; border:1px solid var(--tr-border); background:var(--tr-secondary-beige); color:var(--tr-text); cursor:pointer;">Batal</button>
                <button type="button" id="picker-apply" style="padding:8px 12px; border-radius:8px; border:1px solid var(--tr-primary); background:var(--tr-primary); color:#fff; cursor:pointer;">Apply</button>
            </div>
        </div>
    </div>
</div>