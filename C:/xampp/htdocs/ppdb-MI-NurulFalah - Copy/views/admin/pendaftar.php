<?php
session_start();
include '../../config/database.php'; 

// 🔒 SATPAM UTAMA: Hanya admin dan operator yang boleh melihat database pendaftar
$role_diizinkan = ['admin', 'operator'];
if (!isset($_SESSION['nama_lengkap']) || !in_array($_SESSION['role'], $role_diizinkan)) {
    session_destroy();
    header("Location: ../../login.php"); 
    exit();
}

$current_role = $_SESSION['role'];
$user_name = $_SESSION['nama_lengkap'];

try {
    // Tarik semua data siswa untuk tabel pendaftar utama
    $stmt_list = $pdo->query("SELECT *, id_pendaftar AS id_siswa FROM pendaftar ORDER BY id_pendaftar DESC");
    $daftar_siswa = $stmt_list->fetchAll(PDO::FETCH_ASSOC);
    $total_records = count($daftar_siswa);
} catch (PDOException $e) {
    die("Gangguan Query Database: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Pendaftar - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }
        .active-link { background-color: #f0fdf4 !important; color: #15803d !important; border-right: 4px solid #15803d; font-weight: 700; }
        #content-area { animation: slideUp 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-3xl font-black text-slate-800 tracking-tight">Database Pendaftar</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1">Total berkas masuk terdata di dalam sistem utama penyeleksian.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="export_csv.php" class="w-full sm:w-auto flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl text-xs font-bold uppercase tracking-wider transition-all shadow-md">
                            <span>📥 Export CSV</span>
                        </a>
                    </div>
                </header>

                <div class="bg-white p-4 rounded-3xl border border-slate-100 shadow-[0_15px_30px_-10px_rgba(0,0,0,0.01)] flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-full max-w-xl flex-1">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">🔍</span>
                        <input type="text" id="pendaftarSearch" placeholder="Cari nama, NISN, atau nomor registrasi siswa..." class="w-full pl-12 pr-28 py-3.5 bg-slate-50/80 border border-slate-200/60 rounded-2xl text-sm focus:outline-none focus:border-green-600 transition-all font-semibold text-slate-800">
                        <button type="button" id="btnCariPendaftar" class="absolute right-2 top-2 bottom-2 bg-slate-900 hover:bg-slate-800 text-white text-[10px] font-black uppercase tracking-widest px-5 rounded-xl transition-all">
                            Cari
                        </button>
                    </div>
                    
                    <div class="w-full sm:w-auto flex items-center justify-center gap-2 text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-50 border border-slate-100 px-5 py-3.5 rounded-2xl font-mono ml-auto">
                        <span>Total Records:</span>
                        <span class="text-green-700 font-bold" id="countBadge"><?= $total_records; ?> Siswa</span>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[9px] uppercase tracking-[0.25em] text-slate-400 border-b border-slate-100 bg-slate-50/40 font-mono">
                                    <th class="px-8 py-4">Calon Siswa</th>
                                    <th class="px-8 py-4">Nomor Registrasi</th>
                                    <th class="px-8 py-4">Status Verifikasi</th>
                                    <th class="px-8 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelPendaftarBody" class="divide-y divide-slate-100 text-xs">
                                <?php if ($total_records > 0): ?>
                                    <?php foreach ($daftar_siswa as $row): ?>
                                    <tr class="pendaftar-row hover:bg-green-50/10 transition-all duration-200">
                                        
                                        <td class="px-8 py-4 flex items-center gap-3">
                                            <div class="w-8 h-8 bg-slate-100 text-slate-600 rounded-xl flex items-center justify-center font-bold uppercase text-[11px]">
                                                <?= substr(htmlspecialchars($row['nama_siswa']), 0, 1); ?>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-black text-slate-800 class-nama-siswa uppercase"><?= htmlspecialchars($row['nama_siswa']) ?></span>
                                                <span class="text-[10px] text-slate-400 font-bold font-mono mt-0.5">NISN: <span class="class-nisn-siswa"><?= htmlspecialchars($row['nisn'] ?? '-') ?></span></span>
                                            </div>
                                        </td>
                                        
                                        <td class="px-8 py-4 font-bold font-mono tracking-wider text-slate-600 class-noreg-siswa">
                                            <?= htmlspecialchars($row['no_pendaftaran'] ?? 'Belum Generate') ?>
                                        </td>
                                        
                                        <td class="px-8 py-4">
                                            <?php 
                                            $status = $row['status_pendaftaran'] ?? 'proses';
                                            $badge = "bg-amber-50 text-amber-600 border-amber-100";
                                            if ($status === 'diterima') $badge = "bg-emerald-50 text-emerald-600 border-emerald-100";
                                            elseif ($status === 'ditolak') $badge = "bg-red-50 text-red-600 border-red-100";
                                            ?>
                                            <span class="px-2.5 py-1 border text-[9px] font-black uppercase font-mono tracking-wider rounded-lg <?= $badge ?>">
                                                <?= $status === 'proses' ? 'Proses' : $status ?>
                                            </span>
                                        </td>

                                        <td class="px-8 py-4 text-right">
                                            <a href="detail_siswa.php?id=<?= $row['id_siswa'] ?>" class="inline-block text-[9px] font-black uppercase tracking-widest text-green-700 bg-green-50 border border-green-100 hover:bg-green-700 hover:text-white px-4 py-2 rounded-xl transition-all">
                                                Periksa Berkas →
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="px-8 py-16 text-center text-slate-400 font-bold italic">
                                            📦 Belum ada data pendaftar yang masuk ke database.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

<script>
// Fungsi utama untuk menyaring baris tabel secara real-time
function eksekusiLiveSearch() {
    const kataKunci = document.getElementById('pendaftarSearch').value.toLowerCase();
    const seluruhBaris = document.querySelectorAll('.pendaftar-row');
    let jumlahDitemukan = 0;
    
    seluruhBaris.forEach(row => {
        // Ambil data teks target pencarian dari kolom tabel
        const nama = row.querySelector('.class-nama-siswa').textContent.toLowerCase();
        const nisn = row.querySelector('.class-nisn-siswa').textContent.toLowerCase();
        const noreg = row.querySelector('.class-noreg-siswa').textContent.toLowerCase();
        
        // Cocokkan apakah kata kunci ada di dalam nama, nisn, atau nomor registrasi
        if (nama.includes(kataKunci) || nisn.includes(kataKunci) || noreg.includes(kataKunci)) {
            row.style.display = ''; // Tampilkan jika cocok
            jumlahDitemukan++;
        } else {
            row.style.display = 'none'; // Sembunyikan jika tidak cocok
        }
    });

    // Perbarui jumlah angka counter records di badge atas secara dinamis
    const badgeCount = document.getElementById('countBadge');
    if (badgeCount) {
        badgeCount.textContent = jumlahDitemukan + " Siswa";
    }
}

// 🚀 SISTEM PENGIKAT EVENT (ANTI-LOGOUT / ANTI-LOST FOCUS)
// Membungkus fungsi ke dalam satu inisialisasi agar stabil saat di-load dari menu mana saja
function initSearchEngine() {
    const inputSearch = document.getElementById('pendaftarSearch');
    const btnCari = document.getElementById('btnCariPendaftar');

    if (inputSearch) {
        // Jalankan filter instan SETIAP KALI user mengetik huruf baru (Tanpa nunggu klik cari)
        inputSearch.removeEventListener('input', eksekusiLiveSearch);
        inputSearch.addEventListener('input', eksekusiLiveSearch);
        
        // Antisipasi jika user menekan tombol Enter di keyboard agar tidak reload
        inputSearch.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                eksekusiLiveSearch();
            }
        });
    }

    if (btnCari) {
        // Jalankan filter jika tombol "Cari" diklik mouse
        btnCari.removeEventListener('click', eksekusiLiveSearch);
        btnCari.addEventListener('click', eksekusiLiveSearch);
    }
}

// Jalankan mesin pencari saat dokumen pertama kali dimuat gres
document.addEventListener('DOMContentLoaded', initSearchEngine);
// Jalankan ulang jika halaman dimuat via rute trigger SPA AJAX agar listener tidak hilang
initSearchEngine();

// Menjaga indikator active-link menu di sidebar admin tetap presisi
document.querySelectorAll('.nav-link').forEach(nav => {
    if(nav.getAttribute('href') === window.location.pathname.split('/').pop()) {
        nav.classList.add('active-link');
    }
});
</script>
</body>
</html>