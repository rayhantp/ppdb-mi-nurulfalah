<?php
session_start();
include '../../config/database.php'; // 🔒 Menghubungkan file koneksi database utama

if (!isset($_SESSION['nama_lengkap']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../../login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$message = "";

// 📊 1. ENGINE AUTO-FILL TAHUN AJARAN: Otomatis mencari tahun ajaran berstatus 'aktif'
try {
    $stmt_ta = $pdo->query("SELECT tahun_ajaran FROM tahun_ajaran WHERE status = 'aktif' LIMIT 1");
    $ta_aktif = $stmt_ta->fetchColumn();
    if (!$ta_aktif) {
        $ta_aktif = "2026/2027"; // Fallback aman jika belum diatur admin
    }
} catch (PDOException $e) {
    $ta_aktif = "2026/2027";
}

// 🔍 2. AMBIL DATA LAMA (Untuk mengisi otomatis value input form jika sudah pernah tersimpan)
// Cari di tabel master pendaftar terlebih dahulu
$stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id_user = ?");
$stmt->execute([$id_user]);
$pendaftar = $stmt->fetch();

$id_pendaftar = $pendaftar ? $pendaftar['id_pendaftar'] : null;
$orang_tua = null;

// Jika data pendaftar ada, ambil data orang tuanya menggunakan id_pendaftar (Foreign Key)
if ($id_pendaftar) {
    $stmt_ot = $pdo->prepare("SELECT * FROM orang_tua WHERE id_pendaftar = ?");
    $stmt_ot->execute([$id_pendaftar]);
    $orang_tua = $stmt_ot->fetch();
}

// 🔥 FIX KEAMANAN UTAMA: Akun baru dianggap sudah isi form HANYA JIKA no_pendaftaran resmi sudah terbit
$sudah_isi_form = ($pendaftar && !empty($pendaftar['no_pendaftaran']) && $pendaftar['no_pendaftaran'] !== 'Belum Generate');

// 💾 LOGIKA SIMPAN DATA DENGAN TRANSACTION (GABUNGAN INSERT/UPDATE DUA TABEL)
if (isset($_POST['simpan_semua'])) {
    
    // Kelompok 1: Kolom-kolom khusus untuk masuk ke tabel 'pendaftar'
    $fields_pendaftar = [
        'nisn', 'tahun_ajaran', 'nama_siswa', 'nama_panggilan', 'tempat_lahir', 'tanggal_lahir', 
        'jenis_kelamin', 'agama', 'anak_ke', 'status_keluarga', 'jml_saudara_kandung', 'jml_adik', 
        'jml_kakak', 'asal_sekolah', 'alamat', 'rt', 'rw', 'desa', 'kec', 'kota', 
        'hobi', 'bidang_studi', 'olahraga', 'cita_cita'
    ];

    // Kelompok 2: Kolom-kolom khusus untuk masuk ke tabel 'orang_tua'
    $fields_ortu = [
        'nama_ayah', 'tmpt_lahir_ayah', 'tgl_lahir_ayah', 'pekerjaan_ayah', 'pendidikan_ayah', 'status_ayah', 'no_telp_ayah',
        'nama_ibu', 'tmpt_lahir_ibu', 'tgl_lahir_ibu', 'pekerjaan_ibu', 'pendidikan_ibu', 'status_ibu', 'no_telp_ibu', 'alamat_ortu',
        'rt_ortu', 'rw_ortu', 'desa_ortu', 'kec_ortu', 'kota_ortu',
        'nama_wali', 'jk_wali', 'tmpt_lahir_wali', 'tgl_lahir_wali', 'agama_wali', 'pekerjaan_wali', 
        'alamat_wali', 'rt_wali', 'rw_wali', 'desa_wali', 'kec_wali', 'kota_wali', 'penghasilan_wali'
    ];

    // AUTO-GENERATOR NO REGISTRASI: Diterbitkan acak jika siswa baru pertama kali mengisi form
    if (!$sudah_isi_form) {
        $fields_pendaftar[] = 'no_pendaftaran';
        $_POST['no_pendaftaran'] = 'REG-' . date('Y') . '-' . rand(10000, 99999);
    }

    // Mulai Database Transaction (Proteksi data ganda)
    try {
        $pdo->beginTransaction();

        // ----------------------------------------------------
        // PROSES 1: EKSEKUSI DATA TABEL 'pendaftar'
        // ----------------------------------------------------
        $data_pendaftar = [];
        foreach ($fields_pendaftar as $f) {
            $data_pendaftar[] = $_POST[$f] ?? null;
        }

        if ($pendaftar) {
            // JALUR UPDATE PENDAFTAR
            $setQueryPendaftar = implode("=?, ", $fields_pendaftar) . "=?";
            $sql_pendaftar = "UPDATE pendaftar SET $setQueryPendaftar WHERE id_user=?";
            $data_pendaftar[] = $id_user;
            
            $stmt_p = $pdo->prepare($sql_pendaftar);
            $stmt_p->execute($data_pendaftar);
        } else {
            // JALUR INSERT PENDAFTAR BARU
            $fields_pendaftar[] = 'id_user';
            $data_pendaftar[] = $id_user;

            $cols_p = implode(", ", $fields_pendaftar);
            $params_p = str_repeat("?, ", count($fields_pendaftar) - 1) . "?";
            $sql_pendaftar = "INSERT INTO pendaftar ($cols_p) VALUES ($params_p)";
            
            $stmt_p = $pdo->prepare($sql_pendaftar);
            $stmt_p->execute($data_pendaftar);
            
            // Ambil ID otomatis yang baru saja terbuat untuk dioper ke tabel orang tua
            $id_pendaftar = $pdo->lastInsertId();
        }

        // ----------------------------------------------------
        // PROSES 2: EKSEKUSI DATA TABEL 'orang_tua'
        // ----------------------------------------------------
        $data_ortu = [];
        foreach ($fields_ortu as $f) {
            $data_ortu[] = $_POST[$f] ?? null;
        }

        // Cek dulu apakah baris orang tua sudah ada di database
        $stmt_check_ot = $pdo->prepare("SELECT id_orang_tua FROM orang_tua WHERE id_pendaftar = ?");
        $stmt_check_ot->execute([$id_pendaftar]);
        $ot_exists = $stmt_check_ot->fetch();

        if ($ot_exists) {
            // JALUR UPDATE ORANG TUA
            $setQueryOrtu = implode("=?, ", $fields_ortu) . "=?";
            $sql_ortu = "UPDATE orang_tua SET $setQueryOrtu WHERE id_pendaftar=?";
            $data_ortu[] = $id_pendaftar;
            
            $stmt_o = $pdo->prepare($sql_ortu);
            $stmt_o->execute($data_ortu);
        } else {
            // JALUR INSERT ORANG TUA BARU
            $fields_ortu[] = 'id_pendaftar';
            $data_ortu[] = $id_pendaftar;

            $cols_o = implode(", ", $fields_ortu);
            $params_o = str_repeat("?, ", count($fields_ortu) - 1) . "?";
            $sql_ortu = "INSERT INTO orang_tua ($cols_o) VALUES ($params_o)";
            
            $stmt_o = $pdo->prepare($sql_ortu);
            $stmt_o->execute($data_ortu);
        }

        // Jika kedua tabel sukses tanpa kendala, kunci perubahan ke database secara permanen
        $pdo->commit();
        
        $message = "Data pendaftaran berhasil disimpan!";
        header("Refresh:1; url=formulir.php");

    } catch (PDOException $e) {
        // 🚨 BATALKAN SEMUA PERUBAHAN jika salah satu query gagal agar data tidak timpang/pincang
        $pdo->rollBack();
        
        die("<div style='background:#0f172a; color:#f43f5e; padding:25px; font-family:monospace; border-radius:15px; margin:20px; border:2px solid #f43f5e; line-height:1.6;'>
                <h3 style='margin-top:0; color:#ffffff;'>❌ SQL Transaction Error Terdeteksi!</h3>
                <b>Penyebab Kesalahan:</b> " . $e->getMessage() . "<br><br>
                <i>Solusi Sidang: Pastikan Anda sudah mengeksekusi struktur tabel baru 'pendaftar' dan 'orang_tua' di phpMyAdmin Anda dengan tepat.</i><br><br>
                <a href='formulir.php' style='color:#38bdf8; text-decoration:underline;'>[Kembali ke Form]</a>
            </div>");
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PPDB - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        input, select, textarea { font-size: 0.9rem !important; }
        .active-link { background-color: #f0fdf4 !important; color: #15803d !important; font-weight: 700; }
        /* Style input aesthetic seragam khusus seksi C */
        .form-input-custom {
            width: 100%; padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid #f1f5f9;
            background-color: #f8fafc; outline: none; transition: all 0.2s;
        }
        .form-input-custom:focus { border-color: #15803d; background-color: #ffffff; }
    </style>
</head>
<body class="bg-[#FBFCFA] text-slate-700 min-h-screen">

    <!-- ARSITEKTUR GRID DASHBOARD INDUK -->
    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Memanggil komponen sidebar navigasi siswa -->
        <?php include 'sidebar_siswa.php'; ?>

        <!-- REGION KONTEN UTAMA (SISI KANAN) -->
        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto">
            <div class="max-w-4xl mx-auto">

                <!-- ─── JEPITAN KONDISI 1: TAMPILAN SUKSES JIKA DATA VALID SUDAH MASUK ─── -->
                <?php if ($sudah_isi_form && !isset($_GET['edit'])): ?>
                    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
                        <div class="bg-white p-12 rounded-[3rem] shadow-xl shadow-slate-100 border border-slate-100 w-full">
                            
                            <!-- Icon Interaktif Jam Pasir Animasi -->
                            <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-8 border border-emerald-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-10 h-10 animate-pulse">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                            </div>

                            <h2 class="text-2xl font-black text-slate-800 mb-2">Data Berhasil Terkirim!</h2>
                            <p class="text-xs text-slate-400 font-mono uppercase tracking-wider mb-6">Status: Terdata di Sistem</p>
                            
                            <div class="bg-[#FAFBF9] p-6 rounded-2xl border border-slate-100 mb-8">
                                <p class="text-slate-600 text-xs font-medium leading-relaxed">
                                    "Harap menunggu sampai admin memverifikasi berkas Anda. Kami akan memeriksa kecocokan data dengan dokumen yang telah diunggah."
                                </p>
                            </div>

                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a href="pengumuman.php" class="bg-slate-900 text-white px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-slate-800 transition-all text-center shadow-lg">
                                    📢 Cek Pengumuman Berkala
                                </a>
                                <a href="formulir.php?edit=true" class="bg-white text-emerald-700 border border-emerald-200 px-10 py-4 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-emerald-50 transition-all text-center">
                                    ✏️ Edit Data Saya
                                </a>
                            </div>

                        </div>
                    </div>

                <!-- ─── JEPITAN KONDISI 2: TAMPILKAN FORMULIR (JIKA AKUN BARU / SEDANG MODE EDIT) ─── -->
                <?php else: ?>

                    <header class="mb-10 text-center md:text-left">
                        <h2 class="text-2xl font-black text-green-900 tracking-tight">Formulir Pendaftaran Siswa Baru</h2>
                        <p class="text-xs text-slate-400 font-medium mt-1">Silakan lengkapi seluruh poin pendaftaran dengan benar (A-D)</p>
                    </header>

                    <?php if ($message): ?>
                        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-2xl text-sm font-bold text-center"><?= $message ?></div>
                    <?php endif; ?>
            
                    <form action="" method="POST" class="space-y-8">
                        
                        <!-- =================== SEKSI A (REVISI MATCH GRID TAHUN AJARAN) =================== -->
                        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
                            <div class="flex items-center space-x-3 mb-8 border-b border-slate-50 pb-4">
                                <span class="w-8 h-8 bg-green-700 text-white rounded-full flex items-center justify-center font-bold text-sm">A</span>
                                <h3 class="font-bold text-green-900 uppercase tracking-wider text-xs">Keterangan Calon Peserta Didik</h3>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <!-- 1. Nomor NISN -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">1. Nomor NISN (10 Digit)</label>
                                    <input type="text" name="nisn" maxlength="10" placeholder="Masukkan 10 digit NISN resmi" value="<?= htmlspecialchars($siswa['nisn'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all font-mono font-bold text-slate-800" required>
                                </div>

                                <!-- 2. Tahun Ajaran (Auto-Fill Isi Sendiri Mengunci Data Admin Aktif) -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">2. Tahun Ajaran Pendaftaran</label>
                                    <input type="text" name="tahun_ajaran" value="<?= htmlspecialchars($siswa['tahun_ajaran'] ?? $ta_aktif) ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-100 outline-none font-mono font-black text-green-700 cursor-not-allowed" readonly>
                                </div>

                                <!-- 3. Nama Lengkap -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">3. Nama Lengkap</label>
                                    <input type="text" name="nama_siswa" value="<?= htmlspecialchars($siswa['nama_siswa'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                
                                <!-- 4. Nama Panggilan -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">4. Nama Panggilan</label>
                                    <input type="text" name="nama_panggilan" value="<?= htmlspecialchars($siswa['nama_panggilan'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                
                                <!-- 5. Jenis Kelamin -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">5. Jenis Kelamin</label>
                                    <select name="jenis_kelamin" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <option value="L" <?= ($siswa['jenis_kelamin']??'')=='L'?'selected':'' ?>>Laki-laki</option>
                                        <option value="P" <?= ($siswa['jenis_kelamin']??'')=='P'?'selected':'' ?>>Perempuan</option>
                                    </select>
                                </div>
                                
                                <!-- 6. Tempat / Tgl Lahir -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">6. Tempat / Tgl Lahir</label>
                                    <div class="flex space-x-2">
                                        <input type="text" name="tempat_lahir" placeholder="Tempat" value="<?= htmlspecialchars($siswa['tempat_lahir'] ?? '') ?>" class="w-2/3 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="date" name="tanggal_lahir" value="<?= htmlspecialchars($siswa['tanggal_lahir'] ?? '') ?>" class="w-1/3 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                    </div>
                                </div>
                                
                                <!-- 7. Agama -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">7. Agama</label>
                                    <input type="text" name="agama" value="<?= htmlspecialchars($siswa['agama'] ?? 'Islam') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                
                                <!-- 8. Anak Ke -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">8. Anak Ke</label>
                                    <input type="number" name="anak_ke" value="<?= htmlspecialchars($siswa['anak_ke'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                
                                <!-- 9. Status dalam Keluarga -->
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">9. Status dalam Keluarga</label>
                                    <select name="status_keluarga" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <option value="Kandung" <?= ($siswa['status_keluarga']??'')=='Kandung'?'selected':'' ?>>Anak Kandung</option>
                                        <option value="Tiri" <?= ($siswa['status_keluarga']??'')=='Tiri'?'selected':'' ?>>Anak Tiri</option>
                                        <option value="Angkat" <?= ($siswa['status_keluarga']??'')=='Angkat'?'selected':'' ?>>Anak Angkat</option>
                                    </select>
                                </div>
                                
                                <!-- 10. Jumlah Saudara -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">10. Jumlah Saudara</label>
                                    <div class="grid grid-cols-3 gap-4">
                                        <input type="number" name="jml_saudara_kandung" placeholder="Sdr. Kandung" value="<?= htmlspecialchars($siswa['jml_saudara_kandung'] ?? '0') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="number" name="jml_adik" placeholder="Jumlah Adik" value="<?= htmlspecialchars($siswa['jml_adik'] ?? '0') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="number" name="jml_kakak" placeholder="Jumlah Kakak" value="<?= htmlspecialchars($siswa['jml_kakak'] ?? '0') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                    </div>
                                </div>
                                
                                <!-- 11. Asal Sekolah TK/RA -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">11. Asal Sekolah TK/RA</label>
                                    <input type="text" name="asal_sekolah" value="<?= htmlspecialchars($siswa['asal_sekolah'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                
                                <!-- 12. Alamat Tempat Tinggal -->
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">12. Alamat Tempat Tinggal</label>
                                    <textarea name="alamat" placeholder="Jln. / RT / RW / Ds / Kec / Kota" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all h-24"><?= htmlspecialchars($siswa['alamat'] ?? '') ?></textarea>
                                </div>
                                
                                <input type="hidden" name="rt" value="<?= htmlspecialchars($siswa['rt'] ?? '') ?>">
                                <input type="hidden" name="rw" value="<?= htmlspecialchars($siswa['rw'] ?? '') ?>">
                                <input type="hidden" name="desa" value="<?= htmlspecialchars($siswa['desa'] ?? '') ?>">
                                <input type="hidden" name="kec" value="<?= htmlspecialchars($siswa['kec'] ?? '') ?>">
                                <input type="hidden" name="kota" value="<?= htmlspecialchars($siswa['kota'] ?? '') ?>">
                            </div>
                        </div>

                        <!-- =================== SEKSI B =================== -->
                        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
                            <div class="flex items-center space-x-3 mb-8 border-b border-slate-50 pb-4">
                                <span class="w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold text-sm">B</span>
                                <h3 class="font-bold text-green-900 uppercase tracking-wider text-xs">Keterangan Bakat & Minat</h3>
                            </div>
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">1. Hobi</label>
                                    <input type="text" name="hobi" value="<?= htmlspecialchars($siswa['hobi'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">2. Bidang Study Digemari</label>
                                    <input type="text" name="bidang_studi" value="<?= htmlspecialchars($siswa['bidang_studi'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">3. Olahraga Digemari</label>
                                    <input type="text" name="olahraga" value="<?= htmlspecialchars($siswa['olahraga'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">4. Cita-cita</label>
                                    <input type="text" name="cita_cita" value="<?= htmlspecialchars($siswa['cita_cita'] ?? '') ?>" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- =================== SEKSI C (REVISI STRUKTUR FRAME INPUT) =================== -->
                        <div class="bg-white p-10 rounded-[2.5rem] shadow-sm border border-slate-100 mb-10">
                            <div class="flex items-center space-x-3 mb-10 border-b border-slate-50 pb-5">
                                <span class="w-8 h-8 bg-purple-500 text-white rounded-full flex items-center justify-center font-bold text-xs shadow-lg shadow-purple-100">C</span>
                                <h3 class="font-bold text-green-900 uppercase tracking-widest text-xs italic">Keterangan Orang Tua Kandung</h3>
                            </div>

                            <div class="space-y-8">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">1. Nama Lengkap</div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1">a. Ayah</label>
                                        <input type="text" name="nama_ayah" value="<?= htmlspecialchars($siswa['nama_ayah'] ?? '') ?>" placeholder="Nama Lengkap Ayah" class="form-input-custom">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 mb-1">b. Ibu</label>
                                        <input type="text" name="nama_ibu" value="<?= htmlspecialchars($siswa['nama_ibu'] ?? '') ?>" placeholder="Nama Lengkap Ibu" class="form-input-custom">
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">2. Tempat / Tanggal Lahir</div>
                                    <div class="flex space-x-2">
                                        <input type="text" name="tmpt_lahir_ayah" placeholder="Tempat (Ayah)" value="<?= htmlspecialchars($siswa['tmpt_lahir_ayah'] ?? '') ?>" class="form-input-custom w-1/2">
                                        <input type="date" name="tgl_lahir_ayah" value="<?= htmlspecialchars($siswa['tgl_lahir_ayah'] ?? '') ?>" class="form-input-custom w-1/2">
                                    </div>
                                    <div class="flex space-x-2">
                                        <input type="text" name="tmpt_lahir_ibu" placeholder="Tempat (Ibu)" value="<?= htmlspecialchars($siswa['tmpt_lahir_ibu'] ?? '') ?>" class="form-input-custom w-1/2">
                                        <input type="date" name="tgl_lahir_ibu" value="<?= htmlspecialchars($siswa['tgl_lahir_ibu'] ?? '') ?>" class="form-input-custom w-1/2">
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">3. Pekerjaan</div>
                                    <div><input type="text" name="pekerjaan_ayah" placeholder="a. Ayah" value="<?= htmlspecialchars($siswa['pekerjaan_ayah'] ?? '') ?>" class="form-input-custom"></div>
                                    <div><input type="text" name="pekerjaan_ibu" placeholder="b. Ibu" value="<?= htmlspecialchars($siswa['pekerjaan_ibu'] ?? '') ?>" class="form-input-custom"></div>
                                </div>

                                <div class="space-y-4 pt-2">
                                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">4. Alamat Tempat Tinggal</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <div class="md:col-span-2"><input type="text" name="alamat_ortu" placeholder="Jln." value="<?= htmlspecialchars($siswa['alamat_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                        <div><input type="text" name="rt_ortu" placeholder="RT" value="<?= htmlspecialchars($siswa['rt_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                        <div><input type="text" name="rw_ortu" placeholder="RW" value="<?= htmlspecialchars($siswa['rw_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                        <div><input type="text" name="desa_ortu" placeholder="Ds/Kel." value="<?= htmlspecialchars($siswa['desa_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                        <div><input type="text" name="kec_ortu" placeholder="Kec." value="<?= htmlspecialchars($siswa['kec_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                        <div class="md:col-span-2"><input type="text" name="kota_ortu" placeholder="Kota/Kab." value="<?= htmlspecialchars($siswa['kota_ortu'] ?? '') ?>" class="form-input-custom"></div>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">5. Pendidikan Terakhir</div>
                                    <div><input type="text" name="pendidikan_ayah" placeholder="a. Ayah" value="<?= htmlspecialchars($siswa['pendidikan_ayah'] ?? '') ?>" class="form-input-custom"></div>
                                    <div><input type="text" name="pendidikan_ibu" placeholder="b. Ibu" value="<?= htmlspecialchars($siswa['pendidikan_ibu'] ?? '') ?>" class="form-input-custom"></div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">6. Keterangan Kelangsungan</div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-xs text-slate-400 shrink-0">a. Ayah:</span>
                                        <select name="status_ayah" class="form-input-custom">
                                            <option value="Hidup" <?= ($siswa['status_ayah']??'')=='Hidup'?'selected':'' ?>>Masih Hidup</option>
                                            <option value="Meninggal" <?= ($siswa['status_ayah']??'')=='Meninggal'?'selected':'' ?>>Meninggal Dunia</option>
                                        </select>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-xs text-slate-400 shrink-0">b. Ibu:</span>
                                        <select name="status_ibu" class="form-input-custom">
                                            <option value="Hidup" <?= ($siswa['status_ibu']??'')=='Hidup'?'selected':'' ?>>Masih Hidup</option>
                                            <option value="Meninggal" <?= ($siswa['status_ibu']??'')=='Meninggal'?'selected':'' ?>>Meninggal Dunia</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6 border-b border-slate-100 pb-8">
                                    <div class="md:col-span-2 text-[10px] font-bold text-slate-400 uppercase tracking-widest -mb-4">7. No. Telp / HP Aktif</div>
                                    <div><input type="text" name="no_telp_ayah" placeholder="Ayah" value="<?= htmlspecialchars($siswa['no_telp_ayah'] ?? '') ?>" class="form-input-custom"></div>
                                    <div><input type="text" name="no_telp_ibu" placeholder="Ibu" value="<?= htmlspecialchars($siswa['no_telp_ibu'] ?? '') ?>" class="form-input-custom"></div>
                                </div>
                            </div>
                        </div>

                        <!-- =================== SEKSI D =================== -->
                        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-100 mb-8">
                            <div class="flex items-center space-x-3 mb-10 border-b border-slate-50 pb-5">
                                <span class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center font-bold text-xs italic">D</span>
                                <h3 class="font-bold text-orange-900 uppercase tracking-widest text-[11px]">Keterangan Wali</h3>
                            </div>

                            <div class="grid md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">1. Nama Lengkap Wali</label>
                                    <input type="text" name="nama_wali" value="<?= htmlspecialchars($siswa['nama_wali']??'') ?>" placeholder="Nama Lengkap Wali" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">2. Jenis Kelamin</label>
                                    <select name="jk_wali" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <option value="">-- Pilih --</option>
                                        <option value="L" <?= ($siswa['jk_wali']??'')=='L'?'selected':'' ?>>Laki-laki</option>
                                        <option value="P" <?= ($siswa['jk_wali']??'')=='P'?'selected':'' ?>>Perempuan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">3. Tempat / Tanggal Lahir</label>
                                    <div class="flex space-x-2">
                                        <input type="text" name="tmpt_lahir_wali" placeholder="Tempat" value="<?= htmlspecialchars($siswa['tmpt_lahir_wali']??'') ?>" class="w-1/2 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="date" name="tgl_lahir_wali" value="<?= htmlspecialchars($siswa['tgl_lahir_wali']??'') ?>" class="w-1/2 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">4. Agama</label>
                                    <input type="text" name="agama_wali" value="<?= htmlspecialchars($siswa['agama_wali']??'') ?>" placeholder="Agama" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">5. Pekerjaan</label>
                                    <input type="text" name="pekerjaan_wali" value="<?= htmlspecialchars($siswa['pekerjaan_wali']??'') ?>" placeholder="Pekerjaan Wali" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">6. Alamat Tempat Tinggal Wali</label>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        <input type="text" name="alamat_wali" placeholder="Jln." class="md:col-span-2 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all" value="<?= htmlspecialchars($siswa['alamat_wali']??'') ?>">
                                        <input type="text" name="rt_wali" placeholder="RT" value="<?= htmlspecialchars($siswa['rt_wali']??'') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="text" name="rw_wali" placeholder="RW" value="<?= htmlspecialchars($siswa['rw_wali']??'') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="text" name="desa_wali" placeholder="Ds/Kel." value="<?= htmlspecialchars($siswa['desa_wali']??'') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="text" name="kec_wali" placeholder="Kec." value="<?= htmlspecialchars($siswa['kec_wali']??'') ?>" class="px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <input type="text" name="kota_wali" placeholder="Kota/Kab." class="md:col-span-2 px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all" value="<?= htmlspecialchars($siswa['kota_wali']??'') ?>">
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-2">7. Penghasilan rata-rata perbulan</label>
                                    <select name="penghasilan_wali" class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:border-green-700 focus:bg-white transition-all">
                                        <option value="">-- Pilih Penghasilan --</option>
                                        <option value="Kurang dari 1jt" <?= ($siswa['penghasilan_wali']??'')=='Kurang dari 1jt'?'selected':'' ?>>Kurang dari Rp 1.000.000</option>
                                        <option value="1jt - 2jt" <?= ($siswa['penghasilan_wali']??'')=='1jt - 2jt'?'selected':'' ?>>Antara Rp 1.000.000 - Rp 2.000.000</option>
                                        <option value="Lebih dari 2jt" <?= ($siswa['penghasilan_wali']??'')=='Lebih dari 2jt'?'selected':'' ?>>Lebih dari Rp 2.000.000</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" name="simpan_semua" class="w-full bg-green-700 text-white py-4 rounded-2xl font-bold hover:bg-green-800 shadow-xl shadow-green-100 transition duration-300 uppercase tracking-widest text-sm">
                            Simpan Seluruh Data Pendaftaran
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </main>
    </div>

<script>
// MAGIC SPA AJAX ENGINE: Navigasi cepat tanpa kedipan layar monitor
document.addEventListener('click', function(e) {
    const link = e.target.closest('.nav-link');
    if (!link) return;
    if (link.href.includes('logout.php')) return;

    e.preventDefault();
    const url = link.getAttribute('href');

    fetch(url)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newContent = doc.querySelector('#content-area');
            
            if (newContent) {
                document.querySelector('#content-area').innerHTML = newContent.innerHTML;
                history.pushState(null, '', url);

                document.querySelectorAll('.nav-link').forEach(nav => {
                    nav.classList.remove('active-link', 'bg-green-50', 'text-green-700', 'font-bold');
                    nav.classList.add('text-slate-400');

                    const navHref = nav.getAttribute('href').split('/').pop();
                    const targetUrl = url.split('/').pop();

                    if (navHref === targetUrl) {
                        nav.classList.add('active-link');
                        nav.classList.remove('text-slate-400');
                    }
                });
            } else {
                window.location.href = url; // Jalur aman jika file eksternal berbeda pembungkus
            }
        })
        .catch(err => {
            window.location.href = url;
        });
});

window.addEventListener('popstate', () => location.reload());

// Mengunci background aktif sidebar saat pertama kali load
document.querySelectorAll('.nav-link').forEach(nav => {
    if(nav.getAttribute('href') === window.location.pathname.split('/').pop()) {
        nav.classList.add('active-link');
        nav.classList.remove('text-slate-400');
    }
});
</script>
</body>
</html>