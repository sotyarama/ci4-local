<?php

use CodeIgniter\HTTP\IncomingRequest;

if (! function_exists('tr_parse_range_dateonly')) {
    /**
     * Parse a date-only range from request and return normalized values.
     * Defaults and semantics mirror SalesSummary::byTime() date handling.
     *
     * @param IncomingRequest $request
     * @param array $opts ['timezone' => 'Asia/Jakarta', 'maxDays' => 366]
     * @return array
     */
    function tr_parse_range_dateonly(IncomingRequest $request, array $opts = []): array
    {
        $tz = $opts['timezone'] ?? 'Asia/Jakarta';
        $maxDays = (int) ($opts['maxDays'] ?? 366);

        $startParam = $request->getGet('start') ?? $request->getGet('date_from');
        $endParam   = $request->getGet('end') ?? $request->getGet('date_to');
        $allDayRaw  = $request->getGet('allday');
        $allDay     = ($allDayRaw === '0') ? false : true; // default true for date-only

        $today = new \DateTime('now', new \DateTimeZone($tz));
        $defaultStart = (clone $today)->modify('first day of this month')->format('Y-m-d');
        $defaultEnd   = $today->format('Y-m-d');

        $startDate = $startParam ?: $defaultStart;
        $endDate   = $endParam   ?: $defaultEnd;

        $startObj = \DateTime::createFromFormat('Y-m-d', $startDate, new \DateTimeZone($tz)) ?: new \DateTime($defaultStart, new \DateTimeZone($tz));
        $endObj   = \DateTime::createFromFormat('Y-m-d', $endDate, new \DateTimeZone($tz))   ?: new \DateTime($defaultEnd, new \DateTimeZone($tz));

        $swapped = false;
        if ($startObj > $endObj) {
            $tmp = $startObj;
            $startObj = $endObj;
            $endObj = $tmp;
            $swapped = true;
        }

        // Clamp max range to $maxDays inclusive
        $diffDays = (int) $startObj->diff($endObj)->format('%a');
        $clamped = false;
        if ($diffDays + 1 > $maxDays) {
            $endObj = (clone $startObj)->modify('+' . ($maxDays - 1) . ' days');
            $clamped = true;
        }

        // Date-only semantics: allDay true, times fixed
        $startTime = '00:00';
        $endTime = '23:59';
        $fromDateTime = $startObj->format('Y-m-d') . ' 00:00:00';
        $toDateTime   = $endObj->format('Y-m-d') . ' 23:59:59';

        $rangeDays = (int) ($startObj->diff($endObj)->format('%a')) + 1;

        return [
            'startDate' => $startObj->format('Y-m-d'),
            'endDate'   => $endObj->format('Y-m-d'),
            'allDay'    => true,
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'fromDateTime' => $fromDateTime,
            'toDateTime'   => $toDateTime,
            'rangeDays' => $rangeDays,
            'clamped'   => $clamped,
            'swapped'   => $swapped,
            'original'  => [
                'start' => $request->getGet('start'),
                'end'   => $request->getGet('end'),
                'date_from' => $request->getGet('date_from'),
                'date_to'   => $request->getGet('date_to'),
                'allday'    => $request->getGet('allday'),
                'start_time' => $request->getGet('start_time'),
                'end_time'  => $request->getGet('end_time'),
            ],
        ];
    }
}

if (! function_exists('tr_parse_range_datetime')) {
    /**
     * Parse a datetime range from request and return normalized values.
     * Mirrors SalesSummary::byTime() semantics exactly.
     *
     * @param IncomingRequest $request
     * @param array $opts ['timezone' => 'Asia/Jakarta', 'maxDays' => 366]
     * @return array
     */
    function tr_parse_range_datetime(IncomingRequest $request, array $opts = []): array
    {
        $tz = $opts['timezone'] ?? 'Asia/Jakarta';
        $maxDays = (int) ($opts['maxDays'] ?? 366);

        $startParam = $request->getGet('start') ?? $request->getGet('date_from');
        $endParam   = $request->getGet('end') ?? $request->getGet('date_to');
        $allDayRaw  = $request->getGet('allday');
        $allDay     = ($allDayRaw === '0') ? false : true; // default true

        $startTimeRaw = $request->getGet('start_time');
        $endTimeRaw   = $request->getGet('end_time');

        // sanitize times to HH:MM
        $sanitizeTime = function ($t, $fallback) {
            if (! $t) return $fallback;
            // accept HH:MM or H:MM
            if (preg_match('/^(\d{1,2}):(\d{2})$/', $t, $m)) {
                $h = (int) $m[1];
                $i = (int) $m[2];
                if ($h < 0) $h = 0;
                if ($h > 23) $h = 23;
                if ($i < 0) $i = 0;
                if ($i > 59) $i = 59;
                return sprintf('%02d:%02d', $h, $i);
            }
            return $fallback;
        };

        $startTime = $sanitizeTime($startTimeRaw, '00:00');
        $endTime   = $sanitizeTime($endTimeRaw, '23:59');

        $today = new \DateTime('now', new \DateTimeZone($tz));
        $defaultStart = (clone $today)->modify('first day of this month')->format('Y-m-d');
        $defaultEnd   = $today->format('Y-m-d');

        $startDate = $startParam ?: $defaultStart;
        $endDate   = $endParam   ?: $defaultEnd;

        $startObj = \DateTime::createFromFormat('Y-m-d', $startDate, new \DateTimeZone($tz)) ?: new \DateTime($defaultStart, new \DateTimeZone($tz));
        $endObj   = \DateTime::createFromFormat('Y-m-d', $endDate, new \DateTimeZone($tz))   ?: new \DateTime($defaultEnd, new \DateTimeZone($tz));

        $swapped = false;
        if ($startObj > $endObj) {
            $tmp = $startObj;
            $startObj = $endObj;
            $endObj = $tmp;
            $swapped = true;
        }

        // Clamp max range to $maxDays inclusive
        $diffDays = (int) $startObj->diff($endObj)->format('%a');
        $clamped = false;
        if ($diffDays + 1 > $maxDays) {
            $endObj = (clone $startObj)->modify('+' . ($maxDays - 1) . ' days');
            $clamped = true;
        }

        if ($allDay) {
            $fromDateTime = $startObj->format('Y-m-d') . ' 00:00:00';
            $toDateTime   = $endObj->format('Y-m-d') . ' 23:59:59';
        } else {
            $fromDateTime = $startObj->format('Y-m-d') . ' ' . $startTime . ':00';
            $toDateTime   = $endObj->format('Y-m-d') . ' ' . $endTime . ':59';
        }

        $rangeDays = (int) ($startObj->diff($endObj)->format('%a')) + 1;

        return [
            'startDate' => $startObj->format('Y-m-d'),
            'endDate'   => $endObj->format('Y-m-d'),
            'allDay'    => $allDay,
            'startTime' => $startTime,
            'endTime'   => $endTime,
            'fromDateTime' => $fromDateTime,
            'toDateTime'   => $toDateTime,
            'rangeDays' => $rangeDays,
            'clamped'   => $clamped,
            'swapped'   => $swapped,
            'original'  => [
                'start' => $request->getGet('start'),
                'end'   => $request->getGet('end'),
                'date_from' => $request->getGet('date_from'),
                'date_to'   => $request->getGet('date_to'),
                'allday'    => $request->getGet('allday'),
                'start_time' => $request->getGet('start_time'),
                'end_time'  => $request->getGet('end_time'),
            ],
        ];
    }
}
