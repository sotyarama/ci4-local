<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <title>Temu Rasa – Brand Guide</title>

    <!-- Reveal.js CDN (basic) -->
    <link rel="stylesheet" href="https://unpkg.com/reveal.js/dist/reveal.css">
    <link rel="stylesheet" href="https://unpkg.com/reveal.js/dist/theme/white.css" id="theme">

    <!-- Google Fonts: Nunito, Poppins, Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500&family=Nunito:wght@600;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">

    <!-- Temu Rasa custom theme (1:1 style dengan TSX) -->
    <link rel="stylesheet" href="<?= base_url('css/temurasa-reveal.css'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>

    <div class="reveal tr-bg">
        <div class="slides">

            <!-- SLIDE 1 – COVER -->
            <section class="tr-center">
                <h1 class="tr-title">Temu Rasa</h1>
                <h3 class="tr-subtitle">Brand & UI Guideline</h3>
                <p class="tr-tagline">Tempat Bertemu, Tempat Berasa.</p>
                <p class="tr-meta">Draft internal · <?= date('Y'); ?></p>
            </section>

            <!-- SLIDE 2 – BRAND ESSENCE -->
            <section>
                <h2>Brand Essence</h2>
                <p>Temu Rasa adalah ruang pertemuan yang hangat, ramah, dan minimalis.</p>
                <ul>
                    <li>Ruang untuk remaja, warga, dan pekerja yang singgah.</li>
                    <li>Bukan sekadar warkop, tapi titik temu cerita dan rasa.</li>
                    <li>Suasana tenang, tidak bising, dan visual estetik.</li>
                </ul>
            </section>

            <!-- SLIDE 3 – BRAND STORY -->
            <section>
                <h2>Brand Story</h2>
                <p>
                    Temu Rasa lahir dari ruang keluarga yang sederhana – tempat orang berkumpul,
                    berbicara, dan menerima tamu. Ruang itu berkembang menjadi titik temu baru
                    untuk sekitar.
                </p>
                <ul>
                    <li><strong>“Temu”</strong>: pertemuan antar manusia.</li>
                    <li><strong>“Rasa”</strong>: perasaan, pengalaman, dan cita rasa.</li>
                    <li>Setiap kunjungan diharapkan membawa hati pulang sedikit lebih ringan.</li>
                </ul>
            </section>

            <!-- SLIDE 4 – COLOR PALETTE -->
            <section>
                <h2>Color Palette</h2>
                <div class="tr-color-grid">
                    <div class="tr-color-card" data-name="Sage Green" data-hex="#7A9A6C"></div>
                    <div class="tr-color-card" data-name="Soft Cream" data-hex="#F4F1EA"></div>
                    <div class="tr-color-card" data-name="Warm Brown" data-hex="#A27C55"></div>
                    <div class="tr-color-card" data-name="Charcoal Soft" data-hex="#3A3A3A"></div>
                    <div class="tr-color-card" data-name="Sage Light" data-hex="#C8D7C0"></div>
                    <div class="tr-color-card" data-name="Warm Beige" data-hex="#E7DFD1"></div>
                </div>
                <p class="tr-note">
                    Palet warna dibuat lembut, natural, dan menenangkan – cocok untuk suasana teduh dan estetik.
                </p>
            </section>

            <!-- SLIDE 5 – TYPOGRAPHY -->
            <section>
                <h2>Typography</h2>
                <ul>
                    <li><strong>Heading:</strong> Nunito (rounded, warm)</li>
                    <li><strong>Subheading:</strong> Poppins</li>
                    <li><strong>Body:</strong> Inter atau sistem font modern</li>
                </ul>
                <p class="tr-note">
                    Karakter huruf: lembut, bersih, dan mudah dibaca. Hindari font dekoratif yang terlalu ramai.
                </p>
            </section>

            <!-- SLIDE 6 – LOGO MEANING -->
            <section>
                <h2>Makna Logo</h2>
                <h3>A. Dua Lingkaran – “Pertemuan”</h3>
                <ul>
                    <li>Dua individu yang bertemu secara setara.</li>
                    <li>Ruang yang inklusif, ramah, dan terbuka untuk siapa saja.</li>
                </ul>
                <h3>B. Loop / Infinity – “Rasa yang Mengalir”</h3>
                <ul>
                    <li>Perjalanan rasa, dinamika perasaan, dan cerita yang saling beririsan.</li>
                    <li>Makna kesinambungan dan kedekatan hubungan.</li>
                </ul>
                <p class="tr-note">
                    Kombinasi keduanya: Temu Rasa adalah tempat manusia bertemu dan rasa bersentuhan.
                </p>
            </section>

            <!-- SLIDE 7 – LOGO & CLEARSPACE -->
            <section>
                <h2>Logo Usage & Clearspace</h2>
                <ul>
                    <li>Gunakan logo di atas background terang: putih, Soft Cream, atau Sage Light.</li>
                    <li>Jaga <strong>clearspace</strong> di sekeliling logo minimal 1× tinggi lingkaran atas.</li>
                    <li>Hindari penambahan efek berat (shadow keras, outline tebal, glow).</li>
                    <li>Minimal size: 20 mm (print) · 64 px (digital).</li>
                </ul>
            </section>

            <!-- SLIDE 8 – BRAND PATTERN & VISUAL STYLE -->
            <section>
                <h2>Pattern & Visual Style</h2>
                <ul>
                    <li>Turunan dari bentuk loop dan lingkaran logo.</li>
                    <li>Dipakai sebagai aksen lembut di menu, cup, dan materi sosial media.</li>
                    <li>Hindari pattern yang terlalu kontras atau ramai.</li>
                </ul>
                <p class="tr-note">
                    Visual Temu Rasa harus terasa tenang, hangat, dan ringan – tidak agresif atau berisik.
                </p>
            </section>

            <!-- SLIDE 9 – PHOTOGRAPHY STYLE -->
            <section>
                <h2>Photography & Mood</h2>
                <ul>
                    <li>Tone warna hangat dan lembut (cream, hijau lembut, cokelat natural).</li>
                    <li>Cahaya natural, hindari flash keras.</li>
                    <li>Komposisi sederhana dengan cukup ruang kosong.</li>
                    <li>Subjek: momen kecil, minuman, meja, ekspresi santai.</li>
                </ul>
            </section>

            <!-- SLIDE 10 – POS UI MOCK (CASHIER) -->
            <section>
                <h2>POS UI – Cashier Screen</h2>
                <ul>
                    <li>Warna utama: Sage Green, Soft Cream, dan white.</li>
                    <li>Grid tombol menu sederhana, fokus ke keterbacaan.</li>
                    <li>Order summary di sisi kanan, total dan tombol bayar jelas.</li>
                    <li>Bahasa UI sederhana dan tidak mengintimidasi.</li>
                </ul>
                <p class="tr-note">
                    Tampilan POS harus membantu kasir bekerja cepat, minim distraksi, dan tetap selaras dengan brand.
                </p>
            </section>

            <!-- SLIDE 11 – POS UI MOCK (DASHBOARD) -->
            <section>
                <h2>POS UI – Dashboard</h2>
                <ul>
                    <li>Menampilkan ringkasan penjualan hari ini, transaksi, dan menu terlaris.</li>
                    <li>Grafik sederhana per jam, mudah dibaca sekilas.</li>
                    <li>Panel kecil: catatan operasional, status stok, highlight hari ini.</li>
                </ul>
                <p class="tr-note">
                    Tujuan: pemilik atau pengelola bisa “cek kondisi” dalam 10–20 detik saja.
                </p>
            </section>

            <!-- SLIDE 12 – MENU LAYOUT -->
            <section>
                <h2>Menu Layout – A4 Minimalis</h2>
                <ul>
                    <li>Satu halaman A4, dua kolom: kiri minuman, kanan snack & makanan berat.</li>
                    <li>Heading “TEMU RASA” dengan tracking lebar dan tagline kecil.</li>
                    <li>Gunakan Soft Cream sebagai background dan Sage Green untuk heading.</li>
                    <li>Harga rata kanan, deskripsi pendek di bawah nama menu.</li>
                </ul>
            </section>

            <!-- SLIDE 13 – TONE OF VOICE -->
            <section>
                <h2>Tone of Voice</h2>
                <ul>
                    <li>Hangat, ramah, dan natural.</li>
                    <li>Menggunakan bahasa sederhana, seperti percakapan sehari-hari.</li>
                    <li>Tidak agresif dalam promo, tidak menggurui, tidak menghakimi.</li>
                </ul>
                <p class="tr-note">
                    Contoh: “Yuk istirahat sebentar, kami siapkan minuman hangat buat nemenin kamu.”
                </p>
            </section>

            <!-- SLIDE 14 – PENUTUP -->
            <section class="tr-center">
                <h2>Terima Kasih</h2>
                <p>Brand guideline ini menjadi dasar semua visual & komunikasi Temu Rasa.</p>
                <p class="tr-note">Jika ragu: pilih yang paling hangat, paling lembut, dan paling membumi.</p>
            </section>

        </div>
    </div>

    <!-- Reveal.js script -->
    <script src="https://unpkg.com/reveal.js/dist/reveal.js"></script>
    <script>
        Reveal.initialize({
            hash: true,
            slideNumber: true
        });

        // Inisialisasi kartu warna dari data-attribute
        document.querySelectorAll('.tr-color-card').forEach(card => {
            const hex = card.getAttribute('data-hex');
            const name = card.getAttribute('data-name');
            card.style.backgroundColor = hex;
            card.innerHTML = `
      <div class="tr-color-name">${name}</div>
      <div class="tr-color-hex">${hex}</div>
    `;
        });
    </script>

</body>

</html>