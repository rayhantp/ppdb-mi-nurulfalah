<?php
session_start();
include '../../config/database.php'; 

// 1. Buat daftar role yang diizinkan masuk ke halaman admin ini
$role_diizinkan = ['admin', 'kepala_sekolah', 'operator'];

// 2. Cek apakah session nama ada DAN apakah role user terdaftar di dalam list di atas
if (!isset($_SESSION['nama_lengkap']) || !in_array($_SESSION['role'], $role_diizinkan)) {
    header("Location: ../../login.php"); 
    exit();
}

// Rekam data untuk dipakai di halaman bawah
$current_role = $_SESSION['role'];
$user_name = $_SESSION['nama_lengkap'];


// ====================================================================
// REVISI QUERY STATISTIK: Menghitung semua siswa yang sudah buat akun
// ====================================================================

// 1. Total Pendaftar: Hitung semua pendaftar yang terdaftar di sistem
$totalSiswa = $pdo->query("SELECT COUNT(*) FROM pendaftar")->fetchColumn();

// 2. Belum Verifikasi: Hitung yang statusnya 'proses' atau 'pending'
$totalPending = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_pendaftaran = 'pending' OR status_pendaftaran = 'proses' OR status_pendaftaran IS NULL")->fetchColumn();

// 3. Diterima: Hitung yang sudah pasti lulus
$totalDiterima = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status_pendaftaran = 'diterima'")->fetchColumn();


// ====================================================================
// REVISI QUERY TABEL: Tampilkan semua siswa (baik yang sudah dapat No Reg maupun belum)
// ====================================================================
$query = "SELECT id_pendaftar AS id_siswa, nama_siswa, nisn, no_pendaftaran, status_pendaftaran, tgl_daftar 
          FROM pendaftar 
          ORDER BY id_pendaftar DESC LIMIT 5"; // Urutkan berdasarkan siswa yang paling baru buat akun
