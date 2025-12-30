<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        @page { margin: 24px; }
        body { font-family: "DejaVu Sans", Arial, sans-serif; font-size: 12px; color: #111; }
        img { max-width: 100%; height: auto; }
    </style>
    <?php if (! empty($extraCss)): ?>
        <style>
            <?= $extraCss ?>
        </style>
    <?php endif; ?>
</head>
<body>
    <?= $this->renderSection('content') ?>
</body>
</html>
