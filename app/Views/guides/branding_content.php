<?php
$colors = [
    ['name' => 'Sage Green', 'hex' => '#7A9A6C'],
    ['name' => 'Soft Cream', 'hex' => '#F4F1EA'],
    ['name' => 'Warm Brown', 'hex' => '#A27C55'],
    ['name' => 'Charcoal Soft', 'hex' => '#3A3A3A'],
    ['name' => 'Sage Light', 'hex' => '#C8D7C0'],
    ['name' => 'Warm Beige', 'hex' => '#E7DFD1'],
];

$recommendedBackgrounds = [
    ['name' => 'Soft Cream', 'hex' => '#F4F1EA'],
    ['name' => 'White', 'hex' => '#FFFFFF'],
    ['name' => 'Sage Light', 'hex' => '#C8D7C0'],
    ['name' => 'Warm Beige', 'hex' => '#E7DFD1'],
];

$avoidBackgrounds = [
    ['name' => 'Dark Brown', 'hex' => '#4B3A2F'],
    ['name' => 'Black', 'hex' => '#000000'],
    ['name' => 'Vibrant Red', 'hex' => '#D7263D'],
    ['name' => 'Neon Green', 'hex' => '#39FF14'],
];

$posProducts = [
    ['name' => 'Temu Rasa Latte', 'desc' => 'Hot / Iced', 'price' => '18.000'],
    ['name' => 'Kopi Susu Aren', 'desc' => 'Signature', 'price' => '17.000'],
    ['name' => 'Es Kopi Temu', 'desc' => 'Creamy', 'price' => '19.000'],
    ['name' => 'Es Teh Manis', 'desc' => 'Dingin', 'price' => '8.000'],
    ['name' => 'Roti Bakar', 'desc' => 'Coklat / Keju', 'price' => '12.000'],
    ['name' => 'Nasi Goreng Temu', 'desc' => 'Spesial', 'price' => '20.000'],
];

$dashSummary = [
    [
        'label' => 'Penjualan Hari Ini',
        'value' => 'Rp 1.250.000',
        'note' => '+ 12% vs kemarin',
        'noteClass' => 'tr-text-accent',
    ],
    [
        'label' => 'Transaksi',
        'value' => '64',
        'note' => 'Rata-rata 19.500 / struk',
        'noteClass' => 'tr-muted',
    ],
    [
        'label' => 'Menu Terlaris',
        'value' => 'Kopi Susu Aren',
        'note' => '18 porsi',
        'noteClass' => 'tr-muted',
    ],
    [
        'label' => 'Jam Tersibuk',
        'value' => '16.00 - 18.00',
        'note' => '19 transaksi',
        'noteClass' => 'tr-muted',
    ],
];

$dashBars = [40, 55, 70, 90, 60, 35];

$dashOrders = [
    ['id' => '#INV-023', 'detail' => '2 menu - Tunai', 'total' => '38.000', 'time' => '09:21'],
    ['id' => '#INV-022', 'detail' => '3 menu - QRIS', 'total' => '52.000', 'time' => '09:10'],
    ['id' => '#INV-021', 'detail' => '1 menu - Tunai', 'total' => '17.000', 'time' => '08:55'],
];
?>