$stmt = $pdo->query($query);
$pendaftar = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }

        /* Style Navigasi Aktif Sidebar (Utuh) */
        .active-link { 
            background-color: #f0fdf4 !important; 
            color: #15803d !important; 
            border-right: 4px solid #15803d; 
            font-weight: 700;
        }

        /* Animasi Transisi Halaman Khas SPA */
        #content-area {
            animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Animasi Latar Belakang Grid Berjalan */
        @keyframes gridMove {
            0% { background-position: 0 0; }
            100% { background-position: 4rem 4rem; }
        }
        .moving-grid-bg {
            animation: gridMove 30s linear infinite;
        }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-bl from-green-200/20 to-emerald-100/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 left-1/4 w-[400px] h-[400px] bg-blue-100/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_40%,#000_70%,transparent_100%)] opacity-[0.25] pointer-events-none -z-10 moving-grid-bg"></div>

    <div class="flex flex-col md:flex-row min-h-screen">

        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-14">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-800 tracking-tight">
                        Selamat Datang, <span class="bg-gradient-to-r from-green-700 to-emerald-600 bg-clip-text text-transparent"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin'); ?></span>
                    </h2>
                    <p class="text-sm text-slate-400 font-medium mt-1">Berikut adalah ringkasan sistem pendaftaran digital MI Nurul Falah hari ini.</p>
                </div>
                
                <div class="flex items-center space-x-4 bg-white/80 backdrop-blur-md p-2 pr-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-white transition-all hover:scale-[1.02] duration-300">
                    <div class="w-10 h-10 bg-gradient-to-br from-green-700 to-emerald-600 rounded-xl flex items-center justify-center text-white text-xs font-black uppercase shadow-md shadow-green-700/20">
                        <?= substr(htmlspecialchars($_SESSION['nama_lengkap'] ?? 'AD'), 0, 2); ?>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xs font-black text-slate-800 uppercase tracking-wider leading-none">Administrator</span>
                        <div class="flex items-center gap-1.5 mt-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wider font-mono">Sistem Live</span>
                        </div>
                    </div>
                </div>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-14">
                
                <div class="bg-white/90 backdrop-blur-md p-8 rounded-[3rem] rounded-tr-xl rounded-bl-xl border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] relative overflow-hidden group transition-all duration-500 hover:translate-y-[-6px] hover:shadow-[0_30px_60px_-15px_rgba(59,130,246,0.12)] hover:border-blue-200/60">
                    <div class="absolute top-0 bottom-0 left-0 w-[4px] bg-gradient-to-b from-blue-500 to-indigo-500"></div>
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-blue-50/50 rounded-full transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>
                    
                    <span class="w-12 h-12 bg-blue-50 text-blue-600 border border-blue-100 rounded-2xl flex items-center justify-center mb-6 relative z-10 text-xl shadow-inner transition-transform duration-500 group-hover:rotate-[12deg]">👥</span>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 relative z-10 font-mono">Total Pendaftar</p>
                    <p class="text-5xl font-black text-slate-800 mt-2 relative z-10 tracking-tighter"><?= $totalSiswa; ?></p>
                </div>

                <div class="bg-white/90 backdrop-blur-md p-8 rounded-[3rem] rounded-tr-xl rounded-bl-xl border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] relative overflow-hidden group transition-all duration-500 hover:translate-y-[-6px] hover:shadow-[0_30px_60px_-15px_rgba(245,158,11,0.12)] hover:border-amber-200/60">
                    <div class="absolute top-0 bottom-0 left-0 w-[4px] bg-gradient-to-b from-amber-500 to-orange-500"></div>
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-amber-50/50 rounded-full transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>
                    
                    <span class="w-12 h-12 bg-amber-50 text-amber-600 border border-amber-100 rounded-2xl flex items-center justify-center mb-6 relative z-10 text-xl shadow-inner transition-transform duration-500 group-hover:rotate-[-12deg]">⏳</span>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 relative z-10 font-mono">Belum Verifikasi</p>
                    <p class="text-5xl font-black text-slate-800 mt-2 relative z-10 tracking-tighter"><?= $totalPending; ?></p>
                </div>

                <div class="bg-white/90 backdrop-blur-md p-8 rounded-[3rem] rounded-tr-xl rounded-bl-xl border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] relative overflow-hidden group transition-all duration-500 hover:translate-y-[-6px] hover:shadow-[0_30px_60px_-15px_rgba(16,185,129,0.12)] hover:border-green-200/60">
                    <div class="absolute top-0 bottom-0 left-0 w-[4px] bg-gradient-to-b from-emerald-500 to-green-600"></div>
                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-green-50/50 rounded-full transition-transform duration-700 group-hover:scale-150 pointer-events-none"></div>
                    
                    <span class="w-12 h-12 bg-green-50 text-green-600 border border-green-100 rounded-2xl flex items-center justify-center mb-6 relative z-10 text-xl shadow-inner transition-transform duration-500 group-hover:scale-110">🎓</span>
                    <p class="text-xs font-black uppercase tracking-widest text-slate-400 relative z-10 font-mono">Diterima / Lulus</p>
                    <p class="text-5xl font-black text-slate-800 mt-2 relative z-10 tracking-tighter"><?= $totalDiterima; ?></p>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 shadow-[0_30px_60px_-20px_rgba(0,0,0,0.02)] overflow-hidden transition-all duration-300 hover:shadow-xl hover:shadow-slate-200/30">
                
                <div class="px-10 py-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-white via-slate-50/20 to-white">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight">Pendaftar Terbaru</h3>
                        <p class="text-xs text-slate-400 font-medium mt-1">5 berkas masuk terakhir yang memerlukan tindakan peninjauan.</p>
                    </div>
                    <a href="pendaftar.php" class="nav-link text-xs font-black uppercase tracking-widest text-green-700 bg-green-50 border border-green-100/50 px-6 py-3 rounded-xl hover:bg-green-700 hover:text-white transition-all duration-300 shadow-sm shadow-green-700/5">Lihat Semua Data</a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] uppercase tracking-[0.25em] text-slate-400 border-b border-slate-100 bg-slate-50/40 font-mono">
                                <th class="px-10 py-5">Calon Siswa</th>
                                <th class="px-10 py-5">Nomor Registrasi</th>
                                <th class="px-10 py-5">Status Sistem</th>
                                <th class="px-10 py-5 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white/50">
                            <?php if (count($pendaftar) > 0): ?>
                                <?php foreach ($pendaftar as $row): ?>
                                <tr class="hover:bg-green-50/20 transition-all duration-300 group">
                                    
                                    <td class="px-10 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-9 h-9 bg-gradient-to-br from-slate-100 to-slate-50 border border-slate-200 text-slate-600 rounded-xl flex items-center justify-center font-black text-xs uppercase shadow-inner group-hover:from-green-600 group-hover:to-emerald-600 group-hover:text-white group-hover:border-transparent transition-all duration-300">
                                                <?= substr(htmlspecialchars($row['nama_siswa']), 0, 1); ?>
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="font-black text-slate-800 group-hover:text-green-700 transition-colors duration-200"><?= htmlspecialchars($row['nama_siswa']); ?></span>
                                                <span class="text-xs text-slate-400 font-bold font-mono mt-0.5">NISN: <?= htmlspecialchars($row['nisn'] ?? '-'); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <td class="px-10 py-6 text-sm font-bold text-slate-500 font-mono tracking-wider">
                                        <?= htmlspecialchars($row['no_pendaftaran']); ?>
                                    </td>
                                    
                                    <td class="px-10 py-6">
                                        <?php 
                                        $statusClass = 'bg-slate-100 text-slate-600';
                                        if ($row['status_pendaftaran'] == 'pending' || $row['status_pendaftaran'] == 'proses') {
                                            $statusClass = 'bg-amber-50 text-amber-600 border border-amber-100/60';
                                        } elseif ($row['status_pendaftaran'] == 'diterima') {
                                            $statusClass = 'bg-emerald-50 text-emerald-600 border border-emerald-100/60';
                                        } elseif ($row['status_pendaftaran'] == 'ditolak') {
                                            $statusClass = 'bg-red-50 text-red-600 border border-red-100/60';
                                        }
                                        ?>
                                        <span class="px-4 py-1.5 rounded-full text-[9px] font-black uppercase tracking-widest font-mono <?= $statusClass; ?>">
                                            <?= htmlspecialchars($row['status_pendaftaran']); ?>
                                        </span>
                                    </td>
                                    
                                    <td class="px-10 py-6 text-right">
                                        <a href="detail_siswa.php?id=<?= $row['id_siswa']; ?>" class="inline-flex items-center text-xs font-black uppercase tracking-widest text-green-700 hover:text-white group/btn bg-green-50 border border-green-100/50 hover:bg-green-700 px-4 py-2.5 rounded-xl transition-all duration-300">
                                            <span>Validasi Berkas</span>
                                            <span class="ml-1.5 transform transition-transform group-hover/btn:translate-x-1 font-mono">→</span>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="px-10 py-20 text-center text-slate-400 font-bold text-sm tracking-wide">
                                        📭 Belum ada data pendaftar baru yang masuk ke sistem.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

<script>
document.addEventListener('click', function(e) {
    const link = e.target.closest('.nav-link');
    if (!link) return;

    if (link.href.includes('logout.php') || link.getAttribute('target')) return;

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
                    nav.classList.remove('active-link');
                    nav.classList.add('text-slate-400');
                    if(nav.getAttribute('href') === url.split('/').pop()) {
                        nav.classList.add('active-link');
                        nav.classList.remove('text-slate-400');
                    }
                });
            }
        })
        .catch(err => window.location.href = url);
});

window.addEventListener('popstate', () => location.reload());
</script>

</body>
</html>