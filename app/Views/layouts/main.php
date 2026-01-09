<?php
$assetVer = time(); // keep behavior: non-cache for dev

$role = strtolower((string) (session('role') ?? session('role_name') ?? ''));

$uri = service('uri');
$currentPath = strtolower(trim($uri->getPath(), '/'));

$reqUri = strtolower(trim(parse_url(current_url(false), PHP_URL_PATH) ?? '', '/'));
if ($reqUri !== '') {
    $currentPath = $reqUri;
}
if (str_starts_with($currentPath, 'index.php/')) {
    $currentPath = substr($currentPath, strlen('index.php/'));
}

$flashError = session()->getFlashdata('error') ?? null;

$roleAllow = [
    'owner'   => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
    'staff'   => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
    'auditor' => ['dashboard', 'master', 'transactions', 'inventory', 'reports', 'overhead'],
];

$menuAllowed = static function (string $key) use ($role, $roleAllow): bool {
    if ($role === '') return false;
    return in_array($key, $roleAllow[$role] ?? [], true);
};

$canManageUsers = in_array($role, ['owner', 'auditor'], true);

$isActive = static function (array $paths) use ($currentPath): bool {
    foreach ($paths as $p) {
        $p = strtolower(trim($p, '/'));
        if ($p === '' && $currentPath === '') return true;
        if ($p !== '' && ($currentPath === $p || str_starts_with($currentPath, $p . '/'))) return true;
    }
    return false;
};

    $navLink = static function (
        string $href,
        string $label,
        bool $allowed,
        bool $active,
        bool $small = true
    ): string {
        $classes = ['nav-link'];
        if ($small) $classes[] = 'small';
        if (! $allowed) $classes[] = 'disabled-link';
        if ($active) $classes[] = 'active';

        $classAttr = implode(' ', $classes);
        // Wrap label so collapsed state can hide text, and add title for tooltip support.
        $labelHtml = '<span class="nav-label">' . esc($label) . '</span>';
        return '<a href="' . site_url($href) . '" class="' . esc($classAttr) . '" title="' . esc($label) . '">' . $labelHtml . '</a>';
    };

$layoutData = [
    'assetVer'       => $assetVer,
    'flashError'     => $flashError,
    'title'          => $title ?? 'Cafe POS',
    'subtitle'       => $subtitle ?? null,

    'role'           => $role,
    'currentPath'    => $currentPath,
    'menuAllowed'    => $menuAllowed,
    'canManageUsers' => $canManageUsers,
    'isActive'       => $isActive,
    'navLink'        => $navLink,
];
?>

<!DOCTYPE html>
<html lang="id">
<?= view('layouts/partials/head', $layoutData) ?>

<body>
    <?= view('layouts/partials/flash_toast', $layoutData) ?>

    <div class="layout">
        <?= view('layouts/partials/sidebar', $layoutData) ?>

        <div class="main">
            <?= view('layouts/partials/topbar', $layoutData) ?>

            <main class="content">
                <?= $this->renderSection('content') ?>
            </main>

            <?= view('layouts/partials/footer', $layoutData) ?>
        </div>
    </div>

    <?= view('layouts/partials/scripts', $layoutData) ?>
</body>

</html>