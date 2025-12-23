<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Lupa Password'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css'); ?>">
    <style>
        body { margin:0; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; background: var(--tr-bg); color: var(--tr-text); }
        .wrapper { min-height: 100vh; display:flex; align-items:center; justify-content:center; padding:16px; }
        .card { border-radius:16px; padding:24px 24px 20px; border:1px solid var(--tr-border); background: var(--tr-surface); box-shadow: var(--tr-shadow); max-width:360px; width:100%; }
        h1 { margin:0 0 4px; font-size:22px; }
        .subtitle { margin:0 0 18px; font-size:13px; color: var(--tr-muted-text); }
        label { display:block; font-size:12px; margin-bottom:4px; }
        input[type="email"] { width:100%; padding:8px 10px; border-radius:8px; border:1px solid var(--tr-border); background:#fff; color: var(--tr-text); font-size:13px; }
        input:focus { outline:2px solid rgba(122,154,108,0.35); border-color: var(--tr-primary); }
        .field { margin-bottom:12px; }
        .btn { width:100%; font-size:14px; }
        .hint { margin-top:10px; font-size:11px; color: var(--tr-muted-text); }
        .alert { background: var(--tr-secondary-beige); border-radius:8px; padding:8px 10px; border:1px solid var(--tr-accent-brown); font-size:12px; margin-bottom:12px; color: var(--tr-text); }
        .message { background: #e6f1e7; border:1px solid #7a9a6c; border-radius:8px; padding:8px 10px; font-size:12px; margin-bottom:12px; color:#2f3a2f; }
        a { color: var(--tr-primary); text-decoration:none; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <h1>Lupa Password</h1>
        <p class="subtitle"><?= esc($subtitle ?? ''); ?></p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert"><?= esc(session()->getFlashdata('error')); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('message')): ?>
            <div class="message"><?= esc(session()->getFlashdata('message')); ?></div>
        <?php endif; ?>

        <form action="<?= site_url('auth/forgot'); ?>" method="post">
            <?= csrf_field(); ?>
            <div class="field">
                <label for="email">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?= esc(old('email') ?? ''); ?>"
                    autocomplete="email"
                    required
                >
            </div>
            <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
        </form>

        <div class="hint" style="margin-top:12px;">
            <a href="<?= site_url('login'); ?>">Kembali ke login</a>
        </div>
    </div>
</div>
</body>
</html>
