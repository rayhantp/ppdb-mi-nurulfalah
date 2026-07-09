<?php
session_start();
include '../../config/database.php'; 

// 🔒 PROTEKSI: Pastikan hanya peran 'siswa' yang bisa masuk
if (!isset($_SESSION['nama_lengkap']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../../login.php"); 
    exit();
}

$nama_siswa = $_SESSION['nama_lengkap'];
$id_user = $_SESSION['id_user'];
$sudah_daftar = false;
$data_siswa = null;

// 📊 ENGINE 1: Ambil data Tahun Ajaran yang sedang Aktif dari Database
try {
    $stmt_ta = $pdo->query("SELECT tahun_ajaran FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
    $ta_aktif = $stmt_ta->fetchColumn();
    if (!$ta_aktif) {
        $ta_aktif = "2026/2027"; 
    }
} catch (PDOException $e) {
    $ta_aktif = "2026/2027";
}

// 📊 ENGINE 2: Tarik data profil registrasi siswa secara real-time
try {
    $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id_user = ? LIMIT 1");
    $stmt->execute([$id_user]);
    $data_siswa = $stmt->fetch(PDO::FETCH_ASSOC);

    // 🔥 REVISI LOGIKA: Anggap "Sudah Daftar" HANYA JIKA nomor pendaftaran resmi sudah terbit
    if ($data_siswa && !empty($data_siswa['no_pendaftaran']) && $data_siswa['no_pendaftaran'] !== 'Belum Generate') {
        $sudah_daftar = true; 
        $status_pendaftaran = $data_siswa['status_pendaftaran'] ?? 'proses';
        $no_reg = $data_siswa['no_pendaftaran'];
    } else {
        $sudah_daftar = false; 
    }
} catch (PDOException $e) {
    $sudah_daftar = false;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Dashboard Siswa - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        .active-link { background-color: #f0fdf4 !important; color: #15803d !important; border-right: 4px solid #15803d; font-weight: 700; }
        #content-area { animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <!-- Efek Latar Belakang Ambient Glow Premium -->
    <div class="absolute top-0 right-0 w-[650px] h-[650px] bg-gradient-to-bl from-green-200/15 to-emerald-100/5 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[450px] h-[450px] bg-blue-100/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>

    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Memanggil komponen sidebar_siswa -->
        <?php include 'sidebar_siswa.php'; ?>

        <!-- AREA UTAMA DASHBOARD WORKSPACE -->
        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-5xl mx-auto space-y-8">
                
                <!-- ─── HEADER HERO BANNER (PREMIUM GRADIENT) ─── -->
                <header class="bg-gradient-to-br from-green-800 via-green-900 to-emerald-800 rounded-[2.5rem] p-8 md:p-10 text-white shadow-xl shadow-green-900/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 relative overflow-hidden group">
                    <div class="absolute -right-10 -bottom-10 text-[12rem] opacity-5 pointer-events-none transform rotate-12 group-hover:scale-110 transition-transform duration-700">🕌</div>
                    <div class="relative z-10">
                        <span class="px-3 py-1 bg-white/10 border border-white/10 text-green-200 text-[10px] font-black uppercase font-mono tracking-widest rounded-lg">Student Portal</span>
                        <h1 class="text-3xl lg:text-4xl font-black tracking-tight mt-3">Selamat Datang, <?= htmlspecialchars($nama_siswa) ?>!</h1>
                        <p class="text-xs text-green-100/70 font-medium mt-1">Sistem Informasi Penerimaan Peserta Didik Baru MI Nurul Falah.</p>
                    </div>
                    <span class="relative z-10 bg-white/10 text-white text-[10px] font-mono font-black px-4 py-2.5 rounded-xl border border-white/10 uppercase tracking-widest bg-emerald-950/40">
                        Tahun Ajaran: <?= htmlspecialchars($ta_aktif) ?>
                    </span>
                </header>

                <!-- ─── GRID UTAMA UTAMA (2 KOLOM SPLIT) ─── -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    
                    <!-- SISI KIRI: KONTEN UTAMA STATUS & PROGRESS (2/3) -->
                    <div class="lg:col-span-2 space-y-8">
                        
                        <!-- KONDISI A: JIKA BELUM MENGISI FORMULIR -->
                        <?php if (!$sudah_daftar): ?>
                            <div class="bg-white border-2 border-dashed border-slate-200 rounded-[2.5rem] p-8 md:p-10 text-center space-y-4 shadow-sm hover:border-green-600/30 transition-all duration-300">
                                <div class="w-16 h-16 bg-amber-50 border border-amber-100 text-amber-600 text-3xl rounded-2xl flex items-center justify-center mx-auto shadow-sm">📝</div>
                                <h3 class="text-lg font-black text-slate-800 uppercase tracking-wide">Formulir Pendaftaran Belum Lengkap</h3>
                                <p class="text-xs text-slate-400 font-medium max-w-md mx-auto leading-relaxed">
                                    Akun Anda berhasil diverifikasi. Silakan lakukan pengisian berkas biodata calon siswa secara online untuk mendapatkan Nomor Registrasi resmi ujian masuk.
                                </p>
                                <div class="pt-4">
                                    <a href="formulir.php" class="inline-flex items-center bg-green-700 hover:bg-green-800 text-white px-8 py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-green-700/10 hover:translate-y-[-2px] active:scale-95">
                                        Mulai Isi Formulir Sekarang ➔
                                    </a>
                                </div>
                            </div>

                        <!-- KONDISI B: JIKA SUDAH BERHASIL MENGISI FORMULIR -->
                        <?php else: ?>
                            <!-- METER BOX DATA REGISTRASI -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] font-black uppercase font-mono text-slate-400 tracking-wider block">No Registrasi</span>
                                        <span class="text-base font-black text-green-700 font-mono mt-1 bg-green-50 border border-green-100 px-3 py-1 rounded-xl inline-block tracking-wider"><?= htmlspecialchars($no_reg) ?></span>
                                    </div>
                                    <span class="text-2xl">🆔</span>
                                </div>
                                <div class="bg-white border border-slate-100 p-6 rounded-3xl shadow-sm flex items-center justify-between">
                                    <div>
                                        <span class="text-[9px] font-black uppercase font-mono text-slate-400 tracking-wider block">Nomor NISN Resmi</span>
                                        <span class="text-base font-black text-slate-800 font-mono mt-1 inline-block"><?= htmlspecialchars($data_siswa['nisn'] ?? '-') ?></span>
                                    </div>
                                    <span class="text-2xl">📌</span>
                                </div>
                            </div>

                            <!-- BOX HASIL VERIFIKASI DARI OPERATOR -->
                            <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-sm">
                                <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono mb-4">Hasil Validasi Berkas Pelaksana</span>
                                
                                <?php if ($status_pendaftaran === 'diterima'): ?>
                                    <div class="p-6 bg-emerald-50 border border-emerald-100 rounded-3xl flex items-center gap-4 shadow-sm shadow-emerald-100/50">
                                        <span class="text-4xl animate-bounce">🎉</span>
                                        <div>
                                            <span class="text-xs font-black text-emerald-800 uppercase block">Selamat! Berkas Anda Diterima</span>
                                            <p class="text-[11px] text-emerald-600 font-medium mt-0.5">Anda dinyatakan lulus seleksi administrasi awal MI Nurul Falah. Silakan cetak bukti kelulusan di bawah ini.</p>
                                        </div>
                                    </div>
                                    <a href="cetak_bukti.php" target="_blank" class="w-full flex items-center justify-center space-x-2 bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-slate-800 transition-all mt-4 shadow-xl shadow-slate-900/10 active:scale-95">
                                        <span>🖨️ Cetak Kartu Bukti Kelulusan</span>
                                    </a>
                                <?php elseif ($status_pendaftaran === 'ditolak'): ?>
                                    <div class="p-6 bg-red-50 border border-red-100 rounded-3xl flex items-center gap-4">
                                        <span class="text-4xl">❌</span>
                                        <div>
                                            <span class="text-xs font-black text-red-800 uppercase block">Mohon Maaf, Berkas Belum Lolos</span>
                                            <p class="text-[11px] text-red-600 font-medium mt-0.5">Ada data/lampiran berkas scan yang salah. Harap periksa menu berkas atau hubungi panitia untuk perbaikan.</p>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="p-6 bg-amber-50 border border-amber-100 rounded-3xl flex items-center gap-4">
                                        <span class="text-4xl animate-pulse">⏱️</span>
                                        <div>
                                            <span class="text-xs font-black text-amber-800 uppercase block">Sedang Ditinjau Panitia</span>
                                            <p class="text-[11px] text-amber-600 font-medium mt-0.5">Berkas administrasi Anda telah masuk sistem antrean validasi data. Silakan pantau berkala dalam 1x24 jam.</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <!-- ─── PETA ALUR PROGRESS TIMELINE SELEKSI (PREMIUM UI) ─── -->
                        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 md:p-8 shadow-sm">
                            <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono mb-6">Tahapan Pendaftaran Valid</span>
                            
                            <div class="relative space-y-6 pl-6 border-l-2 border-slate-100">
                                <!-- Step 1 -->
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-0 w-4 h-4 bg-green-700 rounded-full border-4 border-white shadow-sm"></span>
                                    <h4 class="text-xs font-black text-slate-800 uppercase">1. Registrasi Akun Portal</h4>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Selesai dibuat secara sah di database.</p>
                                </div>
                                <!-- Step 2 -->
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm <?= $sudah_daftar ? 'bg-green-700' : 'bg-slate-200' ?>"></span>
                                    <h4 class="text-xs font-black <?= $sudah_daftar ? 'text-slate-800' : 'text-slate-400' ?> uppercase">2. Pengisian Formulir Biodata</h4>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Merekam data Seksi Keterangan Siswa, Orang Tua, dan Wali.</p>
                                </div>
                                <!-- Step 3 -->
                                <div class="relative">
                                    <?php 
                                    // Anggap berkas selesai diisi kalau sudah input form (atau bisa disesuaikan kolom berkas_kk kamu nanti)
                                    $berkas_siap = ($sudah_daftar && !empty($data_siswa['asal_sekolah']));
                                    ?>
                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm <?= $berkas_siap ? 'bg-green-700' : 'bg-slate-200' ?>"></span>
                                    <h4 class="text-xs font-black <?= $berkas_siap ? 'text-slate-800' : 'text-slate-400' ?> uppercase">3. Unggah Dokumen Digital</h4>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Melampirkan hasil scan Kartu Keluarga dan Akta Kelahiran resmi.</p>
                                </div>
                                <!-- Step 4 -->
                                <div class="relative">
                                    <span class="absolute -left-[31px] top-0 w-4 h-4 rounded-full border-4 border-white shadow-sm <?= ($sudah_daftar && $status_pendaftaran === 'diterima') ? 'bg-green-700' : 'bg-slate-200' ?>"></span>
                                    <h4 class="text-xs font-black <?= ($sudah_daftar && $status_pendaftaran === 'diterima') ? 'text-slate-800' : 'text-slate-400' ?> uppercase">4. Pengumuman Kelulusan Berkas</h4>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Mendapatkan lembar verifikasi kartu nomor induk siswa baru.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- SISI KANAN: UTILITY CARDS / PROFIL MINI (1/3) -->
                    <div class="space-y-6">
                        
                        <!-- KARTU PROFIL RINGKASAN DATA SISWA (Hanya muncul jika sudah daftar) -->
                        <?php if ($sudah_daftar && $data_siswa): ?>
                            <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-sm space-y-4">
                                <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono border-b pb-2">📋 Resume Identitas</span>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold block">Nama Calon Murid:</span>
                                        <span class="text-xs font-black text-slate-800 uppercase"><?= htmlspecialchars($data_siswa['nama_siswa']) ?></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold block">Gender / Kelamin:</span>
                                        <span class="text-xs font-bold text-slate-600"><?= $data_siswa['jenis_kelamin'] === 'L' ? '👦 Laki-laki' : '👧 Perempuan' ?></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-bold block">Asal Sekolah RA/TK:</span>
                                        <span class="text-xs font-bold text-slate-600 truncate block"><?= htmlspecialchars($data_siswa['asal_sekolah'] ?? '-') ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- KONTAK HELPDESK PANDUAN UTILITY -->
                        <div class="bg-white border border-slate-100 rounded-[2.5rem] p-6 shadow-sm space-y-4">
                            <div class="flex items-center space-x-2 border-b pb-3 border-slate-50">
                                <span class="text-lg">📌</span>
                                <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono">Pusat Bantuan PPDB</span>
                            </div>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">Jika menemui kendala pengisian sistem pendaftaran online atau pengunggahan scan PDF berkas formulir pendaftaran, hubungi WhatsApp layanan kami:</p>
                            <a href="https://wa.me/628123456789" target="_blank" class="w-full flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white py-3.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-md active:scale-95">
                                <span>💬 Chat Panitia Sekolah</span>
                            </a>
                        </div>

                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- ─── FIX JAVASCRIPT: UPDATE SIDEBAR AKTIF & ROUTER JALUR AMAN ─── -->
    <script>
        document.querySelectorAll('.nav-link').forEach(nav => {
            if(nav.getAttribute('href') === window.location.pathname.split('/').pop()) {
                nav.classList.add('active-link');
                nav.classList.remove('text-slate-400');
            }
        });
    </script>
</body>
</html>