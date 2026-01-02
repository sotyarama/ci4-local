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
        let end = today;

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
                end = endOfWeek(today);
                break;
            case 'last_week':
                const lastWeekRef = new Date(today);
                lastWeekRef.setDate(today.getDate() - 7);
                start = startOfWeek(lastWeekRef);
                end = endOfWeek(lastWeekRef);
                break;
            case 'this_month':
                start = startOfMonth(today);
                end = endOfMonth(today);
                break;
            case 'last_month':
                const lastMonthRef = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                start = startOfMonth(lastMonthRef);
                end = endOfMonth(lastMonthRef);
                break;
            case 'this_year':
                start = startOfYear(today);
                end = endOfYear(today);
                break;
            case 'last_year':
                const lastYearRef = new Date(today.getFullYear() - 1, 0, 1);
                start = startOfYear(lastYearRef);
                end = endOfYear(lastYearRef);
                break;
            default:
                start = end = today;
        }

        startInput.value = formatDate(start);
        endInput.value = formatDate(end);
        if (pickerStart && pickerEnd) {
            pickerStart.value = startInput.value;
            pickerEnd.value = endInput.value;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('filter-form');
        const startInput = document.getElementById('input-start');
        const endInput = document.getElementById('input-end');
        const rangeLabel = document.getElementById('range-label');
        const pickerOverlay = document.getElementById('picker-overlay');
        const pickerStart = document.getElementById('picker-start');
        const pickerEnd = document.getElementById('picker-end');
        const pickerStartTime = document.getElementById('picker-start-time');
        const pickerEndTime = document.getElementById('picker-end-time');
        const pickerAllDayInput = document.getElementById('picker-allday');
        const timeRow = document.getElementById('time-row');
        const knob = document.getElementById('allday-knob');
        const track = pickerAllDayInput ? pickerAllDayInput.nextElementSibling : null;

        function refreshLabel() {
            if (rangeLabel && startInput && endInput) {
                rangeLabel.textContent = formatDisplayRange(startInput.value, endInput.value);
            }
        }

        function toggleOverlay(show) {
            if (!pickerOverlay) return;
            pickerOverlay.style.display = show ? 'flex' : 'none';
            if (show) {
                if (pickerStart && pickerEnd && startInput && endInput) {
                    pickerStart.value = startInput.value;
                    pickerEnd.value = endInput.value;
                }
                if (pickerAllDayInput) {
                    const inputAllday = document.getElementById('input-allday');
                    pickerAllDayInput.checked = inputAllday && inputAllday.value === '1';
                    if (timeRow) timeRow.style.display = pickerAllDayInput.checked ? 'none' : 'grid';
                    if (knob) knob.style.left = pickerAllDayInput.checked ? '20px' : '2px';
                    if (track) track.style.background = pickerAllDayInput.checked ? 'var(--tr-primary)' : 'var(--tr-border)';
                }
                if (pickerStartTime && pickerEndTime) {
                    const inStartTime = document.getElementById('input-start-time');
                    const inEndTime = document.getElementById('input-end-time');
                    pickerStartTime.value = inStartTime && inStartTime.value ? inStartTime.value : '00:00';
                    pickerEndTime.value = inEndTime && inEndTime.value ? inEndTime.value : '23:59';
                }
            }
        }

        const openBtn = document.getElementById('open-picker');
        if (openBtn) openBtn.addEventListener('click', function() { toggleOverlay(true); });
        const closeBtn = document.getElementById('picker-close');
        if (closeBtn) closeBtn.addEventListener('click', function() { toggleOverlay(false); });
        const cancelBtn = document.getElementById('picker-cancel');
        if (cancelBtn) cancelBtn.addEventListener('click', function() { toggleOverlay(false); });

        if (pickerOverlay) {
            pickerOverlay.addEventListener('click', function(e) {
                if (e.target === pickerOverlay) {
                    toggleOverlay(false);
                }
            });
        }

        document.querySelectorAll('.preset-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const key = btn.getAttribute('data-preset');
                applyPreset(key, startInput, endInput, pickerStart, pickerEnd);
                refreshLabel();
            });
        });

        if (pickerAllDayInput) {
            pickerAllDayInput.addEventListener('change', function() {
                const inAllday = document.getElementById('input-allday');
                if (inAllday) inAllday.value = pickerAllDayInput.checked ? '1' : '0';
                if (timeRow) timeRow.style.display = pickerAllDayInput.checked ? 'none' : 'grid';
                if (knob) knob.style.left = pickerAllDayInput.checked ? '20px' : '2px';
                if (track) track.style.background = pickerAllDayInput.checked ? 'var(--tr-primary)' : 'var(--tr-border)';
            });
        }

        const applyBtn = document.getElementById('picker-apply');
        if (applyBtn) applyBtn.addEventListener('click', function() {
            if (startInput && endInput && pickerStart && pickerEnd) {
                startInput.value = pickerStart.value;
                endInput.value = pickerEnd.value;
            }
            const inAllday = document.getElementById('input-allday');
            if (inAllday && pickerAllDayInput) inAllday.value = pickerAllDayInput.checked ? '1' : '0';
            if (pickerAllDayInput && !pickerAllDayInput.checked) {
                const inStartTime = document.getElementById('input-start-time');
                const inEndTime = document.getElementById('input-end-time');
                if (inStartTime) inStartTime.value = pickerStartTime.value || '00:00';
                if (inEndTime) inEndTime.value = pickerEndTime.value || '23:59';
            } else {
                const inStartTime = document.getElementById('input-start-time');
                const inEndTime = document.getElementById('input-end-time');
                if (inStartTime) inStartTime.value = '00:00';
                if (inEndTime) inEndTime.value = '23:59';
            }
            refreshLabel();
            if (form) form.submit();
        });

        const prevBtn = document.getElementById('range-prev');
        if (prevBtn) prevBtn.addEventListener('click', function() {
            if (!startInput || !endInput) return;
            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);
            const diffDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
            startDate.setDate(startDate.getDate() - diffDays);
            endDate.setDate(endDate.getDate() - diffDays);
            startInput.value = formatDate(startDate);
            endInput.value = formatDate(endDate);
            refreshLabel();
            if (form) form.submit();
        });

        const nextBtn = document.getElementById('range-next');
        if (nextBtn) nextBtn.addEventListener('click', function() {
            if (!startInput || !endInput) return;
            const startDate = new Date(startInput.value);
            const endDate = new Date(endInput.value);
            const diffDays = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24)) + 1;
            startDate.setDate(startDate.getDate() + diffDays);
            endDate.setDate(endDate.getDate() + diffDays);
            startInput.value = formatDate(startDate);
            endInput.value = formatDate(endDate);
            refreshLabel();
            if (form) form.submit();
        });

        refreshLabel();
    });
})();
