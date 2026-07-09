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
$status_pendaftaran = 'proses'; 
$sudah_daftar = false; // 🔥 Indikator awal penentu status pengajuan

try {
    // Tarik data pendaftaran real-time berdasarkan id_user
    $stmt = $pdo->prepare("SELECT status_pendaftaran, no_pendaftaran FROM pendaftar WHERE id_user = ? LIMIT 1");
    $stmt->execute([$id_user]);
    $siswa = $stmt->fetch();
    
    // 🔥 PENGUNCI LOGIKA: Dianggap sudah mengajukan jika no_pendaftaran sudah digenerate secara valid
    if ($siswa && !empty($siswa['no_pendaftaran']) && $siswa['no_pendaftaran'] !== 'Belum Generate') {
        $status_pendaftaran = $siswa['status_pendaftaran'] ?? 'proses';
        $sudah_daftar = true;
    }
} catch (PDOException $e) {
    // Jalankan mode fallback jika terjadi gangguan
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman Hasil Seleksi PPDB - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>body { font-family: 'Quicksand', sans-serif; }</style>
</head>
<body class="bg-[#FBFCFA] text-slate-700 min-h-screen">

    <div class="flex flex-col md:flex-row min-h-screen">

        <!-- Memanggil komponen sidebar_siswa -->
        <?php include 'sidebar_siswa.php'; ?>

        <!-- WAJIB SPA CONTAINER: Harus ada id="content-area" -->
        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto">
            <div class="max-w-3xl mx-auto">
                
                <header class="mb-10 text-center md:text-left">
                    <h2 class="text-2xl font-black text-green-900 tracking-tight">Pengumuman Hasil Seleksi</h2>
                    <p class="text-xs text-slate-400 font-medium mt-1">Halaman resmi pengumuman kelulusan berkas pendaftaran online madrasah.</p>
                </header>

                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 text-center space-y-6">
                    
                    <!-- ─── KONDISI 1: JIKA SISWA BELUM MENGISI FORMULIR SAMA SEKALI ─── -->
                    <?php if (!$sudah_daftar): ?>
                        <div class="w-20 h-20 bg-slate-50 text-slate-400 rounded-3xl flex items-center justify-center mx-auto border border-slate-100">
                            <span class="text-3xl">📭</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-black text-slate-800">Belum Ada Riwayat Pendaftaran</h3>
                            <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                Halo <b><?= htmlspecialchars($nama_siswa) ?></b>, sistem mencatat Anda belum melengkapi berkas biodata pada menu Formulir Pendaftaran digital.
                            </p>
                        </div>
                        <div class="pt-4">
                            <a href="formulir.php" class="inline-block bg-green-700 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-green-800 transition-all shadow-md active:scale-95">
                                ✍️ Isi Formulir Pendaftaran
                            </a>
                        </div>

                    <!-- ─── KONDISI 2: JIKA SUDAH DAFTAR & DINYATAKAN LULUS ─── -->
                    <?php elseif ($status_pendaftaran === 'diterima'): ?>
                        <div class="w-20 h-20 bg-emerald-50 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto border border-emerald-100 shadow-sm">
                            <span class="text-3xl">🎉</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-black text-slate-800">Selamat, Anda Dinyatakan LULUS!</h3>
                            <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                                Selamat kepada <b><?= htmlspecialchars($nama_siswa) ?></b>, berkas pendaftaran Anda telah diverifikasi dan dinyatakan berhak menjadi bagian dari keluarga besar MI Nurul Falah.
                            </p>
                        </div>
                        <div class="pt-4">
                            <a href="cetak_bukti.php" target="_blank" class="inline-block bg-slate-950 text-white px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-slate-800 transition-all shadow-md">
                                🖨️ Cetak Bukti Pendaftaran Ulang
                            </a>
                        </div>

                    <!-- ─── KONDISI 3: JIKA SUDAH DAFTAR & DINYATAKAN DITOLAK ─── -->
                    <?php elseif ($status_pendaftaran === 'ditolak'): ?>
                        <div class="w-20 h-20 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mx-auto border border-red-100 shadow-sm">
                            <span class="text-3xl">❌</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-black text-slate-800">Mohon Maaf, Berkas Belum Lolos</h3>
                            <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                                Dokumen pendaftaran yang Anda unggah tidak sesuai dengan kriteria sekolah atau tidak terbaca dengan jelas oleh tim panitia verifikator.
                            </p>
                        </div>
                        <div class="pt-4">
                            <a href="formulir.php?edit=true" class="inline-block bg-white text-red-600 border border-red-200 px-8 py-3.5 rounded-xl font-bold text-xs uppercase tracking-widest hover:bg-red-50 transition-all">
                                ✏️ Perbaiki Data / Berkas
                            </a>
                        </div>

                    <!-- ─── KONDISI 4: JIKA DATA ADA & STATUSNYA MASIH 'PROSES' ─── -->
                    <?php else: ?>
                        <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-3xl flex items-center justify-center mx-auto border border-amber-100 shadow-sm">
                            <span class="text-3xl animate-spin inline-block">⏳</span>
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-black text-slate-800">Berkas Sedang Diperiksa</h3>
                            <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                                Halo <b><?= htmlspecialchars($nama_siswa) ?></b>, saat ini berkas administrasi digital Anda sedang berada dalam antrean peninjauan oleh tim Operator sekolah.
                            </p>
                        </div>
                        <div class="bg-amber-50/50 p-4 rounded-2xl border border-amber-100 max-w-md mx-auto text-[11px] text-amber-700 font-medium">
                            🔔 Halaman ini akan berubah otomatis menjadi pengumuman kelulusan jika status Anda telah divalidasi oleh pihak sekolah.
                        </div>
                    <?php endif; ?>

                </div>

            </div>
        </main>
    </div>

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