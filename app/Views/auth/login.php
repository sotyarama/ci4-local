<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Login'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= base_url('css/theme-temurasa.css'); ?>">

    <style>
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: var(--tr-bg);
            color: var(--tr-text);
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .card {
            border-radius: 16px;
            padding: 24px 24px 20px;
            border: 1px solid var(--tr-border);
            background: var(--tr-surface);
            box-shadow: var(--tr-shadow);
            max-width: 360px;
            width: 100%;
        }
        h1 {
            margin: 0 0 4px;
            font-size: 22px;
        }
        .subtitle {
            margin: 0 0 18px;
            font-size: 13px;
            color: var(--tr-muted-text);
        }
        label {
            display: block;
            font-size: 12px;
            margin-bottom: 4px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid var(--tr-border);
            background: #fff;
            color: var(--tr-text);
            font-size: 13px;
        }
        input:focus {
            outline: 2px solid rgba(122, 154, 108, 0.35);
            border-color: var(--tr-primary);
        }
        .field {
            margin-bottom: 12px;
        }
        .btn {
            width: 100%;
            font-size: 14px;
        }
        .hint {
            margin-top: 10px;
            font-size: 11px;
            color: var(--tr-muted-text);
        }
        .alert {
            background: var(--tr-secondary-beige);
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid var(--tr-accent-brown);
            font-size: 12px;
            margin-bottom: 12px;
            color: var(--tr-text);
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="card">
        <h1>Login</h1>
        <p class="subtitle"><?= esc($subtitle ?? ''); ?></p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert">
                <?= esc(session()->getFlashdata('error')); ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('login/attempt'); ?>" method="post">
            <?= csrf_field(); ?>

            <div class="field">
                <label for="username">Username</label>
                <input
                    type="text"
                    id="username"
                    name="username"
                    value="<?= esc(old('username') ?? ''); ?>"
                    autocomplete="username"
                    required
                >
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <div class="hint">
            User awal: <code>owner / owner123</code><br>
            (Silakan ganti nanti dari modul User Management.)<br>
            <a href="<?= site_url('auth/forgot'); ?>">Lupa password?</a>
        </div>
    </div>
</div>
</body>
</html>


