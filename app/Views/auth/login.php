<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title ?? 'Login'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #020617;
            color: #e5e7eb;
        }
        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }
        .card {
            background: #020617;
            border-radius: 16px;
            padding: 24px 24px 20px;
            border: 1px solid #111827;
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
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
            color: #9ca3af;
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
            border: 1px solid #1f2937;
            background: #020617;
            color: #e5e7eb;
            font-size: 13px;
        }
        input:focus {
            outline: 1px solid #3b82f6;
            border-color: #3b82f6;
        }
        .field {
            margin-bottom: 12px;
        }
        .btn {
            width: 100%;
            padding: 9px 10px;
            border-radius: 999px;
            border: none;
            background: #3b82f6;
            color: white;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
        }
        .btn:hover {
            background: #2563eb;
        }
        .hint {
            margin-top: 10px;
            font-size: 11px;
            color: #9ca3af;
        }
        .alert {
            background: #7f1d1d;
            border-radius: 8px;
            padding: 8px 10px;
            border: 1px solid #b91c1c;
            font-size: 12px;
            margin-bottom: 12px;
            color: #fee2e2;
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

            <button type="submit" class="btn">Masuk</button>
        </form>

        <div class="hint">
            User awal: <code>owner / owner123</code><br>
            (Silakan ganti nanti dari modul User Management.)
        </div>
    </div>
</div>
</body>
</html>
