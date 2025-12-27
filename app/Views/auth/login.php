<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="tr-auth-wrap">
    <div class="tr-auth-card tr-auth-split">

        <!-- LEFT: BRAND -->
        <div class="tr-auth-left">
            <img class="tr-auth-logo" src="<?= base_url('images/temurasa_primary_fit.png'); ?>" alt="Temu Rasa">
            <h1 class="tr-auth-title">Temu Rasa Cafe</h1>
            <p class="tr-auth-subtitle">
                <?= esc($subtitle ?? 'Silakan masuk ke sistem POS'); ?>
            </p>
        </div>

        <!-- RIGHT: FORM -->
        <div class="tr-auth-right">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="tr-auth-alert">
                    <?= esc(session()->getFlashdata('error')); ?>
                </div>
            <?php endif; ?>

            <form class="tr-auth-form" action="<?= site_url('login/attempt'); ?>" method="post">
                <?= csrf_field(); ?>

                <div class="tr-auth-field">
                    <label for="username">Username</label>
                    <input
                        class="tr-auth-input"
                        type="text"
                        id="username"
                        name="username"
                        value="<?= esc(old('username') ?? ''); ?>"
                        autocomplete="username"
                        required>
                </div>

                <div class="tr-auth-field">
                    <label for="password">Password</label>
                    <input
                        class="tr-auth-input"
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        required>
                </div>

                <div class="tr-auth-actions">
                    <button type="submit" class="btn btn-primary">Masuk</button>
                </div>
            </form>

            <div class="tr-auth-hint">
                (Untuk user baru silahkan set password melalui "Lupa password")<br>
                <a href="<?= site_url('auth/forgot'); ?>">Lupa password?</a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>