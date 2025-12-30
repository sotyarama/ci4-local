<?php
$assetVer = $assetVer ?? time();
$title = $title ?? 'Print View';
$extraStylesheets = $extraStylesheets ?? [];
$showPrintToolbar = $showPrintToolbar ?? true;
$forceLongPage = $forceLongPage ?? false;
$pageWidth = $pageWidth ?? '210mm';
$pageHeight = $pageHeight ?? '3500mm';
$pageMargin = $pageMargin ?? '12mm';
?>
<!doctype html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui-baseline.css') . '?v=' . $assetVer; ?>">
    <link rel="stylesheet" href="<?= base_url('css/ui/badges.css') . '?v=' . $assetVer; ?>">

    <?php if (is_array($extraStylesheets)): ?>
        <?php foreach ($extraStylesheets as $href): ?>
            <link rel="stylesheet" href="<?= esc($href); ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($forceLongPage): ?>
        <style id="print-page-size">
            @media print {
                @page {
                    size: <?= esc($pageWidth); ?> <?= esc($pageHeight); ?>;
                    margin: <?= esc($pageMargin); ?>;
                }
            }
        </style>
    <?php endif; ?>

    <style>
        body {
            background: #ffffff;
        }

        .print-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px;
        }

        .print-toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            margin-bottom: 12px;
        }

        .print-toolbar a,
        .print-toolbar button {
            border: 1px solid var(--tr-border);
            background: var(--tr-secondary-beige);
            color: var(--tr-text);
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            cursor: pointer;
            text-decoration: none;
        }

        .print-toolbar button {
            font-family: inherit;
        }

        @media print {
            @page {
                margin: <?= esc($pageMargin); ?>;
            }

            .no-print {
                display: none !important;
            }

            body {
                background: #ffffff;
            }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <?php if ($showPrintToolbar): ?>
            <div class="print-toolbar no-print">
                <a href="<?= esc($backUrl ?? 'javascript:history.back()'); ?>">Back</a>
                <button type="button" onclick="window.print()">Print / Save PDF</button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <?php if ($forceLongPage): ?>
        <script>
            (function() {
                function updatePageSize() {
                    var container = document.querySelector('.print-container');
                    var heightPx = container ? container.scrollHeight : document.documentElement.scrollHeight;
                    var heightIn = Math.max(11.69, heightPx / 96 + 2);
                    var styleEl = document.getElementById('print-page-size');
                    if (!styleEl) return;
                    styleEl.textContent =
                        '@media print {@page { size: <?= esc($pageWidth); ?> ' +
                        heightIn.toFixed(2) + 'in; margin: <?= esc($pageMargin); ?>; }}';
                }

                document.addEventListener('DOMContentLoaded', updatePageSize);
                window.addEventListener('beforeprint', updatePageSize);
            })();
        </script>
    <?php endif; ?>
</body>
</html>
