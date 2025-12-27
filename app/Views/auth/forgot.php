<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="tr-auth-wrap">
    <div class="tr-auth-card tr-auth-split">

        <!-- LEFT: BRAND -->
        <div class="tr-auth-left">
            <img class="tr-auth-logo" src="<?= base_url('images/temurasa_primary_fit.png'); ?>" alt="Temu Rasa">
            <h1 class="tr-auth-title">Lupa Password</h1>
            <p class="tr-auth-subtitle">
                <?= esc($subtitle ?? 'Masukkan email terdaftar untuk menerima tautan reset.'); ?>
            </p>
        </div>

        <!-- RIGHT: FORM -->
        <div class="tr-auth-right">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="tr-auth-alert">
                    <?= esc(session()->getFlashdata('error')); ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('message')): ?>
                <div class="tr-auth-alert" style="border-color: var(--tr-primary); background: rgba(122,154,108,.12);">
                    <?= esc(session()->getFlashdata('message')); ?>
                </div>
            <?php endif; ?>

            <form class="tr-auth-form" action="<?= site_url('auth/forgot'); ?>" method="post">
                <?= csrf_field(); ?>

                <div class="tr-auth-field">
                    <label for="email">Email</label>
                    <input
                        class="tr-auth-input"
                        type="email"
                        id="email"
                        name="email"
                        value="<?= esc(old('email') ?? ''); ?>"
                        autocomplete="email"
                        required>
                </div>

                <div class="tr-auth-actions">
                    <button type="submit" class="btn btn-primary">Kirim Tautan Reset</button>
                </div>
            </form>

            <div class="tr-auth-hint">
                <a href="<?= site_url('login'); ?>">Kembali ke login</a>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>