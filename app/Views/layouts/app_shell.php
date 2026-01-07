<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= esc($title ?? 'App') ?></title>

    <link rel="stylesheet" href="<?= base_url('assets/css/app/shell.css') ?>">
</head>

<body>

    <div class="app">
        <aside class="app__sidebar">
            <?= $this->renderSection('sidebar') ?>
        </aside>

        <main class="app__main">
            <header class="app__topbar">
                <?= $this->renderSection('topbar') ?>
            </header>

            <section class="app__content">
                <?= $this->renderSection('content') ?>
            </section>

            <footer class="app__footer">
                <?= $this->renderSection('footer') ?>
            </footer>
        </main>
    </div>

</body>

</html>