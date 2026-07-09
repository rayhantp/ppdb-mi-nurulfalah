<?php
// Ambil role user yang sedang aktif dari session
$current_role = $_SESSION['role'] ?? 'operator'; 
$user_name = $_SESSION['nama_lengkap'] ?? 'User';
?>
<!-- 🔥 PERBAIKAN UTAMA: Ditambahkan md:h-screen md:sticky md:top-0 agar tinggi sidebar presisi se-layar monitor -->
<aside class="w-full md:w-64 bg-white border-r border-slate-100 flex flex-col shrink-0 relative z-30 md:h-screen md:sticky md:top-0">
    
    <!-- Bagian Atas: Logo Sekolah -->
    <div class="p-6 border-b border-slate-50 flex items-center space-x-3">
        <img src="../../assets/img/logo.png" alt="Logo" class="w-10 h-10 object-contain">
        <div class="flex flex-col">
            <span class="text-sm font-black text-slate-800 tracking-tight leading-none">MI NURUL FALAH</span>
            <span class="text-[9px] font-bold text-emerald-600 tracking-widest uppercase mt-1 font-mono">
                <?= str_replace('_', ' ', strtoupper($current_role)); ?> PANEL
            </span>
        </div>
    </div>

    <!-- Bagian Tengah: Daftar Menu Navigasi -->
    <nav class="flex-1 p-6 space-y-2 overflow-y-auto">
        <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-4 font-mono">Menu Utama</span>
        
        <a href="dashboard.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
            <span>📊</span> <span>Dashboard</span>
        </a>

        <a href="pendaftar.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
            <span>👥</span> <span>Data Pendaftar</span>
        </a>

        <a href="siswa_diterima.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
            <span>🎓</span> <span>Siswa Diterima</span>
        </a>

        <a href="analisis_tren.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
            <span>📈</span> <span>Analisis Tren</span>
        </a>

        <a href="analisis_tren_mingguan.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
            <span>📊</span> <span>Analisis Mingguan</span>
        </a>

        <?php if ($current_role === 'admin'): ?>
            <div class="pt-4 mt-4 border-t border-slate-50 space-y-2">
                <span class="block text-[9px] font-black uppercase tracking-widest text-slate-400 mb-2 font-mono">Konfigurasi Sistem</span>
                <a href="kelola_user.php" class="nav-link flex items-center space-x-3 px-5 py-3.5 rounded-2xl text-sm font-semibold text-slate-500 hover:bg-slate-50 hover:text-slate-800 transition-all duration-200">
                    <span>🔐</span> <span>Kelola Pengguna</span>
                </a>
                
            </div>
        <?php endif; ?>
    </nav>

    <!-- 🔥 Bagian Bawah: Pinned Profile & Tombol Keluar (Menggunakan mt-auto agar pas di dasar layar tanpa amblas) -->
    <div class="mt-auto p-4 border-t border-slate-50 bg-slate-50/50">
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-8 h-8 bg-gradient-to-br from-green-700 to-emerald-600 text-white font-black text-xs rounded-xl flex items-center justify-center uppercase shadow-sm">
                <?= substr(htmlspecialchars($user_name), 0, 2); ?>
            </div>
            <div class="flex flex-col overflow-hidden">
                <span class="text-xs font-black text-slate-800 truncate"><?= htmlspecialchars($user_name); ?></span>
                <span class="text-[9px] font-bold text-slate-400 uppercase font-mono tracking-wider">
                    <?= str_replace('_', ' ', $current_role); ?>
                </span>
            </div>
        </div>
        <a href="../../logout.php" class="w-full flex items-center justify-center space-x-2 bg-white border border-slate-200 text-slate-500 text-xs font-bold uppercase tracking-widest py-2.5 rounded-xl hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all duration-300 shadow-sm active:scale-95">
            <span>Keluar Sistem</span>
        </a>
    </div>
</aside>