<div class="tr-branding-page">
    <div class="tr-branding-container">
        <!-- PDF export intentionally disabled for Branding/How-To modules — web-only content. -->
        <div class="tr-branding-logo-wrap">
            <img src="<?= esc($logoSrc ?? base_url('images/temurasa_primary_fit.png')); ?>" alt="Temu Rasa Logo">
        </div>

        <header class="tr-branding-header">
            <h1>Temu Rasa - Brand Guideline</h1>
            <p class="tr-branding-subtitle">Tempat Bertemu, Tempat Berasa.</p>
        </header>

        <section id="essence-story" class="tr-section tr-section-compact">
            <h2 class="tr-section-title">Brand Essence</h2>
            <p>
                Temu Rasa adalah brand yang hangat, ramah, dan minimalis. Ia hadir sebagai ruang
                pertemuan, tempat orang berbagi cerita dan menikmati rasa dalam suasana yang lembut
                dan bersahabat.
            </p>
            <div class="tr-branding-divider"></div>
            <h2 class="tr-section-title">Brand Story</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Temu Rasa lahir dari sebuah ruang sederhana - ruang yang dulunya digunakan keluarga
                    untuk berkumpul, bercakap, dan menerima tamu yang singgah. Dari ruang itu muncul
                    gagasan: bagaimana jika tempat ini kembali menjadi titik temu, bukan hanya untuk
                    keluarga, tetapi juga untuk banyak orang di sekitar? Remaja yang mencari suasana
                    santai, warga yang ingin berbincang, hingga para pekerja yang singgah sejenak untuk
                    melepas penat.
                </p>
                <p>
                    Nama "Temu Rasa" mengandung dua makna: pertemuan dan perasaan. Pertemuan antar
                    manusia yang membawa cerita, dan perasaan hangat yang muncul dari pengalaman kecil
                    seperti menikmati secangkir minuman, menyapa teman, atau sekadar duduk menikmati
                    suasana. Semua pengalaman itu adalah "rasa" yang bertemu di satu ruang.
                </p>
                <p>
                    Logo Temu Rasa terinspirasi dari momen-momen itu - dua lingkaran yang mewakili manusia,
                    dan garis loop yang melambangkan aliran rasa dan cerita. Visual sederhana ini
                    menangkap esensi brand: tempat yang ramah, estetik, dan hangat, namun tetap membumi.
                </p>
                <p>
                    Temu Rasa bukan hanya warkop naik kelas. Ia adalah ruang kecil yang mengundang orang
                    untuk berhenti sejenak. Ruang untuk mendengar, merasakan, dan terhubung. Ruang untuk
                    menemukan kembali ketenangan dalam keseharian yang kadang terasa tergesa-gesa.
                </p>
                <p>
                    Dengan desain minimalis, palet warna natural, dan identitas visual yang menenangkan,
                    Temu Rasa hadir bukan sekadar sebagai tempat menikmati minuman, tetapi sebagai
                    pengalaman: pengalaman bertemu, merasakan, dan pulang dengan hati yang sedikit lebih
                    ringan.
                </p>
                <p class="tr-quote">
                    "Temu Rasa adalah perjumpaan kecil yang berarti. Tempat di mana manusia dan rasa saling bertemu."
                </p>
            </div>
            <div class="tr-branding-divider"></div>
            <h2 class="tr-section-title">Brand Pillars</h2>
            <div class="tr-pillars-grid">
                <div class="tr-pillar-card">
                    <h3 class="tr-pillar-title">Hangat</h3>
                    <p class="tr-pillar-text">
                        Temu Rasa menghadirkan suasana yang menenangkan dan bersahabat. Setiap
                        pengunjung disambut tanpa jarak, seperti singgah di ruang yang sudah dikenal.
                    </p>
                </div>
                <div class="tr-pillar-card">
                    <h3 class="tr-pillar-title">Ramah</h3>
                    <p class="tr-pillar-text">
                        Interaksi yang sederhana, bahasa yang mudah dipahami, dan pelayanan yang tulus.
                        Tidak kaku, tidak mengintimidasi - semua orang merasa diterima.
                    </p>
                </div>
                <div class="tr-pillar-card">
                    <h3 class="tr-pillar-title">Minimalis</h3>
                    <p class="tr-pillar-text">
                        Tampilan bersih dan tidak berlebihan. Fokus pada esensi: rasa, suasana, dan
                        pertemuan, tanpa distraksi visual yang tidak perlu.
                    </p>
                </div>
                <div class="tr-pillar-card">
                    <h3 class="tr-pillar-title">Membumi</h3>
                    <p class="tr-pillar-text">
                        Warkop naik kelas yang tetap dekat dengan keseharian. Estetik dan modern, namun
                        tetap relevan, terjangkau, dan tidak terasa berjarak.
                    </p>
                </div>
            </div>
        </section>

        <section id="colors-and-usage" class="tr-section">
            <h2 class="tr-section-title">Color Palette</h2>
            <div class="tr-color-grid">
                <?php foreach ($colors as $color): ?>
                    <button type="button" class="tr-color-card" data-hex="<?= esc($color['hex']); ?>">
                        <span class="tr-color-swatch" style="background-color: <?= esc($color['hex']); ?>;"></span>
                        <span class="tr-color-name"><?= esc($color['name']); ?></span>
                        <span class="tr-color-hex"><?= esc($color['hex']); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="tr-hex-hint" id="hexHint">
                HEX disalin: <strong id="hexValue"></strong>
            </div>
            <p class="tr-text-sm">
                Palet warna Temu Rasa terdiri dari enam warna utama yang mencerminkan karakter
                brand: hangat, natural, dan estetik. Warna-warna ini digunakan secara konsisten
                di semua media untuk membangun identitas visual yang kuat dan mudah dikenali.
            </p>
            <div class="tr-branding-divider"></div>
            <h2 class="tr-section-title">Color Usage System</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Sistem penggunaan warna memastikan identitas Temu Rasa tetap konsisten, mudah dikenali,
                    dan memiliki kontras visual yang baik di berbagai media. Warna utama seperti Sage Green
                    dan Soft Cream digunakan untuk membangun karakter hangat, natural, dan estetik.
                </p>
                <div class="tr-color-usage-grid">
                    <div class="tr-color-usage-card">
                        <h3 class="tr-subtitle">Background yang Disarankan</h3>
                        <div class="tr-grid tr-color-samples tr-color-samples-row4 tr-color-samples-compact">
                            <?php foreach ($recommendedBackgrounds as $bg): ?>
                                <div class="tr-color-sample">
                                    <div class="tr-color-square" style="background-color: <?= esc($bg['hex']); ?>;"></div>
                                    <span class="tr-color-label"><?= esc($bg['name']); ?></span>
                                    <span class="tr-color-hex"><?= esc($bg['hex']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="tr-text-xs tr-muted">
                            Gunakan background terang agar logo tetap jelas dan memiliki kontras yang optimal.
                        </p>
                    </div>
                    <div class="tr-color-usage-card tr-color-usage-card--warn">
                        <h3 class="tr-subtitle">Background yang Tidak Disarankan</h3>
                        <div class="tr-grid tr-color-samples tr-color-samples-row4 tr-color-samples-compact">
                            <?php foreach ($avoidBackgrounds as $bg): ?>
                                <div class="tr-color-sample tr-color-sample-muted">
                                    <div class="tr-color-square" style="background-color: <?= esc($bg['hex']); ?>;"></div>
                                    <span class="tr-color-label"><?= esc($bg['name']); ?></span>
                                    <span class="tr-color-hex"><?= esc($bg['hex']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="tr-text-xs tr-muted">
                            Hindari warna dengan kontras rendah atau warna yang terlalu mencolok karena dapat
                            mengganggu karakter lembut dan estetik Temu Rasa.
                        </p>
                    </div>
                </div>
                <div class="tr-color-usage-lists">
                    <div class="tr-stack-sm">
                        <h3 class="tr-subtitle">Kombinasi Warna yang Disarankan</h3>
                        <ul class="tr-list">
                            <li><strong>Sage Green + Soft Cream</strong> - kombinasi utama untuk branding.</li>
                            <li><strong>Warm Brown + Sage Light</strong> - cocok untuk menu dan elemen dekoratif.</li>
                            <li><strong>Charcoal Soft + Soft Cream</strong> - untuk teks dengan keterbacaan tinggi.</li>
                            <li><strong>Sage Green + White</strong> - ideal untuk packaging dan signage.</li>
                        </ul>
                    </div>
                    <div class="tr-stack-sm">
                        <h3 class="tr-subtitle">Hal yang Harus Dihindari</h3>
                        <ul class="tr-list">
                            <li>Menggunakan logo Sage Green di atas background hijau gelap atau warna serupa.</li>
                            <li>Menggunakan warna neon atau warna jenuh tinggi yang tidak sesuai karakter brand.</li>
                            <li>Mengubah palet warna inti di luar enam warna resmi Temu Rasa.</li>
                            <li>Menggunakan kombinasi warna dengan kontras buruk yang menurunkan keterbacaan.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <section id="typhography" class="tr-section">
            <h2 class="tr-section-title">Typography</h2>
            <div class="tr-typography-grid">
                <ul class="tr-typography-list">
                    <li class="tr-typography-item">
                        <span class="tr-typography-label">Heading</span>
                        <span class="tr-typography-sample tr-font-heading">Nunito (rounded, warm)</span>
                    </li>
                    <li class="tr-typography-item">
                        <span class="tr-typography-label">Subheading</span>
                        <span class="tr-typography-sample tr-font-subheading">Poppins</span>
                    </li>
                    <li class="tr-typography-item">
                        <span class="tr-typography-label">Body Text</span>
                        <span class="tr-typography-sample tr-font-body">Inter</span>
                    </li>
                </ul>
            </div>
            <p>
                Tipografi Temu Rasa dipilih untuk mencerminkan karakter brand yang ramah dan
                modern. Nunito sebagai font heading memberikan kesan hangat dan bersahabat,
                sementara Poppins dan Inter memastikan keterbacaan yang optimal di berbagai
                ukuran layar dan media cetak.
            </p>
            <div class="tr-typography-usage">
                <h3 class="tr-subtitle">Typography in Use</h3>
                <p class="tr-text-sm tr-muted tr-typography-usage-intro">
                    Contoh penerapan tipografi Temu Rasa dalam konteks nyata.
                </p>
                <div class="tr-typography-usage-grid">
                    <div class="tr-panel tr-card tr-typography-usage-card">
                        <div class="tr-card-header">
                            <span class="tr-typography-label">Heading (Nunito)</span>
                        </div>
                        <div class="tr-card-body tr-typography-usage-item">
                            <div class="tr-typography-demo tr-typography-demo-heading tr-font-heading">TEMU RASA</div>
                            <div class="tr-typography-demo tr-typography-demo-subheading tr-font-subheading">
                                Tempat Bertemu, Tempat Berasa
                            </div>
                        </div>
                        <div class="tr-card-footer">
                            <ul class="tr-card-footer-list">
                                <li>Digunakan untuk judul utama</li>
                                <li>Memberi kesan hangat dan bersahabat</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tr-panel tr-card tr-typography-usage-card">
                        <div class="tr-card-header">
                            <span class="tr-typography-label">Subheading (Poppins)</span>
                        </div>
                        <div class="tr-card-body tr-typography-usage-item">
                            <div class="tr-typography-demo tr-font-subheading">Brand Essence</div>
                            <div class="tr-typography-demo tr-font-subheading">Brand Story</div>
                            <div class="tr-typography-demo tr-font-subheading">Menu Pilihan</div>
                        </div>
                        <div class="tr-card-footer">
                            <ul class="tr-card-footer-list">
                                <li>Digunakan untuk judul section</li>
                                <li>Tegas namun tetap lembut</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tr-panel tr-card tr-typography-usage-card">
                        <div class="tr-card-header">
                            <span class="tr-typography-label">Body Text (Inter)</span>
                        </div>
                        <div class="tr-card-body tr-typography-usage-item">
                            <p class="tr-typography-body-sample tr-font-body">
                                Temu Rasa adalah ruang pertemuan yang hangat, tempat orang berbagi cerita dan menikmati
                                rasa dalam suasana yang bersahabat.
                            </p>
                        </div>
                        <div class="tr-card-footer">
                            <ul class="tr-card-footer-list">
                                <li>Digunakan untuk paragraf panjang</li>
                                <li>Nyaman dibaca di layar dan cetak</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <p class="tr-typography-note tr-italic">
                    Gunakan line-height yang cukup agar teks terasa ringan dan mudah dibaca.
                </p>
            </div>
            <div class="tr-typography-dos">
                <h3 class="tr-subtitle">Do &amp; Don't Typography</h3>
                <div class="tr-grid tr-grid-2 tr-typography-dos-grid">
                    <div class="tr-panel tr-typography-do">
                        <h4 class="tr-typography-card-title">Do</h4>
                        <ul class="tr-list tr-text-sm">
                            <li>Gunakan Nunito hanya untuk heading</li>
                            <li>Gunakan Inter untuk teks panjang</li>
                            <li>Jaga jarak antar baris agar teks terasa ringan</li>
                            <li>Konsisten menggunakan hirarki ukuran font</li>
                        </ul>
                    </div>
                    <div class="tr-panel tr-typography-dont">
                        <h4 class="tr-typography-card-title">Don't</h4>
                        <ul class="tr-list tr-text-sm">
                            <li>Jangan mencampur lebih dari dua font dalam satu layout</li>
                            <li>Jangan menggunakan font dekoratif</li>
                            <li>Jangan menggunakan bold berlebihan pada body text</li>
                            <li>Hindari ukuran teks terlalu kecil atau terlalu rapat</li>
                        </ul>
                    </div>
                </div>
            </div>

        </section>

        <section id="logo-meaning" class="tr-section">
            <h2 class="tr-section-title">Logo Meaning</h2>
            <div class="tr-logo-meaning-top">
                <div class="tr-panel tr-card tr-logo-meaning-card">
                    <div class="tr-card-header tr-logo-meaning-header">
                        <span class="tr-logo-meaning-eyebrow">Dua Lingkaran</span>
                        <span class="tr-logo-meaning-title">Pertemuan</span>
                        <span class="tr-logo-meaning-caption">Setara - inklusif - saling menerima</span>
                    </div>
                    <div class="tr-card-body tr-logo-meaning-body">
                        <img class="tr-logo-meaning-image" src="<?= base_url('images/two_circles.png'); ?>" alt="Dua lingkaran">
                    </div>
                    <div class="tr-card-footer tr-logo-meaning-footer">
                        <h3 class="tr-subtitle">A. Dua Lingkaran - "Pertemuan"</h3>
                        <ul class="tr-list tr-text-sm">
                            <li>Dua individu yang bertemu secara setara.</li>
                            <li>Saling menerima dan saling terhubung.</li>
                            <li>Ruang terbuka bagi siapa pun: remaja, warga, hingga pekerja yang singgah.</li>
                        </ul>
                        <p class="tr-logo-meaning-note">
                            Temu Rasa menegaskan karakter brand sebagai tempat pertemuan yang inklusif, ramah, dan tidak mengintimidasi.
                        </p>
                    </div>
                </div>
                <div class="tr-panel tr-card tr-logo-meaning-card">
                    <div class="tr-card-header tr-logo-meaning-header">
                        <span class="tr-logo-meaning-eyebrow">Garis Loop</span>
                        <span class="tr-logo-meaning-title">Rasa yang Mengalir</span>
                        <span class="tr-logo-meaning-caption">Kesinambungan - pertumbuhan - cerita</span>
                    </div>
                    <div class="tr-card-body tr-logo-meaning-body">
                        <img class="tr-logo-meaning-image" src="<?= base_url('images/infinity.png'); ?>" alt="Garis loop">
                    </div>
                    <div class="tr-card-footer tr-logo-meaning-footer">
                        <h3 class="tr-subtitle">B. Garis Infinity / Loop - "Rasa yang Mengalir"</h3>
                        <ul class="tr-list tr-text-sm">
                            <li>Perjalanan rasa, dinamika perasaan, dan cerita yang beririsan.</li>
                            <li>Mewakili percakapan yang tumbuh di meja kopi.</li>
                            <li>Simbol kesinambungan, pertumbuhan, dan pengalaman yang tidak terputus.</li>
                        </ul>
                        <p class="tr-logo-meaning-note">
                            Loop memberi makna bahwa setiap singgah bisa menjadi bagian dari cerita yang berlanjut.
                        </p>
                    </div>
                </div>
                <div class="tr-panel tr-card tr-logo-meaning-card">
                    <div class="tr-card-header tr-logo-meaning-header">
                        <span class="tr-logo-meaning-eyebrow">Komposisi</span>
                        <span class="tr-logo-meaning-title">Cerita Bersilangan</span>
                        <span class="tr-logo-meaning-caption">Manusia - rasa - perjumpaan</span>
                    </div>
                    <div class="tr-card-body tr-logo-meaning-body">
                        <img class="tr-logo-meaning-image tr-logo-meaning-image--combo" src="<?= base_url('images/temurasa_logo-only_fit.png'); ?>" alt="Logo Temu Rasa">
                    </div>
                    <div class="tr-card-footer tr-logo-meaning-footer">
                        <h3 class="tr-subtitle">C. Keharmonisan Keduanya</h3>
                        <ul class="tr-list tr-text-sm">
                            <li>Manusia bertemu, rasa bersentuhan, cerita saling terhubung.</li>
                            <li>Visual sederhana yang tetap bermakna.</li>
                            <li>Representasi positioning: warkop naik kelas yang estetik.</li>
                        </ul>
                        <p class="tr-logo-meaning-note">
                            Tempat modern bagi remaja untuk berkumpul - hangat, santai, dan membumi.
                        </p>
                    </div>
                </div>
            </div>

            <div class="tr-panel tr-logo-meaning-wide">
                <div class="tr-logo-meaning-color">
                    <span class="tr-logo-meaning-color-dot" aria-hidden="true"></span>
                    <div>
                        <h3 class="tr-subtitle">Makna Warna - Sage Green</h3>
                        <p class="tr-text-xs tr-muted">Teduh - natural - modern - elegan tanpa berjarak</p>
                    </div>
                </div>
                <p class="tr-text-sm">
                    Sage green dipilih karena menghadirkan keteduhan, kesederhanaan, keseimbangan, dan kehangatan.
                    Warna ini estetik namun tetap terasa dekat - membuat Temu Rasa nyaman bagi semua kalangan.
                </p>
            </div>

            <div class="tr-logo-meaning-bottom">
                <div class="tr-panel">
                    <h3 class="tr-subtitle">Pesan Brand yang Tersirat</h3>
                    <ul class="tr-list tr-text-sm">
                        <li><strong>Ramah dan terbuka</strong> - bentuk sederhana yang mudah dipahami.</li>
                        <li><strong>Modern dan estetik</strong> - clean-line, cocok dengan budaya visual remaja.</li>
                        <li><strong>Hangat dan bersahabat</strong> - kurva lembut tanpa sudut tajam.</li>
                        <li><strong>Naik kelas namun membumi</strong> - modern tanpa kehilangan keaslian.</li>
                        <li><strong>Mudah diaplikasi</strong> - fleksibel untuk signage, menu, cup, stiker, dan merchandise.</li>
                    </ul>
                </div>
                <div class="tr-panel">
                    <h3 class="tr-subtitle">Kekuatan Logo Ini</h3>
                    <ul class="tr-list tr-text-sm">
                        <li>Unik namun mudah dikenali.</li>
                        <li>Estetik dan menarik bagi target muda (remaja).</li>
                        <li>Clean, relevan dengan tren desain modern.</li>
                        <li>Fleksibel untuk format vertikal, horizontal, maupun ikon.</li>
                        <li>Mudah dibuat versi hitam-putih untuk print hemat biaya.</li>
                    </ul>
                </div>
            </div>
        </section>

        <section id="logo-system" class="tr-section">
            <h2 class="tr-section-title">Logo System</h2>
            <div class="tr-grid tr-grid-3">
                <div class="tr-panel tr-logo-system-card">
                    <h3 class="tr-subtitle">Primary Logo</h3>
                    <div class="tr-placeholder tr-placeholder-media">
                        <img src="<?= base_url('images/temurasa_primary_fit.png'); ?>" alt="Temu Rasa Primary Logo">
                    </div>
                    <p class="tr-text-sm">
                        Digunakan sebagai identitas visual utama Temu Rasa.
                        Versi ini menampilkan simbol dan nama brand secara lengkap,
                        sehingga paling representatif dalam memperkenalkan karakter, filosofi, dan kehadiran brand secara menyeluruh.
                    </p>
                    <div class="tr-logo-system-recommend">
                        <span class="tr-logo-system-label">Recommended for:</span>
                        <ul class="tr-list tr-text-sm tr-logo-system-list">
                            <li>Papan nama</li>
                            <li>Menu cetak</li>
                            <li>Website utama</li>
                            <li>Materi promosi</li>
                        </ul>
                    </div>
                </div>
                <div class="tr-panel tr-logo-system-card">
                    <h3 class="tr-subtitle">Secondary Logo (Symbol)</h3>
                    <div class="tr-placeholder circle tr-placeholder-media">
                        <img src="<?= base_url('images/temurasa_logo-only_fit.png'); ?>" alt="Temu Rasa Symbol Logo">
                    </div>
                    <p class="tr-text-sm">
                        Versi simbol dari logo Temu Rasa yang digunakan ketika ruang visual sangat terbatas.
                        Fokus pada bentuk inti logo untuk menjaga identitas tetap dikenali meskipun tanpa teks.
                    </p>
                    <div class="tr-logo-system-recommend">
                        <span class="tr-logo-system-label">Recommended for:</span>
                        <ul class="tr-list tr-text-sm tr-logo-system-list">
                            <li>Stiker cup</li>
                            <li>Cup lid</li>
                            <li>Favicon</li>
                            <li>Watermark</li>
                        </ul>
                    </div>
                </div>
                <div class="tr-panel tr-logo-system-card">
                    <h3 class="tr-subtitle">Horizontal Logo</h3>
                    <div class="tr-placeholder tr-placeholder-media">
                        <img src="<?= base_url('images/temurasa_horizontal_fit.png'); ?>" alt="Temu Rasa Horizontal Logo">
                    </div>
                    <p class="tr-text-sm">
                        Variasi logo dengan orientasi horizontal yang dirancang untuk kebutuhan layout melebar.
                        Menjaga keterbacaan dan keseimbangan visual saat logo ditempatkan pada area dengan ruang vertikal terbatas.
                    </p>
                    <div class="tr-logo-system-recommend">
                        <span class="tr-logo-system-label">Recommended for:</span>
                        <ul class="tr-list tr-text-sm tr-logo-system-list">
                            <li>Website header</li>
                            <li>POS header</li>
                            <li>Kop surat</li>
                            <li>Spanduk dan banner horizontal</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tr-panel tr-logo-system-summary">
                <h3 class="tr-subtitle">Quick Rule Summary</h3>
                <p class="tr-text-sm">
                    <strong>Primary Logo adalah pilihan utama.</strong>
                    Gunakan Secondary Logo hanya ketika ruang visual sangat terbatas.
                    Gunakan Horizontal Logo untuk kebutuhan layout melebar atau area header.
                </p>
            </div>
        </section>

        <section id="logo-grid-clearspace" class="tr-section">
            <div class="tr-logo-grid-clearspace">
                <div class="tr-logo-grid-col">
                    <h2 class="tr-section-title">Logo Construction Grid</h2>
                    <div class="tr-logo-grid-panel">
                        <div class="tr-panel tr-stack">
                            <p class="tr-text-sm">
                                Construction grid membantu menjaga proporsi dan konsistensi logo Temu Rasa ketika digunakan pada berbagai
                                ukuran dan media. Grid ini menjadi panduan visual untuk memastikan keseimbangan bentuk lingkaran dan loop
                                tetap harmonis.
                            </p>
                            <div class="tr-logo-grid-wrap">
                                <div class="tr-logo-grid">
                                    <div class="tr-logo-grid-lines">
                                        <?php for ($i = 0; $i < 36; $i++): ?>
                                            <span></span>
                                        <?php endfor; ?>
                                    </div>
                                    <div class="tr-logo-grid-label">
                                        <img src="<?= base_url('images/temurasa_primary_fit.png'); ?>" alt="Logo Temu Rasa">
                                    </div>
                                </div>
                            </div>
                            <p>
                                Logo Temu Rasa dirancang menggunakan harmoni lingkaran dan garis lengkung. Grid 6x6 memastikan semua
                                elemen logo selalu berada dalam proporsi yang benar, terutama saat logo diperbesar atau diperkecil.
                            </p>
                            <ul class="tr-list">
                                <li>Dua lingkaran menggunakan diameter identik sebagai dasar keseimbangan visual.</li>
                                <li>Loop mengikuti kurva konsisten untuk menjaga karakter lembut logo.</li>
                                <li>Center alignment menggunakan prinsip optical centering untuk hasil visual yang seimbang.</li>
                                <li>Grid membantu menjaga proporsi ketika logo digunakan pada media digital maupun cetak.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="tr-logo-grid-divider" aria-hidden="true"></div>
                <div class="tr-logo-grid-col">
                    <h2 class="tr-section-title">Clearspace and Minimum Size</h2>
                    <div class="tr-logo-grid-panel">
                        <div class="tr-panel tr-stack">
                            <p>
                                Clearspace adalah ruang aman di sekitar logo yang memastikan logo tetap terlihat jelas
                                dan tidak terganggu oleh elemen visual lain. Area ini wajib dijaga pada semua media
                                untuk mempertahankan integritas identitas visual Temu Rasa.
                            </p>
                            <div class="tr-clearspace-wrap">
                                <div class="tr-clearspace-box">
                                    <div class="tr-clearspace-inner"></div>
                                    <div class="tr-clearspace-label">
                                        <?php
                                        $clearspaceSvg = file_get_contents(FCPATH . 'images/temurasa_primary_fit.svg');
                                        $clearspaceSvg = preg_replace('/<\?xml.*?\?>\s*/', '', $clearspaceSvg, 1);
                                        $clearspaceSvg = preg_replace('/\swidth="[^"]*"/i', '', $clearspaceSvg, 1);
                                        $clearspaceSvg = preg_replace('/\sheight="[^"]*"/i', '', $clearspaceSvg, 1);
                                        $clearspaceSvg = preg_replace(
                                            '/<svg([^>]*)>/i',
                                            '<svg$1 style="width:100%; height:auto; display:block;" aria-label="Temu Rasa Logo">',
                                            $clearspaceSvg,
                                            1
                                        );
                                        ?>
                                        <div style="width:80%; margin:0 auto;">
                                            <?= $clearspaceSvg ?>
                                        </div>
                                    </div>
                                    <span class="tr-clearspace-x tr-clearspace-top">X</span>
                                    <span class="tr-clearspace-x tr-clearspace-bottom">X</span>
                                    <span class="tr-clearspace-x tr-clearspace-left">X</span>
                                    <span class="tr-clearspace-x tr-clearspace-right">X</span>
                                </div>
                            </div>
                            <p>
                                Nilai <strong>X</strong> adalah tinggi lingkaran logo bagian atas. Tidak boleh ada teks, ikon,
                                garis, atau elemen visual lain yang memasuki area X di sekeliling logo.
                            </p>
                            <h3 class="tr-subtitle">Minimum Size</h3>
                            <p>
                                Untuk menjaga keterbacaan dan detail visual, logo Temu Rasa memiliki batas minimal saat
                                digunakan pada media cetak maupun digital:
                            </p>
                            <ul class="tr-list">
                                <li><strong>Print:</strong> tinggi minimum 20 mm</li>
                                <li><strong>Digital:</strong> tinggi minimum 64 px</li>
                                <li><strong>Icon (symbol-only):</strong> minimum 24 px</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="brand-pattern" class="tr-section">
            <h2 class="tr-section-title">Brand Pattern &amp; Graphic Elements</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Brand pattern Temu Rasa berasal dari bentuk loop pada logo - garis lengkung yang lembut, mengalir,
                    dan harmonis. Pattern digunakan sebagai elemen pendukung agar desain tetap hangat, rapi, dan estetik,
                    bukan sebagai fokus utama.
                </p>
                <div>
                    <h3 class="tr-subtitle">Gaya Pattern yang Direkomendasikan</h3>
                    <div class="tr-grid tr-grid-3 tr-pattern-grid">
                        <div class="tr-pattern-card" style="text-align:left; display:flex; flex-direction:column; gap:6px; padding:10px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                <span class="tr-typography-label">Primary Pattern</span>
                                <span class="tr-essence-pill">Subtle</span>
                            </div>
                            <h4 class="tr-subtitle" style="margin:0;">Wave Line</h4>
                            <div class="tr-pattern tr-pattern-wave" style="margin:4px 0 0; height:80px;"></div>
                            <p class="tr-text-xs" style="margin:0;">
                                Turunan langsung dari lengkung logo. Cocok untuk divider, header, dan frame.
                            </p>
                            <p class="tr-text-xs tr-muted" style="margin:0;">
                                <em>Typical usage:</em> header menu, divider section, frame Instagram.
                            </p>
                        </div>
                        <div class="tr-pattern-card" style="text-align:left; display:flex; flex-direction:column; gap:6px; padding:10px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                <span class="tr-typography-label">Supporting Pattern</span>
                                <span class="tr-essence-pill">Modular</span>
                            </div>
                            <h4 class="tr-subtitle" style="margin:0;">Soft Dots</h4>
                            <div class="tr-pattern tr-pattern-dots" style="margin:4px 0 0; height:80px;"></div>
                            <p class="tr-text-xs" style="margin:0;">
                                Interpretasi lembut dari dua lingkaran. Cocok untuk area kecil dan aksen.
                            </p>
                            <p class="tr-text-xs tr-muted" style="margin:0;">
                                <em>Typical usage:</em> stiker kecil, cup, interior detail, watermark.
                            </p>
                        </div>
                        <div class="tr-pattern-card" style="text-align:left; display:flex; flex-direction:column; gap:6px; padding:10px;">
                            <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                                <span class="tr-typography-label">Background Accent</span>
                                <span class="tr-essence-pill">Field</span>
                            </div>
                            <h4 class="tr-subtitle" style="margin:0;">Diagonal Soft Texture</h4>
                            <div class="tr-pattern tr-pattern-diagonal" style="margin:4px 0 0; height:80px;"></div>
                            <p class="tr-text-xs" style="margin:0;">
                                Memberi nuansa hangat dan estetik sebagai background yang tidak mengganggu.
                            </p>
                            <p class="tr-text-xs tr-muted" style="margin:0;">
                                <em>Typical usage:</em> background menu, poster, kartu promo, cover PDF.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="tr-subtitle">Rekomendasi Penggunaan Pattern</h3>
                    <ul class="tr-text-sm" style="margin:0; padding-left:18px; display:grid; gap:4px;">
                        <li>Pattern dipakai secara halus agar tidak mengalahkan logo dan konten utama.</li>
                        <li>Gunakan warna Sage Light atau Warm Beige untuk menjaga kesan lembut.</li>
                        <li>Pattern sebaiknya digunakan sebagai aksen, bukan elemen dominan.</li>
                        <li>Hindari pattern dengan kontras terlalu tinggi atau warna terlalu gelap.</li>
                        <li>Ideal untuk background menu, frame Instagram, stiker, atau dekor sudut interior.</li>
                    </ul>
                    <p class="tr-text-xs tr-muted tr-italic" style="margin:8px 0 0;">
                        <strong>Consistency note:</strong> Konsistensi penggunaan pattern lebih penting daripada variasi.
                        Disarankan memilih 1-2 pattern utama dan menggunakannya secara berulang agar identitas Temu Rasa mudah dikenali.
                    </p>
                </div>
                <div>
                    <h3 class="tr-subtitle">Do &amp; Don't</h3>
                    <div class="tr-grid tr-grid-2">
                        <div class="tr-panel tr-panel-soft">
                            <h4 class="tr-subtitle">Do</h4>
                            <ul class="tr-text-xs" style="margin:0; padding-left:18px; display:grid; gap:4px;">
                                <li>Gunakan pattern tipis / subtle untuk menjaga estetika.</li>
                                <li>Pastikan pattern tidak mengganggu teks utama.</li>
                                <li>Gunakan untuk memperkuat suasana hangat dan natural.</li>
                                <li>Pilih 1-2 pattern utama dan gunakan konsisten di seluruh materi.</li>
                            </ul>
                        </div>
                        <div class="tr-panel tr-panel-warn">
                            <h4 class="tr-subtitle">Don't</h4>
                            <ul class="tr-text-xs" style="margin:0; padding-left:18px; display:grid; gap:4px;">
                                <li>Jangan memakai pattern tebal / terlalu ramai.</li>
                                <li>Jangan menggunakan warna neon atau warna jenuh tinggi.</li>
                                <li>Jangan menempatkan pattern terlalu dekat dengan logo utama.</li>
                                <li>Jangan mengganti palet warna pattern di luar warna resmi Temu Rasa.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="logo-usage" class="tr-section">
            <h2 class="tr-section-title">Logo Usage</h2>
            <div class="tr-panel tr-stack">
                <div>
                    <h3 class="tr-subtitle">Prinsip Penggunaan Logo</h3>
                    <p class="tr-text-sm">
                        Gunakan logo Temu Rasa pada background terang (cream/putih) agar keterbacaan optimal. Jaga proporsi
                        (jangan ditarik/ditekan), hindari efek berat (shadow/glow/outline tebal), dan gunakan warna sesuai
                        palet resmi.
                    </p>
                </div>
                <div class="tr-grid tr-grid-2 tr-tone-voice-grid">
                    <div class="tr-panel tr-panel-soft">
                        <div class="tr-logo-usage-card-head">
                            <h4 class="tr-subtitle tr-logo-usage-title">Do</h4>
                            <span class="tr-essence-pill">Recommended</span>
                        </div>
                        <p class="tr-text-sm tr-text-accent tr-logo-usage-lead">
                            Gunakan versi resmi + background bersih
                        </p>
                        <div class="tr-logo-usage-preview">
                            <?php
                            $usageSvg = file_get_contents(FCPATH . 'images/temurasa_primary_fit.svg');
                            $usageSvg = preg_replace('/<\?xml.*?\?>\s*/', '', $usageSvg, 1);
                            $usageSvg = preg_replace('/\swidth="[^"]*"/i', '', $usageSvg, 1);
                            $usageSvg = preg_replace('/\sheight="[^"]*"/i', '', $usageSvg, 1);
                            $usageSvg = preg_replace(
                                '/<svg([^>]*)>/i',
                                '<svg$1 class="tr-logo-usage-svg" aria-label="Temu Rasa Logo">',
                                $usageSvg,
                                1
                            );
                            ?>
                            <div class="tr-logo-usage-preview-inner">
                                <div class="tr-logo-usage-preview-logo">
                                    <?= $usageSvg ?>
                                </div>
                            </div>
                        </div>
                        <ul class="tr-text-xs tr-logo-usage-list">
                            <li>Proporsi logo utuh (tidak di-stretch).</li>
                            <li>Background terang &amp; rapi.</li>
                            <li>Jaga clearspace di sekeliling logo.</li>
                        </ul>
                    </div>
                    <div class="tr-panel tr-panel-warn">
                        <div class="tr-logo-usage-card-head">
                            <h4 class="tr-subtitle tr-logo-usage-title">Don't</h4>
                            <span class="tr-essence-pill">Avoid</span>
                        </div>
                        <p class="tr-text-sm tr-text-warn tr-logo-usage-lead">
                            Jangan distorsi / beri efek berat / background ramai
                        </p>
                        <div class="tr-logo-usage-preview tr-logo-usage-preview--warn">
                            <div class="tr-logo-usage-dont-grid">
                                <div class="tr-logo-usage-dont-tile">
                                    <div class="tr-logo-usage-dont-logo tr-logo-usage-dont-logo--rotate">
                                        <?= $usageSvg ?>
                                    </div>
                                </div>
                                <div class="tr-logo-usage-dont-tile">
                                    <div class="tr-logo-usage-dont-logo tr-logo-usage-dont-logo--stretch">
                                        <?= $usageSvg ?>
                                    </div>
                                </div>
                                <div class="tr-logo-usage-dont-tile tr-logo-usage-dont-tile--neon">
                                    <div class="tr-logo-usage-dont-logo tr-logo-usage-dont-logo--neon">
                                        <?= $usageSvg ?>
                                    </div>
                                </div>
                                <div class="tr-logo-usage-dont-tile tr-logo-usage-dont-tile--busy">
                                    <div class="tr-logo-usage-dont-logo tr-logo-usage-dont-logo--busy">
                                        <?= $usageSvg ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <ul class="tr-text-xs tr-logo-usage-list">
                            <li>Jangan di-rotate / di-stretch / diubah proporsinya.</li>
                            <li>Jangan pakai neon/gradient/efek shadow tebal.</li>
                            <li>Jangan taruh di background yang ramai/kontras buruk.</li>
                        </ul>
                    </div>
                </div>
                <div class="tr-panel tr-panel-soft">
                    <p class="tr-text-xs tr-logo-usage-quick">
                        <strong>Quick check:</strong> clearspace aman • kontras cukup • ukuran terbaca • tanpa distorsi/efek berat.
                    </p>
                </div>
            </div>
        </section>

        <section id="tone-voice" class="tr-section">
            <h2 class="tr-section-title">Tone of Voice</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Tone of Voice Temu Rasa mencerminkan suasana hangat, ramah, dan sederhana. Cara brand berbicara
                    harus terasa seperti percakapan santai yang membuat pengunjung merasa diterima dan nyaman.
                </p>
                <div class="tr-grid tr-grid-2">
                    <div class="tr-panel tr-panel-soft">
                        <h3 class="tr-subtitle">Karakter Utama</h3>
                        <ul class="tr-list">
                            <li><strong>Hangat</strong> - nada bicara bersahabat, tidak kaku.</li>
                            <li><strong>Ramah</strong> - menggunakan kalimat sederhana dan mudah dipahami.</li>
                            <li><strong>Natural</strong> - menghindari kata-kata yang terlalu formal atau teknis.</li>
                            <li><strong>Positif</strong> - menghadirkan suasana yang teduh dan menyenangkan.</li>
                            <li><strong>Inklusif</strong> - tidak menggurui, tidak menyinggung, dan terasa dekat.</li>
                        </ul>
                    </div>
                    <div class="tr-panel tr-panel-soft">
                        <h3 class="tr-subtitle">Panduan Gaya Penulisan</h3>
                        <ul class="tr-list">
                            <li>Gunakan kalimat pendek dan jelas.</li>
                            <li>Pilih kata-kata yang halus dan lembut.</li>
                            <li>Gunakan kata ganti yang akrab seperti <strong>kita</strong> atau <strong>teman-teman</strong>.</li>
                            <li>Hindari nada berjualan yang terlalu agresif.</li>
                            <li>Sisipkan sentuhan emosional pada kalimat tertentu untuk membangun kedekatan.</li>
                        </ul>
                    </div>
                </div>
                <div class="tr-grid tr-grid-2 tr-tone-voice-grid">
                    <div class="tr-panel tr-panel-soft">
                        <h3 class="tr-subtitle">Contoh Penulisan (Do)</h3>
                        <div class="tr-tone-card">
                            <p>"Hari ini mau istirahat sejenak? Yuk mampir, kita siapkan minuman hangat buat nemenin kamu."</p>
                            <p>"Terima kasih sudah singgah. Semoga harimu jadi lebih ringan."</p>
                            <p>"Butuh tempat ngobrol? Temu Rasa selalu terbuka untuk kamu."</p>
                        </div>
                    </div>
                    <div class="tr-panel tr-panel-warn">
                        <h3 class="tr-subtitle">Contoh Penulisan (Don't)</h3>
                        <div class="tr-tone-card tr-tone-card-warn">
                            <p>"Promo besar! Beli sekarang sebelum kehabisan!!!"</p>
                            <p>"Kami menghimbau pelanggan untuk mengikuti peraturan yang berlaku."</p>
                            <p>"Tempat ini hanya untuk pembeli, dilarang duduk tanpa memesan."</p>
                        </div>
                    </div>
                </div>
                <div class="tr-tone-compass-grid tr-tone-compass-grid--split">
                    <div class="tr-panel tr-panel-soft tr-tone-compass-summary-card">
                        <h3 class="tr-subtitle">Tone of Voice Compass</h3>
                        <p class="tr-text-xs tr-muted">
                            Ringkasan prinsip yang harus terasa konsisten di setiap kata, kalimat, dan interaksi Temu Rasa.
                        </p>
                        <p class="tr-tone-compass-summary-title">
                            Hangat, Ramah, Natural, Positif, Inklusif
                        </p>
                        <p class="tr-text-xs tr-muted">
                            Jika ragu memilih kata, kembali ke 5 prinsip ini. Pastikan terdengar seperti percakapan
                            yang mengundang, bukan menggurui atau berjualan agresif.
                        </p>
                        <ul class="tr-list tr-text-xs tr-tone-compass-list">
                            <li>Pendek, jelas, dan lembut.</li>
                            <li>Gunakan kata yang terasa dekat: kita, teman-teman.</li>
                            <li>Hindari huruf kapital berlebihan, tanda seru bertumpuk, dan kalimat terlalu formal.</li>
                        </ul>
                        <p class="tr-italic tr-muted">
                            Suara Temu Rasa adalah suara yang mengundang: lembut, ramah, dan membuat orang ingin kembali.
                        </p>
                    </div>
                    <div class="tr-tone-compass-illustration">
                        <div class="tr-tone-compass-map-inner tr-tone-compass-map-inner--standalone">
                            <div class="tr-tone-compass-oval"></div>
                            <span class="tr-tone-compass-chip tr-tone-compass-chip--top">Hangat</span>
                            <span class="tr-tone-compass-chip tr-tone-compass-chip--right">Ramah</span>
                            <span class="tr-tone-compass-chip tr-tone-compass-chip--left">Natural</span>
                            <span class="tr-tone-compass-chip tr-tone-compass-chip--bottom">Positif</span>
                            <span class="tr-tone-compass-chip tr-tone-compass-chip--middle">Inklusif</span>
                            <div class="tr-tone-compass-center">
                                <span class="tr-tone-compass-center-label">Voice</span>
                                <span class="tr-tone-compass-center-title">Temu Rasa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section id="photography" class="tr-section">
            <h2 class="tr-section-title">Photography and Visual Style</h2>
            <div class="tr-grid tr-grid-2 tr-photo-main">
                <div class="tr-panel tr-panel-soft tr-photo-direction">
                    <div class="tr-photo-card-head">
                        <h3 class="tr-subtitle">Visual Direction</h3>
                        <span class="tr-essence-pill">Moodboard (placeholder)</span>
                    </div>
                    <div class="tr-photo-direction-grid">
                        <div class="tr-photo-hero">
                            <div class="tr-photo-frame tr-photo-frame--hero">
                                <img src="<?= base_url('images/hero-photo.png'); ?>" alt="Hero photo" class="tr-photo-img">
                            </div>
                            <div class="tr-photo-caption">
                                <span class="tr-photo-caption-title">Warm table scene</span>
                                <span class="tr-photo-caption-meta">Warm tone • Soft shadow • Calm • Unhurried • Inviting</span>
                            </div>
                            <div class="tr-photo-pill-row">
                                <span class="tr-essence-pill">Natural light</span>
                                <span class="tr-essence-pill">Negative space</span>
                                <span class="tr-essence-pill">Minimal props</span>
                            </div>
                            <div class="tr-panel tr-panel-soft tr-photo-shotlist">
                                <h3 class="tr-subtitle">Shot List</h3>
                                <div class="tr-photo-shotlist-grid">
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Hero menu</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Close-up texture</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Wide interior</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">People moment</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Counter / bar</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Packaging / cup</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Night warm light</span></div>
                                    <div class="tr-photo-shot-cell"><span class="tr-essence-pill">Detail signage</span></div>
                                </div>
                                <p class="tr-text-xs tr-muted">
                                    Pilih 3–4 tipe shot ini dan ulangi secara konsisten untuk menjaga identitas visual.
                                </p>
                            </div>
                        </div>
                        <div class="tr-photo-stack">
                            <div class="tr-photo-card">
                                <div class="tr-photo-frame">
                                    <img src="<?= base_url('images/detail-drink-photo-landscape.png'); ?>" alt="Detail menu close-up" class="tr-photo-img">
                                </div>
                                <p class="tr-photo-card-title">Detail menu (close-up)</p>
                                <p class="tr-photo-card-meta">Warm tone•Soft shadow•Texture focus</p>
                            </div>
                            <div class="tr-photo-card">
                                <div class="tr-photo-frame">
                                    <img src="<?= base_url('images/people-moment.png'); ?>" alt="Moment ngobrol" class="tr-photo-img">
                                </div>
                                <p class="tr-photo-card-title">Momen ngobrol (2-3 orang)</p>
                                <p class="tr-photo-card-meta">Warm tone•Soft shadow•Interaction</p>
                            </div>
                            <div class="tr-photo-card">
                                <div class="tr-photo-frame">
                                    <img src="<?= base_url('images/interior-corner-landscape.png'); ?>" alt="Interior corner" class="tr-photo-img">
                                </div>
                                <p class="tr-photo-card-title">Interior corner (calm)</p>
                                <p class="tr-photo-card-meta">Warm tone•Soft shadow•Calm</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tr-photo-side">
                    <div class="tr-panel tr-panel-soft tr-photo-principle">
                        <p class="tr-text-xs tr-photo-principle-title">
                            <strong>Core Principle:</strong> Biarkan suasana yang bicara. Foto harus terasa ringan,
                            hangat, dan tidak memaksa.
                        </p>
                        <p class="tr-text-xs tr-photo-principle-note">
                            Gunakan <strong>natural light</strong>, warna sesuai palet, dan komposisi sederhana dengan
                            <strong>negative space</strong>.
                        </p>
                        <div class="tr-photo-tags tr-photo-tags--compact">
                            <span class="tr-essence-pill">Warm tone</span>
                            <span class="tr-essence-pill">Soft shadow</span>
                            <span class="tr-essence-pill">Clean background</span>
                        </div>
                    </div>
                    <div class="tr-panel tr-panel-soft tr-photo-rules">
                        <div class="tr-photo-card-head">
                            <h3 class="tr-subtitle">Rules (Quick)</h3>
                            <span class="tr-essence-pill">1 frame = 1 fokus</span>
                        </div>
                        <div class="tr-photo-rule-grid">
                            <div class="tr-photo-rule-card">
                                <p class="tr-photo-rule-label">Lighting</p>
                                <p class="tr-photo-rule-title">Natural / soft</p>
                                <p class="tr-photo-rule-note">Dekat jendela • Hindari flash keras.</p>
                            </div>
                            <div class="tr-photo-rule-card">
                                <p class="tr-photo-rule-label">Color</p>
                                <p class="tr-photo-rule-title">Krem - sage - cokelat</p>
                                <p class="tr-photo-rule-note">Jaga tone hangat • No neon.</p>
                            </div>
                            <div class="tr-photo-rule-card">
                                <p class="tr-photo-rule-label">Composition</p>
                                <p class="tr-photo-rule-title">Negative space</p>
                                <p class="tr-photo-rule-note">Background rapi • Fokus jelas.</p>
                            </div>
                            <div class="tr-photo-rule-card">
                                <p class="tr-photo-rule-label">Editing</p>
                                <p class="tr-photo-rule-title">Soft, natural</p>
                                <p class="tr-photo-rule-note">Jangan over-filter • Kontras berlebihan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="tr-grid tr-grid-2 tr-photo-dos">
                        <div class="tr-panel tr-panel-soft tr-photo-do">
                            <div class="tr-photo-card-head">
                                <h3 class="tr-subtitle">Do</h3>
                                <span class="tr-essence-pill">Recommended</span>
                            </div>
                            <p class="tr-photo-do-title">Warm * clean * calm</p>
                            <div class="tr-photo-frame tr-photo-frame--mini">
                                <img src="<?= base_url('images/photography-do.png'); ?>" alt="Contoh foto do" class="tr-photo-img">
                            </div>
                            <div class="tr-photo-pill-row">
                                <span class="tr-essence-pill">Palet konsisten</span>
                                <span class="tr-essence-pill">Soft shadow</span>
                                <span class="tr-essence-pill">Simple props</span>
                            </div>
                        </div>
                        <div class="tr-panel tr-panel-warn tr-photo-dont">
                            <div class="tr-photo-card-head">
                                <h3 class="tr-subtitle">Don't</h3>
                                <span class="tr-essence-pill">Avoid</span>
                            </div>
                            <p class="tr-photo-dont-title">Flash * busy * over-filter</p>
                            <div class="tr-photo-frame tr-photo-frame--mini tr-photo-frame--warn">
                                <img src="<?= base_url('images/photography-dont.png'); ?>" alt="Contoh foto dont" class="tr-photo-img">
                            </div>
                            <div class="tr-photo-pill-row">
                                <span class="tr-essence-pill">Neon tone</span>
                                <span class="tr-essence-pill">Background ramai</span>
                                <span class="tr-essence-pill">Distorsi warna</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="applications" class="tr-section">
            <h2 class="tr-section-title">Application Examples</h2>
            <p class="tr-text-sm tr-application-intro">
                Contoh penerapan visual Temu Rasa di berbagai media. Gunakan palet warna natural, layout minimalis,
                dan ruang kosong agar identitas tetap terasa ringan dan konsisten.
            </p>
            <div class="tr-grid tr-grid-3 tr-application-grid">
                <div class="tr-panel tr-panel-soft tr-application-card">
                    <h3 class="tr-subtitle">Printed Media</h3>
                    <p class="tr-text-xs">
                        Media harus terasa ringan, rapi, dan mudah dibaca.
                    </p>
                    <div class="tr-application-mockup">
                        <div class="tr-application-tags">
                            <span class="tr-application-pill">Menu</span>
                            <span class="tr-application-tag-text">Minimalis feel</span>
                            <span class="tr-application-tag-text tr-application-tag-text--muted">text-first</span>
                        </div>
                        <div class="tr-application-mockup-inner">
                            <img src="<?= base_url('images/application-menu.png'); ?>" alt="Menu mockup" class="tr-application-img tr-application-img--ig">
                        </div>
                    </div>
                    <ul class="tr-application-list">
                        <li>Background Soft Cream / White</li>
                        <li>Heading Sage Green, body Charcoal Soft</li>
                        <li>Gunakan grid sederhana &amp; negative space</li>
                    </ul>
                    <p class="tr-application-note">Avoid using excessive icons or high-contrast colors</p>
                </div>
                <div class="tr-panel tr-panel-soft tr-application-card">
                    <h3 class="tr-subtitle">Packaging</h3>
                    <p class="tr-text-xs">
                        Identitas hadir secara halus, tidak mendominasi.
                    </p>
                    <div class="tr-application-mockup">
                        <div class="tr-application-tags">
                            <span class="tr-application-pill">Cup</span>
                            <span class="tr-application-tag-text">symbol-only</span>
                            <span class="tr-application-tag-text tr-application-tag-text--muted">calm wall</span>
                        </div>
                        <div class="tr-application-mockup-inner">
                            <img src="<?= base_url('images/application-cup.png'); ?>" alt="Cup mockup" class="tr-application-img tr-application-img--ig">
                        </div>
                    </div>
                    <ul class="tr-application-list">
                        <li>Gunakan symbol-only logo untuk cup kecil</li>
                        <li>Pattern lembut sebagai aksen</li>
                        <li>Jaga keseimbangan visual &amp; terlihat</li>
                    </ul>
                    <p class="tr-application-note">Note: logo jelas saat dilihat sekilas</p>
                </div>
                <div class="tr-panel tr-panel-soft tr-application-card">
                    <h3 class="tr-subtitle">Social Media Content</h3>
                    <p class="tr-text-xs">
                        Hangat, jujur, dan terasa dekat.
                    </p>
                    <div class="tr-application-mockup">
                        <div class="tr-application-tags">
                            <span class="tr-application-pill">Instagram</span>
                            <span class="tr-application-tag-text">Clean post</span>
                            <span class="tr-application-tag-text tr-application-tag-text--muted">minimal overlay</span>
                        </div>
                        <div class="tr-application-mockup-inner">
                            <img src="<?= base_url('images/application-ig.png'); ?>" alt="Social media mockup" class="tr-application-img tr-application-img--ig">
                        </div>
                    </div>
                    <ul class="tr-application-list">
                        <li>Gunakan foto warm tone &amp; natural light</li>
                        <li>Sage Green / Soft Cream sebagai aksen</li>
                        <li>Teks pendek, tenang, tidak hard-selling</li>
                    </ul>
                    <p class="tr-application-note">Avoid using: template ramai &amp; filter berlebihan</p>
                </div>
            </div>
        </section>

        <section id="menu-layout" class="tr-section">
            <h2 class="tr-section-title">Menu Layout - Landscape (Draft)</h2>
            <p class="tr-text-sm">
                Versi landscape dirancang untuk menu meja: alur baca horizontal, lebih lapang, dan nyaman dibaca sambil
                berbincang.
            </p>
            <div class="tr-menu-draft">
                <div class="tr-menu-header">
                    <div class="tr-menu-brand">
                        <img src="<?= base_url('images/temurasa_horizontal_fit.png'); ?>" alt="Temu Rasa" class="tr-menu-logo">
                        <h3 class="tr-menu-title">Menu</h3>
                    </div>
                    <p class="tr-menu-tagline">Tempat Bertemu, Tempat Berasa</p>
                </div>

                <div class="tr-menu-grid">
                    <div class="tr-menu-col">
                        <div class="tr-menu-group">
                            <h4>Signature &amp; Coffee</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">
                                        Temu Rasa Latte <span class="tr-menu-item-note">(Rekomendasi)</span>
                                    </span>
                                    <span class="tr-menu-price">18.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Kopi Susu Aren</span>
                                    <span class="tr-menu-price">17.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Es Kopi Temu</span>
                                    <span class="tr-menu-price">19.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="tr-menu-group">
                            <h4>Kopi &amp; Teh Panas</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Kopi Tubruk</span>
                                    <span class="tr-menu-price">10.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Kopi Hitam</span>
                                    <span class="tr-menu-price">11.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Teh Panas</span>
                                    <span class="tr-menu-price">8.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tr-menu-col">
                        <div class="tr-menu-group">
                            <h4>Non-Coffee &amp; Dingin</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Es Kopi Susu</span>
                                    <span class="tr-menu-price">15.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Es Coklat</span>
                                    <span class="tr-menu-price">14.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Es Teh Manis</span>
                                    <span class="tr-menu-price">8.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Lemon Tea</span>
                                    <span class="tr-menu-price">12.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Mineral Water</span>
                                    <span class="tr-menu-price">6.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tr-menu-col">
                        <div class="tr-menu-group">
                            <h4>Food &amp; Snack</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Roti Bakar</span>
                                    <span class="tr-menu-price">12.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Pisang Goreng</span>
                                    <span class="tr-menu-price">11.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Tahu Crispy</span>
                                    <span class="tr-menu-price">10.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Kentang Goreng</span>
                                    <span class="tr-menu-price">13.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="tr-menu-group">
                            <h4>Makanan Berat</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Nasi Goreng Temu</span>
                                    <span class="tr-menu-price">20.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Mie Goreng</span>
                                    <span class="tr-menu-price">17.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Ayam Geprek</span>
                                    <span class="tr-menu-price">22.000</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tr-menu-footer">
                    <p class="tr-menu-note">
                        * Menu di atas adalah dummy untuk keperluan desain. Harga dan item dapat disesuaikan saat operasional.
                    </p>
                </div>
            </div>
        </section>
        <?php if (false): ?>
            <section id="pos-cashier" class="tr-section">
                <h2 class="tr-section-title">POS UI - Cashier Screen (Mock)</h2>
                <p class="tr-text-sm">
                    Tampilan POS Temu Rasa dirancang sederhana, bersih, dan mudah dipahami. Warna mengikuti palet brand
                    dengan fokus pada keterbacaan dan kecepatan input pesanan di area kasir.
                </p>
                <div class="tr-pos-mock">
                    <div class="tr-pos-layout">
                        <div class="tr-pos-main">
                            <div class="tr-pos-topbar">
                                <div>
                                    <p class="tr-pos-topbar-title">Temu Rasa POS</p>
                                    <p class="tr-pos-topbar-sub">Kasir 01 - Meja / Takeaway</p>
                                </div>
                                <div class="tr-pos-topbar-right">
                                    <p>09:32</p>
                                    <p>Dummy Date</p>
                                </div>
                            </div>

                            <div class="tr-pos-search">
                                <div class="tr-pos-search-row">
                                    <input
                                        class="tr-pos-input"
                                        placeholder="Cari menu (contoh: kopi, mie, roti)">
                                    <button type="button" class="tr-btn tr-btn-primary">+ Custom</button>
                                </div>
                                <div class="tr-pos-categories">
                                    <span class="tr-pill tr-pill-active">Semua</span>
                                    <span class="tr-pill">Coffee</span>
                                    <span class="tr-pill">Non-Coffee</span>
                                    <span class="tr-pill">Snack</span>
                                    <span class="tr-pill">Makanan</span>
                                </div>
                            </div>

                            <div class="tr-pos-grid">
                                <?php foreach ($posProducts as $product): ?>
                                    <button type="button" class="tr-pos-item">
                                        <span class="tr-pos-item-name"><?= esc($product['name']); ?></span>
                                        <span class="tr-pos-item-desc"><?= esc($product['desc']); ?></span>
                                        <span class="tr-pos-item-price"><?= esc($product['price']); ?></span>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="tr-pos-side">
                            <div class="tr-pos-summary-header">
                                <h3>Pesanan</h3>
                                <span class="tr-muted">#DUMMY001</span>
                            </div>

                            <div class="tr-pos-summary-list">
                                <div class="tr-pos-summary-item">
                                    <div>
                                        <p class="tr-pos-summary-name">Temu Rasa Latte</p>
                                        <p class="tr-pos-summary-sub">1 x 18.000</p>
                                    </div>
                                    <span class="tr-pos-summary-total">18.000</span>
                                </div>
                                <div class="tr-pos-summary-item">
                                    <div>
                                        <p class="tr-pos-summary-name">Roti Bakar</p>
                                        <p class="tr-pos-summary-sub">1 x 12.000</p>
                                    </div>
                                    <span class="tr-pos-summary-total">12.000</span>
                                </div>
                                <div class="tr-pos-summary-item">
                                    <div>
                                        <p class="tr-pos-summary-name">Es Teh Manis</p>
                                        <p class="tr-pos-summary-sub">1 x 8.000</p>
                                    </div>
                                    <span class="tr-pos-summary-total">8.000</span>
                                </div>
                            </div>

                            <div class="tr-pos-totals">
                                <div class="tr-pos-total-line">
                                    <span>Subtotal</span>
                                    <span>38.000</span>
                                </div>
                                <div class="tr-pos-total-line">
                                    <span>Diskon</span>
                                    <span>0</span>
                                </div>
                                <div class="tr-pos-total-line tr-pos-total-strong">
                                    <span>Total</span>
                                    <span>38.000</span>
                                </div>
                            </div>

                            <div class="tr-pos-actions">
                                <div class="tr-pos-actions-row">
                                    <button class="tr-btn tr-btn-primary">Bayar Tunai</button>
                                    <button class="tr-btn tr-btn-outline">QR / Transfer</button>
                                </div>
                                <button class="tr-btn tr-btn-muted">Simpan / Bayar Nanti</button>
                            </div>

                            <p class="tr-text-xs tr-muted tr-italic">
                                * Tampilan ini adalah mock statis untuk panduan desain UI POS. Alur dan fungsi dapat dikembangkan
                                lebih lanjut pada aplikasi POS sebenarnya.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
            <section id="pos-dashboard" class="tr-section">
                <h2 class="tr-section-title">POS UI - Dashboard Screen (Mock)</h2>
                <p class="tr-text-sm">
                    Dashboard Temu Rasa memberikan ringkasan singkat performa harian dan membantu pemilik atau pengelola
                    melihat kondisi cafe secara cepat tanpa perlu membuka banyak menu.
                </p>
                <div class="tr-dash-mock">
                    <div class="tr-dash-top">
                        <div>
                            <p class="tr-dash-title">Temu Rasa POS</p>
                            <p class="tr-dash-subtitle">Dashboard - Ringkasan Hari Ini</p>
                        </div>
                        <div class="tr-dash-actions">
                            <select class="tr-dash-select">
                                <option>Hari ini</option>
                                <option>Minggu ini</option>
                                <option>Bulan ini</option>
                            </select>
                            <button class="tr-btn tr-btn-outline">Export</button>
                        </div>
                    </div>

                    <div class="tr-dash-cards">
                        <?php foreach ($dashSummary as $summary): ?>
                            <div class="tr-dash-card">
                                <span class="tr-muted"><?= esc($summary['label']); ?></span>
                                <span class="tr-dash-value"><?= esc($summary['value']); ?></span>
                                <span class="<?= esc($summary['noteClass']); ?> tr-text-xs"><?= esc($summary['note']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="tr-dash-grid">
                        <div class="tr-dash-chart">
                            <div class="tr-dash-chart-head">
                                <h3>Penjualan per Jam</h3>
                                <span class="tr-muted">Dummy data</span>
                            </div>
                            <div class="tr-dash-bars">
                                <?php foreach ($dashBars as $index => $height): ?>
                                    <div class="tr-dash-bar-item">
                                        <div class="tr-dash-bar" style="--bar-height: <?= (int) $height; ?>%;"></div>
                                        <span class="tr-dash-bar-label"><?= (int) ($index + 12); ?>.00</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="tr-dash-orders">
                            <div class="tr-dash-orders-head">
                                <h3>Transaksi Terakhir</h3>
                                <span class="tr-text-accent">Lihat semua</span>
                            </div>
                            <div class="tr-dash-orders-list">
                                <?php foreach ($dashOrders as $order): ?>
                                    <div class="tr-dash-order">
                                        <div>
                                            <p class="tr-dash-order-id"><?= esc($order['id']); ?></p>
                                            <p class="tr-dash-order-detail"><?= esc($order['detail']); ?></p>
                                        </div>
                                        <div class="tr-dash-order-meta">
                                            <p class="tr-dash-order-total"><?= esc($order['total']); ?></p>
                                            <p class="tr-dash-order-time"><?= esc($order['time']); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <div class="tr-dash-bottom">
                        <div class="tr-dash-note">
                            <h4>Catatan Operasional</h4>
                            <ul class="tr-list tr-text-xs">
                                <li>Stok gula aren menipis, cek gudang.</li>
                                <li>Feedback baik untuk rasa kopi susu.</li>
                            </ul>
                        </div>
                        <div class="tr-dash-note">
                            <h4>Status Stok (Ringkas)</h4>
                            <ul class="tr-dash-status tr-text-xs">
                                <li>Kopi biji utama: <span class="tr-text-accent">Cukup</span></li>
                                <li>Susu UHT: <span class="tr-text-warn">Perlu cek</span></li>
                                <li>Gas LPG: <span class="tr-text-accent">Aman</span></li>
                            </ul>
                        </div>
                        <div class="tr-dash-note">
                            <h4>Highlight Hari Ini</h4>
                            <p class="tr-text-xs tr-muted">
                                Cuaca cerah, ramai di jam sore. Pertimbangkan promo kecil untuk jam 14.00 - 16.00
                                agar sebaran pengunjung lebih merata.
                            </p>
                        </div>
                    </div>

                    <p class="tr-text-xs tr-muted tr-italic">
                        * Semua angka di atas adalah dummy untuk panduan desain. Struktur dan data aktual dapat disesuaikan
                        saat pengembangan sistem POS.
                    </p>
                </div>
            </section>
        <?php endif; ?>

        <section id="close-page" class="tr-section">
            <div class="tr-panel tr-panel-soft tr-close-card">
                <div class="tr-close-inner tr-stack">
                    <img src="<?= base_url('images/temurasa_horizontal_fit.png'); ?>" alt="Temu Rasa" class="tr-close-logo">
                    <h2 class="tr-close-title">Terima Kasih</h2>
                    <p class="tr-text-sm tr-muted">
                        Brand guideline ini dirancang untuk menjaga konsistensi Temu Rasa di setiap touchpoint.
                    </p>
                    <p class="tr-italic tr-muted">Tempat Bertemu, Tempat Berasa.</p>
                    <footer class="tr-branding-footer">
                        (c) Temu Rasa - Brand Guideline Draft (Preview)
                    </footer>
                </div>
            </div>
        </section>

    </div>
</div>
