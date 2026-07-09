<?php
session_start();
include '../../config/database.php'; 

// 🔐 SATPAM UTAMA: Hanya 3 role internal ini yang boleh melihat data kelulusan
$role_diizinkan = ['admin', 'operator', 'kepala_sekolah'];
if (!isset($_SESSION['nama_lengkap']) || !in_array($_SESSION['role'], $role_diizinkan)) {
    session_destroy();
    header("Location: ../../login.php"); 
    exit();
}

$current_role = $_SESSION['role'];
$user_name = $_SESSION['nama_lengkap'];

try {
    // 📊 ENGINE STATISTIK DATA (Menghitung data real-time khusus yang DITERIMA)
    $stmt_total = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_pendaftaran = 'diterima'");
    $total_diterima = $stmt_total->fetchColumn();

    $stmt_lk = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_pendaftaran = 'diterima' AND jenis_kelamin = 'L'");
    $total_lk = $stmt_lk->fetchColumn();

    $stmt_pr = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_pendaftaran = 'diterima' AND jenis_kelamin = 'P'");
    $total_pr = $stmt_pr->fetchColumn();

    // 👥 GET DATA UTAMA: Ambil semua siswa yang berstatus 'diterima'
    $stmt_list = $pdo->query("SELECT *, id_pendaftar AS id_siswa FROM pendaftar WHERE status_pendaftaran = 'diterima' ORDER BY id_pendaftar DESC");
    $siswa_diterima = $stmt_list->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Gangguan Query Basis Data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa Dinyatakan Diterima - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }
        .active-link { background-color: #f0fdf4 !important; color: #15803d !important; border-right: 4px solid #15803d; font-weight: 700; }
        #content-area { animation: slideUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1); }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <div class="absolute top-0 right-0 w-[700px] h-[700px] bg-gradient-to-bl from-emerald-200/15 to-green-100/5 rounded-full blur-[150px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-blue-100/10 rounded-full blur-[130px] pointer-events-none -z-10"></div>

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <div class="flex items-center space-x-2.5">
                            <span class="px-3 py-1 bg-emerald-50 border border-emerald-100 text-emerald-700 text-[10px] font-black uppercase font-mono tracking-widest rounded-lg">Official Pass</span>
                            <span class="text-xs text-slate-400 font-bold font-mono">TA 2026/2027</span>
                        </div>
                        <h2 class="text-3xl lg:text-4xl font-black text-slate-800 tracking-tight mt-2">Siswa Diterima</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1">Daftar menyeluruh calon peserta didik baru yang lolos seleksi berkas administrasi.</p>
                    </div>
                    
                    <a href="cetak_rekap_diterima.php" target="_blank" class="w-full sm:w-auto flex items-center justify-center space-x-2.5 bg-slate-900 text-white px-6 py-4 rounded-2xl text-xs font-black uppercase tracking-widest hover:bg-slate-800 transition-all duration-300 shadow-xl shadow-slate-900/10 active:scale-95">
                        <span>🖨️ Cetak Rekap Kelulusan</span>
                    </a>
                </header>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-green-800 to-emerald-700 p-6 rounded-[2.5rem] text-white shadow-xl shadow-green-800/10 relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-8xl opacity-10 transform rotate-12 transition-transform group-hover:scale-110 duration-500">🎓</div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-green-200/80 font-mono">Total Lolos Seleksi</span>
                        <h3 class="text-4xl font-black tracking-tight mt-2 font-mono"><?= $total_diterima; ?> <span class="text-sm font-medium tracking-normal font-sans text-green-100">Siswa</span></h3>
                    </div>
                    <div class="bg-white border border-slate-100 p-6 rounded-[2.5rem] shadow-[0_15px_30px_-10px_rgba(0,0,0,0.01)] relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 transform rotate-6">👦</div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono">Siswa Laki-Laki (L)</span>
                        <h3 class="text-3xl font-black tracking-tight text-slate-800 mt-2 font-mono"><?= $total_lk; ?> <span class="text-xs font-bold tracking-normal font-sans text-slate-400">Peserta</span></h3>
                    </div>
                    <div class="bg-white border border-slate-100 p-6 rounded-[2.5rem] shadow-[0_15px_30px_-10px_rgba(0,0,0,0.01)] relative overflow-hidden group">
                        <div class="absolute -right-4 -bottom-4 text-7xl opacity-5 transform rotate-6">👧</div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 font-mono">Siswa Perempuan (P)</span>
                        <h3 class="text-3xl font-black tracking-tight text-slate-800 mt-2 font-mono"><?= $total_pr; ?> <span class="text-xs font-bold tracking-normal font-sans text-slate-400">Peserta</span></h3>
                    </div>
                </div>

                <div class="bg-white/80 backdrop-blur-md p-4 rounded-3xl border border-slate-100 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.01)] flex flex-col sm:flex-row items-center gap-4">
                    <div class="relative w-full max-w-xl flex-1">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">🔍</span>
                        <input type="text" id="diterimaSearch" placeholder="Cari nama atau nomor registrasi siswa yang lulus..." class="w-full pl-12 pr-28 py-3.5 bg-slate-50/80 border border-slate-200/60 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/10 focus:border-green-600 transition-all font-semibold text-slate-800 placeholder-slate-400">
                        <button type="button" id="btnDiterimaSearch" class="absolute right-2 top-2 bottom-2 bg-green-700 hover:bg-green-800 text-white text-[10px] font-black uppercase tracking-widest px-5 rounded-xl transition-all duration-200 shadow-sm shadow-green-700/10 active:scale-95">
                            Saring
                        </button>
                    </div>
                    
                    <div class="w-full sm:w-auto flex items-center justify-center gap-2 text-[10px] text-slate-400 font-black uppercase tracking-widest bg-slate-50 border border-slate-100 px-5 py-3.5 rounded-2xl font-mono ml-auto">
                        <span>Lulus Terverifikasi:</span>
                        <span class="text-emerald-700 bg-emerald-50 border border-emerald-100/50 px-2.5 py-1 rounded-lg font-black"><?= $total_diterima; ?> Akun</span>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 shadow-[0_30px_60px_-20px_rgba(0,0,0,0.01)] overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-[10px] uppercase tracking-[0.25em] text-slate-400 border-b border-slate-100 bg-slate-50/40 font-mono">
                                    <th class="px-10 py-5">Identitas Calon Siswa</th>
                                    <th class="px-10 py-5">Nomor Registrasi</th>
                                    <th class="px-10 py-5">Gender</th>
                                    <th class="px-10 py-5">Asal Sekolah TK/RA</th>
                                    <th class="px-10 py-5 text-right">Lembar Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody id="tabelDiterimaBody" class="divide-y divide-slate-100 bg-white/30">
                                <?php if (count($siswa_diterima) > 0): ?>
                                    <?php foreach ($siswa_diterima as $row): ?>
                                    <tr class="diterima-row hover:bg-green-50/10 transition-all duration-300 group">
                                        
                                        <td class="px-10 py-5">
                                            <div class="flex items-center gap-4">
                                                <div class="w-9 h-9 bg-gradient-to-br from-green-50 to-emerald-50 border border-green-100 text-green-700 rounded-xl flex items-center justify-center font-black text-xs uppercase shadow-sm group-hover:from-green-700 group-hover:to-emerald-600 group-hover:text-white group-hover:border-transparent transition-all duration-300">
                                                    <?= substr(htmlspecialchars($row['nama_siswa']), 0, 1); ?>
                                                </div>
                                                <div class="flex flex-col">
                                                    <span class="font-black text-slate-800 class-nama group-hover:text-green-700 transition-colors duration-200"><?= htmlspecialchars($row['nama_siswa']) ?></span>
                                                    <span class="text-xs text-slate-400 font-bold font-mono mt-0.5">NISN: <?= htmlspecialchars($row['nisn'] ?? '-') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td class="px-10 py-5 text-sm font-bold text-slate-600 font-mono tracking-wider class-noreg">
                                            <?= htmlspecialchars($row['no_pendaftaran'] ?? 'UNREG-ERROR') ?>
                                        </td>
                                        
                                        <td class="px-10 py-5">
                                            <span class="px-3 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg font-mono <?= $row['jenis_kelamin'] === 'L' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-pink-50 text-pink-600 border border-pink-100' ?>">
                                                <?= $row['jenis_kelamin'] === 'L' ? 'Laki-Laki' : 'Perempuan' ?>
                                            </span>
                                        </td>

                                        <td class="px-10 py-5 text-xs text-slate-500 font-semibold truncate max-w-[180px]">
                                            <?= htmlspecialchars($row['asal_sekolah'] ?? '-') ?>
                                        </td>
                                        
                                        <td class="px-10 py-5 text-right">
                                            <a href="detail_siswa.php?id=<?= $row['id_siswa'] ?>" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-emerald-700 hover:text-white bg-emerald-50 hover:bg-emerald-700 px-4 py-2.5 rounded-xl transition-all duration-300 border border-emerald-100/50 shadow-sm">
                                                <span>Buka Berkas</span>
                                                <span class="ml-1.5 font-mono">→</span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="px-10 py-24 text-center text-slate-400 font-bold text-sm tracking-wide">
                                            📦 Belum ada calon siswa yang berstatus 'Diterima' di dalam sistem.
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
function filterDataDiterima() {
    const keyword = document.getElementById('diterimaSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.diterima-row');
    
    rows.forEach(row => {
        const nama = row.querySelector('.class-nama').textContent.toLowerCase();
        const noreg = row.querySelector('.class-noreg').textContent.toLowerCase();
        
        if (nama.includes(keyword) || noreg.includes(keyword)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

// Menghubungkan trigger input, klik button saring, dan penekanan enter keyboard
document.getElementById('diterimaSearch')?.addEventListener('input', filterDataDiterima);
document.getElementById('btnDiterimaSearch')?.addEventListener('click', filterDataDiterima);
document.getElementById('diterimaSearch')?.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        filterDataDiterima();
    }
});

// Penjaga tanda active-link pada sidebar panel utama
document.querySelectorAll('.nav-link').forEach(nav => {
    if(nav.getAttribute('href') === window.location.pathname.split('/').pop()) {
        nav.classList.add('active-link');
    }
});
</script>
</body>
</html>