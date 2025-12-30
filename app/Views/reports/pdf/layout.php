<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px 24px 28px 24px; }
        body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 11px; color: #111; }
        h1 { font-size: 16px; margin: 0 0 4px; }
        .subtitle { font-size: 11px; color: #555; margin: 0 0 10px; }
        .meta { font-size: 10px; color: #666; margin-bottom: 12px; }
        .meta div { margin-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
        th { background: #f2f2f2; font-weight: 700; text-align: left; }
        td.num { text-align: right; }
        td.center { text-align: center; }
        tfoot td { font-weight: 700; border-top: 1px solid #999; }
        .note { font-size: 10px; color: #666; margin-top: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1><?= esc($title ?? ''); ?></h1>
        <?php if (! empty($subtitle)): ?>
            <div class="subtitle"><?= esc($subtitle); ?></div>
        <?php endif; ?>
        <?php if (! empty($metaLines) && is_array($metaLines)): ?>
            <div class="meta">
                <?php foreach ($metaLines as $line): ?>
                    <div><?= esc($line); ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <?= $this->renderSection('content') ?>
</body>
</html>
