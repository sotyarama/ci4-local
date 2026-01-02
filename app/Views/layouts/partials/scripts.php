<meta name="csrf-name" content="<?= csrf_token(); ?>">
<meta name="csrf-token" content="<?= csrf_hash(); ?>">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script src="<?= base_url('js/app.js') . '?v=' . $assetVer; ?>"></script>
<script src="<?= base_url('js/theme-toggle.js') . '?v=' . $assetVer; ?>"></script>
<script src="<?= base_url('js/sidebar-toggle.js') . '?v=' . $assetVer; ?>"></script>
<script src="<?= base_url('js/tr-daterange.js') . '?v=' . $assetVer; ?>"></script>