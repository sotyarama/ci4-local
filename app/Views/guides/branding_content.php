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
        <?php if (! empty($showExport) && (! empty($pdfUrl) || ! empty($printUrl))): ?>
            <div style="display:flex; justify-content:flex-end; gap:8px; margin-bottom:12px;">
                <?php if (! empty($printUrl)): ?>
                    <a href="<?= esc($printUrl); ?>" class="tr-btn tr-btn-muted" style="text-decoration:none;">
                        Print / Save PDF
                    </a>
                <?php endif; ?>
                <?php if (! empty($pdfUrl)): ?>
                    <a href="<?= esc($pdfUrl); ?>" class="tr-btn tr-btn-outline" style="text-decoration:none;">Export PDF</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="tr-branding-logo-wrap">
            <img src="<?= esc($logoSrc ?? base_url('images/temurasa_primary_fit.png')); ?>" alt="Temu Rasa Logo">
        </div>

        <header class="tr-branding-header">
            <h1>Temu Rasa - Brand Guideline</h1>
            <p class="tr-branding-subtitle">Tempat Bertemu, Tempat Berasa.</p>
        </header>

        <section id="essence" class="tr-section">
            <h2 class="tr-section-title">Brand Essence</h2>
            <p>
                Temu Rasa adalah brand yang hangat, ramah, dan minimalis. Ia hadir sebagai ruang
                pertemuan, tempat orang berbagi cerita dan menikmati rasa dalam suasana yang lembut
                dan bersahabat.
            </p>
        </section>

        <section id="story" class="tr-section">
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
        </section>
        <section id="colors" class="tr-section">
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
        </section>

        <section id="typography" class="tr-section">
            <h2 class="tr-section-title">Typography</h2>
            <ul class="tr-list">
                <li><strong>Heading:</strong> Nunito (rounded, warm)</li>
                <li><strong>Subheading:</strong> Poppins</li>
                <li><strong>Body Text:</strong> Inter</li>
            </ul>
        </section>

        <section id="logo-system" class="tr-section">
            <h2 class="tr-section-title">Logo System</h2>
            <div class="tr-grid tr-grid-3">
                <div class="tr-panel">
                    <h3 class="tr-subtitle">Primary Logo</h3>
                    <div class="tr-placeholder">Logo utama</div>
                    <p class="tr-text-sm">
                        Digunakan untuk sebagian besar kebutuhan: papan nama, menu, web,
                        poster, dan materi utama lainnya.
                    </p>
                </div>
                <div class="tr-panel">
                    <h3 class="tr-subtitle">Secondary Logo (Symbol)</h3>
                    <div class="tr-placeholder circle">Ikon</div>
                    <p class="tr-text-sm">
                        Digunakan ketika ruang sangat terbatas, seperti stiker kecil, cup
                        lid, favicon, atau watermark.
                    </p>
                </div>
                <div class="tr-panel">
                    <h3 class="tr-subtitle">Horizontal Logo</h3>
                    <div class="tr-placeholder wide">Logo horizontal</div>
                    <p class="tr-text-sm">
                        Digunakan untuk area desain melebar, seperti header website, kop
                        surat, POS header, atau spanduk.
                    </p>
                </div>
            </div>
        </section>

        <section id="logo-meaning" class="tr-section">
            <h2 class="tr-section-title">Makna Logo</h2>
            <div class="tr-panel tr-stack">
                <div>
                    <h3 class="tr-subtitle">A. Dua Lingkaran - "Pertemuan"</h3>
                    <p>
                        Dua lingkaran identik melambangkan dua individu yang bertemu secara setara: saling menerima,
                        saling terhubung, dan hadir dalam ruang yang sama. Lingkaran adalah bentuk universal tanpa awal
                        maupun akhir, menggambarkan bahwa Temu Rasa adalah ruang terbuka bagi siapa pun - remaja,
                        warga, hingga pekerja yang singgah. Bentuk ini menegaskan karakter brand sebagai tempat pertemuan
                        yang inklusif, ramah, dan tidak mengintimidasi.
                    </p>
                </div>
                <div>
                    <h3 class="tr-subtitle">B. Garis Infinity / Loop - "Rasa yang Mengalir"</h3>
                    <p>
                        Garis lengkung bersilangan yang menyerupai simbol infinity menggambarkan perjalanan rasa yang
                        mengalir, dinamika perasaan, serta cerita yang saling beririsan. Bentuk ini merepresentasikan
                        hubungan antar manusia yang terus berkembang, percakapan yang terjadi di meja kopi, dan momen-momen
                        kecil yang memberi warna pada hari. Loop ini menghadirkan makna tentang kesinambungan, pertumbuhan,
                        dan pengalaman yang tidak terputus.
                    </p>
                </div>
                <div>
                    <h3 class="tr-subtitle">C. Keharmonisan Keduanya</h3>
                    <p>
                        Ketika dua lingkaran (manusia) dipadukan dengan garis lengkung (rasa/perjalanan), tercipta simbol
                        besar tentang perjumpaan: tempat di mana manusia bertemu, rasa bersentuhan, dan cerita saling
                        terhubung. Logo ini menjadi representasi visual dari positioning Temu Rasa sebagai "warkop naik
                        kelas yang estetik", tempat modern bagi remaja untuk berkumpul.
                    </p>
                </div>
                <div>
                    <h3 class="tr-subtitle">Makna Warna - Sage Green</h3>
                    <p>
                        Sage green dipilih sebagai warna utama karena menghadirkan keteduhan, kesederhanaan, keseimbangan,
                        dan kehangatan. Warna ini modern, estetik, natural, dan sangat relevan bagi generasi muda. Selain
                        itu, sage green memberi kesan elegan tanpa terkesan mahal atau berjarak, sehingga Temu Rasa dapat
                        menjadi tempat yang terasa nyaman bagi semua kalangan.
                    </p>
                </div>
                <div>
                    <h3 class="tr-subtitle">Pesan Brand yang Tersirat</h3>
                    <ul class="tr-list">
                        <li><strong>Ramah dan terbuka</strong> - bentuk sederhana yang mudah dipahami.</li>
                        <li><strong>Modern dan estetik</strong> - clean-line, cocok dengan budaya visual remaja.</li>
                        <li><strong>Hangat dan bersahabat</strong> - kurva lembut tanpa sudut tajam.</li>
                        <li><strong>Naik kelas namun membumi</strong> - warkop modern tanpa kehilangan keaslian.</li>
                        <li><strong>Mudah diaplikasi</strong> - fleksibel digunakan di signage, menu, cup, stiker, dan merchandise.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Kekuatan Logo Ini</h3>
                    <ul class="tr-list">
                        <li>Unik namun mudah dikenali.</li>
                        <li>Estetik dan sangat menarik bagi target muda (remaja).</li>
                        <li>Clean, tidak over-designed, dan relevan dengan tren desain modern.</li>
                        <li>Fleksibel untuk berbagai format: vertikal, horizontal, ataupun ikon.</li>
                        <li>Mudah dibuat versi hitam-putih untuk print hemat biaya.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Rekomendasi Pengembangan</h3>
                    <ul class="tr-list">
                        <li>Gunakan versi primary (simbol + teks) untuk kebutuhan utama.</li>
                        <li>Gunakan versi secondary (ikon saja) untuk stiker kecil atau watermark.</li>
                        <li>Eksplorasi komposisi logo dengan teks di bawah atau di samping simbol.</li>
                        <li>Gunakan font modern-berkesan-hangat seperti Inter, Poppins, Montserrat, atau Sora.</li>
                    </ul>
                </div>
            </div>
        </section>
        <section id="logo-grid" class="tr-section">
            <h2 class="tr-section-title">Logo Construction Grid</h2>
            <p class="tr-text-sm">
                Construction grid membantu menjaga proporsi dan konsistensi logo Temu Rasa ketika digunakan pada berbagai
                ukuran dan media. Grid ini menjadi panduan visual untuk memastikan keseimbangan bentuk lingkaran dan loop
                tetap harmonis.
            </p>
            <div class="tr-panel tr-stack">
                <div class="tr-logo-grid-wrap">
                    <div class="tr-logo-grid">
                        <div class="tr-logo-grid-lines">
                            <?php for ($i = 0; $i < 36; $i++): ?>
                                <span></span>
                            <?php endfor; ?>
                        </div>
                        <div class="tr-logo-grid-label">Logo Here</div>
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
        </section>

        <section id="clearspace" class="tr-section">
            <h2 class="tr-section-title">Clearspace and Minimum Size</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Clearspace adalah ruang aman di sekitar logo yang memastikan logo tetap terlihat jelas
                    dan tidak terganggu oleh elemen visual lain. Area ini wajib dijaga pada semua media
                    untuk mempertahankan integritas identitas visual Temu Rasa.
                </p>
                <div class="tr-clearspace-wrap">
                    <div class="tr-clearspace-box">
                        <div class="tr-clearspace-inner"></div>
                        <div class="tr-clearspace-label">Logo Here</div>
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
                    <li><strong>Ikon (symbol-only):</strong> minimum 24 px</li>
                </ul>
            </div>
        </section>

        <section id="color-usage" class="tr-section">
            <h2 class="tr-section-title">Color Usage System</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Sistem penggunaan warna memastikan identitas Temu Rasa tetap konsisten, mudah dikenali,
                    dan memiliki kontras visual yang baik di berbagai media. Warna utama seperti Sage Green
                    dan Soft Cream digunakan untuk membangun karakter hangat, natural, dan estetik.
                </p>
                <div class="tr-stack-sm">
                    <h3 class="tr-subtitle">Background yang Disarankan</h3>
                    <div class="tr-grid tr-grid-4 tr-color-samples">
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
                <div class="tr-stack-sm">
                    <h3 class="tr-subtitle">Background yang Tidak Disarankan</h3>
                    <div class="tr-grid tr-grid-4 tr-color-samples">
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
        </section>
        <section id="brand-pattern" class="tr-section">
            <h2 class="tr-section-title">Brand Pattern and Graphic Elements</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Brand pattern Temu Rasa berasal dari bentuk loop pada logo - garis lengkung
                    yang lembut, mengalir, dan harmonis. Elemen ini digunakan untuk menciptakan identitas
                    visual yang konsisten pada menu, packaging, konten media sosial, dan dekorasi ruang.
                </p>
                <div>
                    <h3 class="tr-subtitle">Gaya Pattern yang Direkomendasikan</h3>
                    <div class="tr-grid tr-grid-3 tr-pattern-grid">
                        <div class="tr-pattern-card">
                            <div class="tr-pattern tr-pattern-wave"></div>
                            <p class="tr-text-xs">
                                Wave Line - turunan langsung dari lengkung logo.
                                Cocok untuk header menu dan frame media sosial.
                            </p>
                        </div>
                        <div class="tr-pattern-card">
                            <div class="tr-pattern tr-pattern-dots"></div>
                            <p class="tr-text-xs">
                                Soft Dots - interpretasi lembut dari dua lingkaran.
                                Cocok untuk stiker dan elemen interior.
                            </p>
                        </div>
                        <div class="tr-pattern-card">
                            <div class="tr-pattern tr-pattern-diagonal"></div>
                            <p class="tr-text-xs">
                                Diagonal Soft Texture - memberi nuansa hangat dan estetik.
                                Cocok untuk kartu menu dan poster.
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="tr-subtitle">Rekomendasi Penggunaan Pattern</h3>
                    <ul class="tr-list">
                        <li>Pola harus dipakai secara halus agar tidak mengalahkan logo.</li>
                        <li>Gunakan warna Sage Light atau Warm Beige untuk menjaga kesan lembut.</li>
                        <li>Pattern sebaiknya digunakan sebagai aksen, bukan elemen utama.</li>
                        <li>Hindari pattern dengan warna terlalu gelap atau kontras yang tinggi.</li>
                        <li>Ideal untuk background menu, frame Instagram, stiker, atau dekor sudut interior.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Do and Don't</h3>
                    <div class="tr-grid tr-grid-2">
                        <div class="tr-panel tr-panel-soft">
                            <h4 class="tr-subtitle">Do</h4>
                            <ul class="tr-list tr-text-xs">
                                <li>Gunakan pattern tipis / subtle untuk menjaga estetika.</li>
                                <li>Pastikan pattern tidak mengganggu teks utama.</li>
                                <li>Gunakan untuk memperkuat suasana hangat dan natural.</li>
                            </ul>
                        </div>
                        <div class="tr-panel tr-panel-warn">
                            <h4 class="tr-subtitle">Don't</h4>
                            <ul class="tr-list tr-text-xs">
                                <li>Jangan memakai pattern tebal / terlalu ramai.</li>
                                <li>Jangan menggunakan warna neon atau warna jenuh tinggi.</li>
                                <li>Jangan menempatkan pattern terlalu dekat dengan logo utama.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="tone" class="tr-section">
            <h2 class="tr-section-title">Tone of Voice</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Tone of Voice Temu Rasa mencerminkan suasana hangat, ramah, dan sederhana. Cara brand berbicara
                    harus terasa seperti percakapan santai yang membuat pengunjung merasa diterima dan nyaman.
                </p>
                <div>
                    <h3 class="tr-subtitle">Karakter Utama</h3>
                    <ul class="tr-list">
                        <li><strong>Hangat</strong> - nada bicara bersahabat, tidak kaku.</li>
                        <li><strong>Ramah</strong> - menggunakan kalimat sederhana dan mudah dipahami.</li>
                        <li><strong>Natural</strong> - menghindari kata-kata yang terlalu formal atau teknis.</li>
                        <li><strong>Positif</strong> - menghadirkan suasana yang teduh dan menyenangkan.</li>
                        <li><strong>Inklusif</strong> - tidak menggurui, tidak menyinggung, dan terasa dekat.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Panduan Gaya Penulisan</h3>
                    <ul class="tr-list">
                        <li>Gunakan kalimat pendek dan jelas.</li>
                        <li>Pilih kata-kata yang halus dan lembut.</li>
                        <li>Gunakan kata ganti yang akrab seperti <strong>kita</strong> atau <strong>teman-teman</strong>.</li>
                        <li>Hindari nada berjualan yang terlalu agresif.</li>
                        <li>Sisipkan sentuhan emosional pada kalimat tertentu untuk membangun kedekatan.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Contoh Penulisan (Do)</h3>
                    <div class="tr-tone-card">
                        <p>"Hari ini mau istirahat sejenak? Yuk mampir, kita siapkan minuman hangat buat nemenin kamu."</p>
                        <p>"Terima kasih sudah singgah. Semoga harimu jadi lebih ringan."</p>
                        <p>"Butuh tempat ngobrol? Temu Rasa selalu terbuka untuk kamu."</p>
                    </div>
                </div>
                <div>
                    <h3 class="tr-subtitle">Contoh Penulisan (Don't)</h3>
                    <div class="tr-tone-card tr-tone-card-warn">
                        <p>"Promo besar! Beli sekarang sebelum kehabisan!!!"</p>
                        <p>"Kami menghimbau pelanggan untuk mengikuti peraturan yang berlaku."</p>
                        <p>"Tempat ini hanya untuk pembeli, dilarang duduk tanpa memesan."</p>
                    </div>
                </div>
                <p class="tr-italic tr-muted">
                    Suara Temu Rasa adalah suara yang mengundang: lembut, ramah, dan membuat orang ingin kembali.
                </p>
            </div>
        </section>

        <section id="logo-usage" class="tr-section">
            <h2 class="tr-section-title">Logo Usage</h2>
            <p class="tr-text-sm">
                Gunakan kombinasi simbol dua titik + loop dan teks lowercase "temu rasa". Disarankan
                menggunakan background terang (cream/putih). Hindari distorsi, penambahan efek berat,
                atau penggunaan warna di luar palet resmi.
            </p>
        </section>

        <section id="photography" class="tr-section">
            <h2 class="tr-section-title">Photography and Visual Style</h2>
            <div class="tr-panel tr-stack">
                <p>
                    Gaya foto Temu Rasa harus mendukung kesan hangat, tenang, dan estetik. Foto digunakan untuk
                    menggambarkan suasana ruang, menu, dan momen kebersamaan tanpa terasa berlebihan.
                </p>
                <div>
                    <h3 class="tr-subtitle">Tone Warna</h3>
                    <ul class="tr-list">
                        <li>Gunakan tone hangat dan lembut: krem, hijau lembut, dan cokelat natural.</li>
                        <li>Hindari saturasi tinggi dan kontras ekstrem; pilih warna yang calming.</li>
                        <li>Usahakan pencahayaan natural (dekat jendela) dengan bayangan lembut.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Komposisi dan Framing</h3>
                    <ul class="tr-list">
                        <li>Gunakan komposisi sederhana dengan cukup ruang kosong (negative space).</li>
                        <li>Fokus pada satu subjek utama per frame: cangkir, meja, atau ekspresi pengunjung.</li>
                        <li>Ambil sudut 45 derajat atau eye-level untuk menonjolkan kedekatan dan kehangatan.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Subjek yang Direkomendasikan</h3>
                    <ul class="tr-list">
                        <li>Detail minuman dan makanan dengan prop sederhana di sekelilingnya.</li>
                        <li>Meja dengan buku, laptop, atau catatan untuk menggambarkan suasana santai produktif.</li>
                        <li>Momen kebersamaan kecil: dua orang sedang mengobrol, senyum, atau tertawa pelan.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="tr-subtitle">Hal yang Perlu Dihindari</h3>
                    <ul class="tr-list">
                        <li>Foto dengan flash keras yang membuat suasana terasa kaku atau dingin.</li>
                        <li>Foto yang terlalu ramai dengan banyak elemen tidak penting.</li>
                        <li>Filter berlebihan yang mengubah warna utama brand secara drastis.</li>
                    </ul>
                </div>
            </div>
        </section>
        <section id="applications" class="tr-section">
            <h2 class="tr-section-title">Application Examples</h2>
            <p class="tr-text-sm">
                Style dapat diterapkan pada: menu cetak, cup, stiker, website, POS UI, signage, dan
                social media. Gunakan palet warna natural dan layout minimalis.
            </p>
            <div class="tr-grid tr-grid-3">
                <div class="tr-panel tr-panel-sm">
                    <h3 class="tr-subtitle">Menu Cetak</h3>
                    <p class="tr-text-xs">
                        Gunakan background Soft Cream, heading Sage Green, dan body text Charcoal Soft.
                        Jaga ruang kosong agar menu terasa ringan dan mudah dibaca.
                    </p>
                </div>
                <div class="tr-panel tr-panel-sm">
                    <h3 class="tr-subtitle">Cup and Packaging</h3>
                    <p class="tr-text-xs">
                        Logo symbol-only cocok untuk stiker cup. Gunakan pattern lembut sebagai aksen di
                        bagian bawah atau samping.
                    </p>
                </div>
                <div class="tr-panel tr-panel-sm">
                    <h3 class="tr-subtitle">Social Media</h3>
                    <p class="tr-text-xs">
                        Gunakan foto dengan tone hangat, kombinasikan dengan frame Sage Green dan teks
                        pendek yang hangat dan mengundang.
                    </p>
                </div>
            </div>
        </section>

        <section id="menu-layout" class="tr-section">
            <h2 class="tr-section-title">Menu Layout - Draft (A4 Minimalis)</h2>
            <p class="tr-text-sm">
                Konsep awal layout menu Temu Rasa untuk cetak A4. Menggunakan palet warna brand, ruang kosong yang
                nyaman, serta tipografi yang bersih untuk meningkatkan keterbacaan.
            </p>
            <div class="tr-menu-draft">
                <div class="tr-menu-header">
                    <h3 class="tr-menu-title">TEMU RASA</h3>
                    <p class="tr-menu-subtitle">Tempat Bertemu, Tempat Berasa</p>
                    <div class="tr-menu-divider"></div>
                    <p class="tr-menu-label">Menu</p>
                </div>

                <div class="tr-menu-grid">
                    <div class="tr-menu-col">
                        <div class="tr-menu-group">
                            <h4>
                                Signature Coffee
                                <span class="tr-menu-badge">Rekomendasi</span>
                            </h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Temu Rasa Latte</span>
                                        <p class="tr-menu-item-desc">kopi susu lembut dengan hint caramel</p>
                                    </div>
                                    <span class="tr-menu-price">18.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Kopi Susu Aren</span>
                                        <p class="tr-menu-item-desc">gula aren lokal, rasa manis seimbang</p>
                                    </div>
                                    <span class="tr-menu-price">17.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Es Kopi Temu</span>
                                        <p class="tr-menu-item-desc">signature es kopi creamy</p>
                                    </div>
                                    <span class="tr-menu-price">19.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="tr-menu-group">
                            <h4>Kopi dan Teh Panas</h4>
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
                                <div class="tr-menu-item">
                                    <span class="tr-menu-item-title">Teh Jahe</span>
                                    <span class="tr-menu-price">10.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="tr-menu-group">
                            <h4>Non-Coffee dan Dingin</h4>
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
                            <h4>Snack Teman Ngobrol</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Roti Bakar</span>
                                        <p class="tr-menu-item-desc">coklat / keju / meses</p>
                                    </div>
                                    <span class="tr-menu-price">12.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Pisang Goreng</span>
                                        <p class="tr-menu-item-desc">disajikan hangat</p>
                                    </div>
                                    <span class="tr-menu-price">11.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Tahu Crispy</span>
                                        <p class="tr-menu-item-desc">dengan sambal kecap</p>
                                    </div>
                                    <span class="tr-menu-price">10.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Kentang Goreng</span>
                                        <p class="tr-menu-item-desc">plus saus dan mayo</p>
                                    </div>
                                    <span class="tr-menu-price">13.000</span>
                                </div>
                            </div>
                        </div>

                        <div class="tr-menu-group">
                            <h4>Makanan Berat</h4>
                            <div class="tr-menu-items">
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Nasi Goreng Temu</span>
                                        <p class="tr-menu-item-desc">dengan telur dan kerupuk</p>
                                    </div>
                                    <span class="tr-menu-price">20.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Mie Goreng</span>
                                        <p class="tr-menu-item-desc">sayur dan telur</p>
                                    </div>
                                    <span class="tr-menu-price">17.000</span>
                                </div>
                                <div class="tr-menu-item">
                                    <div class="tr-menu-item-info">
                                        <span class="tr-menu-item-title">Ayam Geprek</span>
                                        <p class="tr-menu-item-desc">level sambal bisa dipilih</p>
                                    </div>
                                    <span class="tr-menu-price">22.000</span>
                                </div>
                            </div>
                        </div>

                        <p class="tr-menu-note">
                            * Menu di atas adalah dummy untuk keperluan desain. Harga dan item dapat disesuaikan saat operasional.
                        </p>
                    </div>
                </div>
            </div>
        </section>
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

        <footer class="tr-branding-footer">
            (c) Temu Rasa - Brand Guideline Draft (Preview)
        </footer>
    </div>
</div>
