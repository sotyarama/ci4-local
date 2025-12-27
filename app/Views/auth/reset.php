<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="tr-auth-wrap">
    <div class="tr-auth-card tr-auth-split">

        <!-- LEFT: BRAND -->
        <div class="tr-auth-left">
            <img class="tr-auth-logo" src="<?= base_url('images/temurasa_primary_fit.png'); ?>" alt="Temu Rasa">
            <h1 class="tr-auth-title">Reset Password</h1>
            <p class="tr-auth-subtitle">
                <?= esc($subtitle ?? 'Buat password baru untuk akun Anda.'); ?>
            </p>
        </div>

        <!-- RIGHT: FORM -->
        <div class="tr-auth-right">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="tr-auth-alert">
                    <?= esc(session()->getFlashdata('error')); ?>
                </div>
            <?php endif; ?>

            <form class="tr-auth-form" action="<?= site_url('auth/reset'); ?>" method="post">
                <?= csrf_field(); ?>

                <!-- Hidden context (DO NOT CHANGE) -->
                <input type="hidden" name="token" value="<?= esc($token ?? ''); ?>">
                <input type="hidden" name="email" value="<?= esc($email ?? ''); ?>">

                <div class="tr-auth-field">
                    <label for="password">Password baru</label>
                    <input
                        class="tr-auth-input"
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
                        required>
                </div>

                <div class="tr-auth-field">
                    <label for="password_confirm">Konfirmasi password</label>
                    <input
                        class="tr-auth-input"
                        type="password"
                        id="password_confirm"
                        name="password_confirm"
                        autocomplete="new-password"
                        required>
                </div>

                <div class="tr-auth-actions">
                    <button type="submit" class="btn btn-primary">Simpan Password</button>
                </div>
            </form>

            <div class="tr-auth-hint">
                <a href="<?= site_url('login'); ?>">Kembali ke login</a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>