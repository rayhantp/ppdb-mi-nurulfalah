<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Online - MI Nurul Falah</title>
    <!-- Tailwind CSS & Google Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,700;1,700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-serif { font-family: 'Playfair Display', serif; }
        .glass-effect { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

   <nav id="main-navbar" class="fixed w-full z-50 top-0 left-0 transition-all duration-500 border-b border-transparent bg-transparent">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center transition-all duration-500" id="navbar-container">
        
        <a href="index.php" class="flex items-center space-x-4 group">
            <div class="relative">
                <div class="absolute -inset-1 bg-green-100 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-300"></div>
                <img src="assets/img/logo.png" alt="Logo MI Nurul Falah" class="relative w-12 h-12 object-contain">
            </div>
            <div class="flex flex-col">
                <span id="brand-title" class="text-xl font-black text-white tracking-tight leading-none transition duration-300">MI NURUL FALAH</span>
                <span id="brand-sub" class="text-[10px] font-bold text-emerald-400 tracking-[0.2em] uppercase mt-1 transition duration-300">Unggul & Berakhlak</span>
            </div>
        </a>

        <div class="flex items-center space-x-4 md:space-x-8">
            <div class="hidden md:flex space-x-6">
                <a href="index.php" class="nav-item text-sm font-bold text-slate-200 hover:text-emerald-400 transition duration-300">Beranda</a>
                <a href="#prosedur" class="nav-item text-sm font-bold text-slate-200 hover:text-emerald-400 transition duration-300">Prosedur</a>
                <a href="login.php" class="nav-item text-sm font-bold text-emerald-400 hover:text-emerald-300 transition duration-300">Masuk</a>
            </div>
            
            <a href="register.php" id="btn-daftar" class="hidden md:flex bg-gradient-to-r from-emerald-600 to-green-600 text-white px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-widest hover:from-emerald-700 hover:to-green-700 transition-all duration-300 shadow-lg shadow-emerald-900/30 items-center space-x-2">
                <span>Daftar Sekarang</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                </svg>
            </a>

            <button id="menu-toggle" class="block md:hidden text-white focus:outline-none transition-colors duration-300 p-1 rounded-lg hover:bg-white/10" aria-label="Toggle Menu">
                <svg id="hamburger-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 block">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
                <svg id="close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6 hidden">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden absolute top-full left-0 w-full bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xl px-6 py-6 flex flex-col space-y-4 origin-top transition-all duration-300">
        <a href="index.php" class="text-base font-bold text-slate-600 hover:text-green-700 transition duration-200 py-1 border-b border-slate-50">Beranda</a>
        <a href="#prosedur" class="text-base font-bold text-slate-600 hover:text-green-700 transition duration-200 py-1 border-b border-slate-50">Prosedur</a>
        <a href="login.php" class="text-base font-bold text-green-700 hover:text-green-900 transition duration-200 py-1 border-b border-slate-50">Masuk Portal</a>
        <a href="register.php" class="w-full text-center bg-gradient-to-r from-green-700 to-emerald-700 text-white py-3.5 rounded-xl text-xs font-black uppercase tracking-widest shadow-md flex items-center justify-center space-x-2">
            <span>Daftar Sekarang</span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
            </svg>
        </a>
    </div>
</nav>

<script>
// 1. LOGIKA INTERAKSI KLIK MENU MOBILE (HAMBURGER TOGGLE)
const menuToggle = document.getElementById('menu-toggle');
const mobileMenu = document.getElementById('mobile-menu');
const hamburgerIcon = document.getElementById('hamburger-icon');
const closeIcon = document.getElementById('close-icon');

menuToggle.addEventListener('click', function() {
    // Toggle menu container
    mobileMenu.classList.toggle('hidden');
    
    // Toggle pergantian ikon (Garis tiga <-> Silang X)
    hamburgerIcon.classList.toggle('hidden');
    hamburgerIcon.classList.toggle('block');
    closeIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('block');
});

// 2. LOGIKA RESPONS WARNA SAAT DI-SCROLL DOWN
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('main-navbar');
    const container = document.getElementById('navbar-container');
    const brandTitle = document.getElementById('brand-title');
    const brandSub = document.getElementById('brand-sub');
    const navItems = document.querySelectorAll('.nav-item');
    const btnDaftar = document.getElementById('btn-daftar');

    if (window.scrollY > 50) {
        // State saat digulir ke bawah: Navbar Putih Solid Premium
        navbar.classList.remove('bg-transparent', 'border-transparent');
        navbar.classList.add('bg-white/95', 'backdrop-blur-md', 'border-b', 'border-slate-100', 'shadow-md', 'shadow-slate-100/50');
        
        container.classList.remove('py-4');
        container.classList.add('py-2.5');

        brandTitle.classList.remove('text-white');
        brandTitle.classList.add('text-slate-800');
        brandSub.classList.remove('text-emerald-400');
        brandSub.classList.add('text-green-700');

        menuToggle.classList.remove('text-white');
        menuToggle.classList.add('text-slate-800');

        navItems.forEach(item => {
            if (item.getAttribute('href') === 'login.php') {
                item.classList.remove('text-emerald-400', 'hover:text-emerald-300');
                item.classList.add('text-green-700', 'hover:text-green-900');
            } else {
                item.classList.remove('text-slate-200', 'hover:text-emerald-400');
                item.classList.add('text-slate-600', 'hover:text-green-700');
            }
        });

        btnDaftar.classList.remove('shadow-emerald-900/30');
        btnDaftar.classList.add('shadow-green-700/20');

    } else {
        // State saat kembali ke paling atas: Transparan Kembali
        navbar.classList.remove('bg-white/95', 'backdrop-blur-md', 'border-slate-100', 'shadow-md', 'shadow-slate-100/50');
        navbar.classList.add('bg-transparent', 'border-transparent');
        
        container.classList.remove('py-2.5');
        container.classList.add('py-4');

        brandTitle.classList.remove('text-slate-800');
        brandTitle.classList.add('text-white');
        brandSub.classList.remove('text-green-700');
        brandSub.classList.add('text-emerald-400');

        menuToggle.classList.remove('text-slate-800');
        menuToggle.classList.add('text-white');

        navItems.forEach(item => {
            if (item.getAttribute('href') === 'login.php') {
                item.classList.remove('text-green-700', 'hover:text-green-900');
                item.classList.add('text-emerald-400', 'hover:text-emerald-300');
            } else {
                item.classList.remove('text-slate-600', 'hover:text-green-700');
                item.classList.add('text-slate-200', 'hover:text-emerald-400');
            }
        });

        btnDaftar.classList.remove('shadow-green-700/20');
        btnDaftar.classList.add('shadow-emerald-900/30');
    }
});
</script>

    <!-- Hero Section -->
    <section class="relative min-h-screen w-full flex items-center justify-center bg-slate-950 overflow-hidden py-24 sm:py-32">
    
    <div class="absolute inset-0 w-full h-full z-0 select-none pointer-events-none">
        <img src="assets/img/sekolah.png" alt="MI Nurul Falah" class="w-full h-full object-cover object-center transform scale-100 group-hover:scale-105 transition-transform duration-[10s]">
        <div class="absolute inset-0 bg-gradient-to-tr from-slate-950 via-slate-950/85 to-slate-900/40 mix-blend-multiply"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-slate-950/90"></div>
        <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-emerald-500/10 rounded-full blur-[140px]"></div>
    </div>

    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:6rem_6rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_50%,transparent_100%)] opacity-[0.03] pointer-events-none z-1" style="animation: gridMove 25s linear infinite;"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <div class="lg:col-span-7 text-center lg:text-left">
                
                <div class="inline-flex items-center space-x-3 bg-white/5 backdrop-blur-md border border-white/10 pl-3 pr-5 py-2 rounded-full mb-8 shadow-2xl transition-all duration-300 hover:border-emerald-500/40 hover:bg-white/10">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-emerald-300 font-bold text-[10px] uppercase tracking-[0.25em] font-mono">Official PPDB Portal</span>
                </div>
                
                <h1 class="text-4xl sm:text-6xl lg:text-[4.6rem] font-black text-white mb-6 leading-[1.05] tracking-tight drop-shadow-md">
                    Membentuk Karakter <br>
                    <span class="bg-gradient-to-r from-emerald-400 via-green-400 to-teal-300 bg-clip-text text-transparent underline decoration-emerald-500/40 decoration-wavy decoration-2 underline-offset-8">Unggul</span> 
                    <span class="font-serif font-normal italic text-slate-200 tracking-normal pr-2">Berbasis Islami.</span>
                </h1>
                
                <p class="text-slate-300 text-sm sm:text-base lg:text-lg mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                    Selamat datang di MI Nurul Falah. Transformasi sistem pendaftaran digital yang transparan, aman, dan instan demi kenyamanan akses calon wali murid.
                </p>

                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 mb-12 sm:mb-16">
                    <a href="register.php" class="relative overflow-hidden w-full sm:w-auto text-center bg-gradient-to-r from-emerald-600 to-green-600 text-white px-10 py-4.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-xl shadow-emerald-900/40 hover:shadow-emerald-600/50 hover:translate-y-[-4px] active:translate-y-0 group/btn">
                        <span class="absolute inset-0 w-full h-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:animate-[shimmer_1.2s_infinite]"></span>
                        Daftar Akun Baru
                    </a>
                    <a href="login.php" class="w-full sm:w-auto text-center bg-white/5 backdrop-blur-md hover:bg-white/10 text-white border border-white/20 px-10 py-4.5 rounded-2xl text-xs font-black uppercase tracking-widest transition-all duration-300 shadow-lg hover:border-white/40 hover:translate-y-[-4px]">
                        Masuk Portal Login
                    </a>
                </div>
            </div>

            </div> </div>
    </div>
</section>

<style>
/* Animasi Bergerak Diagonal Lembut Garis Grid Latar Belakang */
@keyframes gridMove {
    0% { background-position: 0 0; }
    100% { background-position: 6rem 6rem; }
}

/* Animasi Kilatan Shimmer Menyapu Permukaan Tombol Registrasi */
@keyframes shimmer {
    100% { transform: translateX(100%); }
}

/* Animasi Amplitudo Parallaks Melayang Lembut untuk Kartu Kaca */
@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}
.animate-bounce-slow {
    animation: bounce-slow 5s infinite ease-in-out;
}
</style>

<!-- =================== SECTION WHY US / FAVORIT (REVISI: HANYA BAYAR SERAGAM) =================== -->
<section class="relative py-32 bg-[#FAFBF9] overflow-hidden border-t border-slate-100">
    
    <!-- Ambient Radial Glow (Efek Cahaya Mewah di Latar Belakang) -->
    <div class="absolute top-1/2 right-1/4 w-[500px] h-[500px] bg-gradient-to-tr from-emerald-100/20 via-green-50/10 to-transparent rounded-full blur-[130px] pointer-events-none -z-10"></div>
    <div class="absolute -bottom-20 -left-20 w-[450px] h-[450px] bg-gradient-to-br from-green-100/20 to-transparent rounded-full blur-[120px] pointer-events-none -z-10"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-16 items-center">
            
            <!-- ─── SISI KIRI: TEKS COPYSCRIPT & JUDUL (lg:col-span-5) ─── -->
            <div class="lg:col-span-5 text-center lg:text-left flex flex-col justify-center">
                
                <!-- Badge Kategori Atas -->
                <div class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/50 pl-3 pr-4 py-1.5 rounded-full mb-6 w-fit mx-auto lg:mx-0 shadow-sm">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                    </span>
                    <span class="text-green-900 font-black text-[10px] uppercase tracking-[0.2em] font-mono">Suara Masyarakat</span>
                </div>
                
                <!-- Judul Utama dengan Wavvy Underline Estetik -->
                <h2 class="text-4xl md:text-5xl lg:text-[3.5rem] font-black text-slate-900 mb-6 leading-[1.1] tracking-tight">
                    Mengapa Kami <br>
                    Menjadi <span class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent underline decoration-green-200/80 decoration-wavy decoration-2 underline-offset-8 italic font-serif font-normal pr-2">Favorit?</span>
                </h2>
                
                <p class="text-slate-400 text-sm md:text-base mb-8 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                    MI Nurul Falah berkomitmen menghadirkan pendidikan madrasah berkualitas tinggi yang merata. Melalui program penataan anggaran yang tepat, kami membebaskan seluruh biaya pendidikan inti demi meringankan beban masa depan anak bangsa.
                </p>

                <!-- Nilai Tambahan Kecil di Bawah Teks -->
                <div class="grid grid-cols-2 gap-4 text-left max-w-md mx-auto lg:mx-0 pt-2 border-t border-slate-100">
                    <div class="flex items-start space-x-2.5">
                        <span class="text-green-600 text-sm mt-0.5">✔</span>
                        <span class="text-xs font-bold text-slate-600 leading-snug">Kurikulum Kemenag Resmi</span>
                    </div>
                    <div class="flex items-start space-x-2.5">
                        <span class="text-green-600 text-sm mt-0.5">✔</span>
                        <span class="text-xs font-bold text-slate-600 leading-snug">Fasilitas Digital Lengkap</span>
                    </div>
                </div>
            </div>

            <!-- ─── SISI KANAN: THE MAHAL SHOWCASE CARD (lg:col-span-7) ─── -->
            <div class="lg:col-span-7 w-full flex justify-center lg:justify-end">
                
                <!-- Card Utama Bergaya Gradasi Gelap Obsidian Premium -->
                <div class="relative w-full max-w-xl bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 rounded-[3rem] p-8 md:p-12 shadow-[0_50px_100px_-20px_rgba(15,23,42,0.3)] border border-slate-800 text-white overflow-hidden group transition-all duration-500 hover:translate-y-[-6px] hover:shadow-[0_60px_110px_-10px_rgba(4,47,31,0.4)]">
                    
                    <!-- Dekorasi Lingkaran Abstrak -->
                    <div class="absolute -right-16 -top-16 w-48 h-48 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none group-hover:bg-emerald-500/20 transition-all duration-700"></div>
                    <div class="absolute -left-20 -bottom-20 w-56 h-56 bg-green-500/5 rounded-full blur-3xl pointer-events-none"></div>

                    <!-- Layout Isi Konten Dalam Card -->
                    <div class="relative z-10 space-y-8">
                        
                        <!-- Header Card -->
                        <div class="flex items-center justify-between border-b border-white/10 pb-6">
                            <div class="flex flex-col">
                                <span class="text-[9px] font-black uppercase tracking-widest text-emerald-400 font-mono">Kebijakan Khusus PPDB</span>
                                <span class="text-sm font-bold text-slate-300 mt-1">Subsidi Biaya Pendidikan Penuh</span>
                            </div>
                            <div class="w-12 h-12 bg-white/5 border border-white/10 rounded-2xl flex items-center justify-center text-xl shadow-inner">
                                🛡️
                            </div>
                        </div>

                        <!-- Highlight Utama: 100% GRATIS -->
                        <div class="space-y-2">
                            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 block font-mono">Biaya Operasional Sekolah</span>
                            <h3 class="text-6xl md:text-7xl font-black tracking-tight bg-gradient-to-r from-white via-slate-100 to-emerald-300 bg-clip-text text-transparent">
                                100% GRATIS
                            </h3>
                            <p class="text-xs font-semibold text-emerald-400 font-mono uppercase tracking-wider pt-1 flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Khusus KK Domisili Kota Tangerang
                            </p>
                        </div>

                        <!-- Detail Hak Istimewa (Glassmorphic Stack Card) -->
                        <div class="bg-white/[0.03] border border-white/5 p-6 rounded-2xl space-y-4 shadow-inner">
                            <span class="block text-[9px] font-black uppercase tracking-widest text-slate-500 font-mono">Rincian Transparansi Biaya:</span>
                            
                            <div class="grid sm:grid-cols-2 gap-4 text-xs font-medium text-slate-300">
                                <div class="flex items-center space-x-3">
                                    <span class="flex h-5 w-5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 items-center justify-center text-[10px] text-emerald-400 font-mono">✓</span>
                                    <span>Bebas Uang Gedung / Pangkal</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="flex h-5 w-5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 items-center justify-center text-[10px] text-emerald-400 font-mono">✓</span>
                                    <span>Bebas SPP / Biaya Bulanan</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="flex h-5 w-5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 items-center justify-center text-[10px] text-emerald-400 font-mono">✓</span>
                                    <span>Bebas Biaya Ujian & Lembar Soal</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="flex h-5 w-5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 items-center justify-center text-[10px] text-emerald-400 font-mono">✓</span>
                                    <span>Gratis Fasilitas Lab & Buku Paket</span>
                                </div>
                            </div>

                            <!-- 🔥 POIN REVISI UTAMA: Dibuat box khusus seragam di dalam komponen mewah -->
                            <div class="mt-4 pt-4 border-t border-white/5 flex items-start space-x-3 bg-emerald-950/20 p-3 rounded-xl border border-emerald-500/10">
                                <span class="text-sm mt-0.5">👕</span>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black text-amber-400 uppercase tracking-wider font-mono">Ketentuan Pakaian:</span>
                                    <span class="text-[11px] text-slate-300 font-medium mt-0.5 leading-relaxed">Wali murid hanya bertanggung jawab atas **Biaya Pengadaan Paket Pakaian Seragam Sekolah Mandiri** untuk calon siswa.</span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Info Card -->
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed italic text-center sm:text-left">
                            *Seluruh sistem kegiatan belajar-mengajar dibiayai penuh oleh subsidi madrasah, memastikan siswa belajar dengan tenang tanpa iuran bulanan tambahan.
                        </p>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>

<section class="relative py-32 bg-[#FCFDFB] overflow-hidden border-t border-slate-100">
    
    <div class="absolute -top-10 -left-20 w-[600px] h-[600px] bg-gradient-to-br from-green-100/30 via-emerald-50/10 to-transparent rounded-full blur-[140px] pointer-events-none animate-pulse duration-[8000ms]"></div>
    
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:5rem_5rem] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_60%,transparent_100%)] opacity-35 pointer-events-none" style="animation: gridMove 25s linear infinite;"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10 w-full">
        <div class="grid lg:grid-cols-12 gap-16 lg:gap-8 items-center">
            
            <div class="lg:col-span-5 flex justify-center lg:justify-start order-1 relative w-full h-[640px] items-center">
                
                <div class="absolute -bottom-8 left-16 w-36 h-36 bg-gradient-to-br from-green-50 to-emerald-50 rounded-full pointer-events-none -z-10 opacity-70"></div>
                <div class="absolute -top-8 left-24 w-28 h-28 border border-dashed border-green-200/60 rounded-[2.5rem] pointer-events-none -z-10 rotate-12"></div>

                <div class="relative group w-full flex justify-center lg:justify-start">
                    <div class="absolute -inset-10 bg-gradient-to-tr from-green-500/10 via-emerald-400/5 to-transparent rounded-[5rem] blur-3xl transition-all duration-700 group-hover:scale-110"></div>

                    <div class="relative w-[300px] h-[600px] bg-slate-950 rounded-[3.2rem] p-3 shadow-[0_50px_100px_-20px_rgba(15,118,110,0.25)] border-[2px] border-slate-800 transition-all duration-500 group-hover:translate-y-[-8px] group-hover:rotate-0 rotate-[-3deg] group-hover:shadow-[0_60px_110px_-10px_rgba(15,118,110,0.35)] z-20">
                        
                        <div class="absolute top-4 left-1/2 -translate-x-1/2 w-20 h-4 bg-black rounded-full z-50 flex items-center justify-end pr-2 space-x-1 pointer-events-none">
                            <div class="w-1.5 h-1.5 bg-slate-900 rounded-full"></div>
                        </div>

                        <div class="relative w-full h-full bg-black rounded-[2.5rem] overflow-hidden border border-black shadow-inner">
                            
                            <iframe 
                                class="absolute inset-0 w-full h-full object-cover z-10" 
                                src="https://www.youtube.com/embed/CaXkcEInOno?autoplay=1&mute=1&loop=1&playlist=CaXkcEInOno&controls=0&modestbranding=1&rel=0&iv_load_policy=3" 
                                title="YouTube video player" 
                                frameborder="0" 
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>

                            <div class="absolute top-0 left-0 right-0 h-20 bg-gradient-to-b from-black/95 via-black/40 to-transparent z-20 pointer-events-none"></div>
                            <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-black/90 via-black/30 to-transparent z-20 pointer-events-none"></div>

                            <div class="absolute inset-0 z-40 bg-transparent cursor-default"></div>
                        </div>

                        <div class="absolute -left-[2px] top-[120px] w-[2px] h-12 bg-slate-800 rounded-l-md z-50"></div> <div class="absolute -left-[2px] top-[180px] w-[2px] h-12 bg-slate-800 rounded-l-md z-50"></div> <div class="absolute -right-[2px] top-[140px] w-[2px] h-16 bg-slate-800 rounded-r-md z-50"></div> </div>

                </div>
            </div>

            <div class="lg:col-span-7 text-center lg:text-left order-2 flex flex-col justify-center">
                
                <div class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/60 pl-3 pr-4 py-1.5 rounded-full mb-6 w-fit mx-auto lg:mx-0">
                    <span class="text-green-900 font-bold text-[10px] uppercase tracking-[0.25em] font-mono">School Identity</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-[3.8rem] font-black text-slate-900 mb-6 leading-[1.1] tracking-tight">
                    Seragam <br>
                    Kebanggaan <span class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent underline decoration-green-200/80 decoration-wavy decoration-2 underline-offset-8 italic font-serif font-normal pr-2">Siswa.</span>
                </h2>
                
                <p class="text-slate-400 text-sm md:text-base mb-10 leading-relaxed max-w-xl mx-auto lg:mx-0 font-medium">
                    Kerapihan mencerminkan kedisiplinan tingkat tinggi. Lihat bagaimana cerianya siswa-siswi kami mengenakan atribut seragam kebanggaan MI Nurul Falah yang bersih, rapi, dan mencerminkan nilai-nilai akhlakul karimah.
                </p>
                
        
            </div> 

        </div> 
    </div>
</section>

<style>
/* Gerakan Gelombang Grid Latar Belakang yang Membuat Desain Hidup */
@keyframes gridMove {
    0% { background-position: 0 0; }
    100% { background-position: 5rem 5rem; }
}
</style>

   <section class="relative py-32 bg-[#FCFDFB] overflow-hidden border-t border-slate-100">
    
    <div class="absolute -bottom-40 -right-20 w-[600px] h-[600px] bg-gradient-to-tl from-emerald-100/20 via-green-50/5 to-transparent rounded-full blur-[150px] pointer-events-none animate-pulse duration-[9000ms]"></div>
    
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:5rem_5rem] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_60%,transparent_100%)] opacity-35 pointer-events-none" style="animation: gridMove 25s linear infinite;"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
        
        <div class="text-center mb-20 relative">
            <div class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/60 pl-3 pr-4 py-1.5 rounded-full mb-4 transition-all hover:scale-105 duration-300">
                <span class="text-green-900 font-bold text-[10px] uppercase tracking-[0.25em] font-mono">Student Development</span>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-[3.8rem] font-black text-slate-900 mb-6 tracking-tight leading-none">
                Program <span class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent underline decoration-green-200/80 decoration-wavy decoration-2 underline-offset-8 italic font-serif font-normal pr-2">Ekstrakurikuler.</span>
            </h2>
            
            <p class="text-slate-400 max-w-2xl mx-auto text-sm md:text-base font-medium leading-relaxed">
                Wadah bagi para siswa untuk mengembangkan bakat, minat, dan kreativitas di luar jam pelajaran sekolah.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <div class="group bg-white rounded-[3.5rem] rounded-tr-2xl rounded-bl-2xl border border-slate-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.02)] overflow-hidden hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative">
                <div class="absolute -inset-px bg-gradient-to-b from-transparent via-green-500/5 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                
                <div class="h-52 w-full overflow-hidden relative">
                    <img src="assets/img/pramuka.png" alt="Kegiatan Pramuka" class="w-full h-full object-cover transform scale-100 group-hover:scale-108 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 px-3 py-1.5 rounded-xl border border-white/50 shadow-sm">Kepanduan</span>
                </div>
                <div class="p-8 relative z-10">
                    <h3 class="text-xl font-black text-slate-800 mb-3 group-hover:text-green-700 transition-colors duration-300">Pramuka</h3>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium leading-relaxed">Melatih kemandirian, kedisiplinan, dan jiwa kepemimpinan melalui kegiatan kepanduan yang seru.</p>
                </div>
            </div>

            <div class="group bg-white rounded-[3.5rem] rounded-tr-2xl rounded-bl-2xl border border-slate-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.02)] overflow-hidden hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative">
                <div class="absolute -inset-px bg-gradient-to-b from-transparent via-green-500/5 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                
                <div class="h-52 w-full overflow-hidden relative">
                    <img src="assets/img/ngaji.png" alt="Tahfidz Qur'an" class="w-full h-full object-cover transform scale-100 group-hover:scale-108 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 px-3 py-1.5 rounded-xl border border-white/50 shadow-sm">Keagamaan</span>
                </div>
                <div class="p-8 relative z-10">
                    <h3 class="text-xl font-black text-slate-800 mb-3 group-hover:text-green-700 transition-colors duration-300">Tahfidz Qur'an</h3>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium leading-relaxed">Program khusus hafalan Al-Qur'an dengan bimbingan ustadz yang berpengalaman.</p>
                </div>
            </div>

            <div class="group bg-white rounded-[3.5rem] rounded-tr-2xl rounded-bl-2xl border border-slate-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.02)] overflow-hidden hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative">
                <div class="absolute -inset-px bg-gradient-to-b from-transparent via-green-500/5 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                
                <div class="h-52 w-full overflow-hidden relative">
                    <img src="assets/img/silat.png" alt="Latihan Pencak Silat" class="w-full h-full object-cover transform scale-100 group-hover:scale-108 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 px-3 py-1.5 rounded-xl border border-white/50 shadow-sm">Bela Diri</span>
                </div>
                <div class="p-8 relative z-10">
                    <h3 class="text-xl font-black text-slate-800 mb-3 group-hover:text-green-700 transition-colors duration-300">Pencak Silat</h3>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium leading-relaxed">Membangun karakter yang tangguh, percaya diri, dan berjiwa ksatria dengan teknik koordinasi gerak yang disiplin.</p>
                </div>
            </div>

            <div class="group bg-white rounded-[3.5rem] rounded-tr-2xl rounded-bl-2xl border border-slate-100 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.02)] overflow-hidden hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative">
                <div class="absolute -inset-px bg-gradient-to-b from-transparent via-green-500/5 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>
                
                <div class="h-52 w-full overflow-hidden relative">
                    <img src="assets/img/hadroh.png" alt="Seni Rebana" class="w-full h-full object-cover transform scale-100 group-hover:scale-108 transition-transform duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/40 via-transparent to-transparent"></div>
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md text-[9px] font-black uppercase tracking-widest text-slate-700 px-3 py-1.5 rounded-xl border border-white/50 shadow-sm">Seni Budaya</span>
                </div>
                <div class="p-8 relative z-10">
                    <h3 class="text-xl font-black text-slate-800 mb-3 group-hover:text-green-700 transition-colors duration-300">Hadroh</h3>
                    <p class="text-slate-400 text-xs sm:text-sm font-medium leading-relaxed">Melestarikan kesenian islami dan mengasah musikalitas siswa melalui rebana.</p>
                </div>
            </div>

        </div> </div>
</section>

<style>
/* Gerakan Grid Latar Belakang Penjaga Ritme Desain */
@keyframes gridMove {
    0% { background-position: 0 0; }
    100% { background-position: 5rem 5rem; }
}
</style>

   <section id="prosedur" class="relative py-32 bg-[#FCFDFB] overflow-hidden border-t border-slate-100">
    
    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-gradient-to-tr from-green-100/20 via-emerald-50/10 to-transparent rounded-full blur-[150px] pointer-events-none animate-pulse duration-[8000ms]"></div>
    
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:5rem_5rem] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_60%,transparent_100%)] opacity-35 pointer-events-none" style="animation: gridMove 25s linear infinite;"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
        
        <div class="text-center mb-24 relative">
            <div class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200/60 pl-3 pr-4 py-1.5 rounded-full mb-4 transition-all hover:scale-105 duration-300">
                <span class="text-green-900 font-bold text-[10px] uppercase tracking-[0.25em] font-mono">Registration Workflow</span>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-[3.8rem] font-black text-slate-900 mb-6 tracking-tight leading-none">
                Langkah Mudah <span class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent underline decoration-green-200/80 decoration-wavy decoration-2 underline-offset-8 italic font-serif font-normal pr-2">Bergabung.</span>
            </h2>
            
            <p class="text-slate-400 max-w-xl mx-auto text-sm md:text-base font-medium leading-relaxed">
                Pahami tahapan alur pendaftaran digital mandiri untuk calon peserta didik baru MI Nurul Falah.
            </p>
        </div>

        <div class="relative w-full">
            
            <div class="absolute top-1/3 left-[12%] right-[12%] h-[2px] border-t-2 border-dashed border-green-200/50 -z-10 hidden lg:block"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-20">
                
                <div class="group bg-white rounded-[3.5rem] rounded-tr-xl rounded-bl-xl border border-slate-100 p-8 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.01)] hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative overflow-hidden flex flex-col items-center text-center">
                    <span class="absolute -right-4 -bottom-6 text-[7rem] font-mono font-black text-slate-100 select-none pointer-events-none group-hover:text-green-50/50 group-hover:scale-105 transition-all duration-500">01</span>
                    
                    <div class="w-20 h-20 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mb-8 relative z-10 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-green-700 group-hover:to-emerald-600 group-hover:text-white group-hover:rotate-[360deg] transition-all duration-[700ms] shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>
                    </div>
                    
                    <h3 class="font-black text-slate-800 text-lg group-hover:text-green-700 transition-colors duration-300 relative z-10">Buat Akun</h3>
                    <p class="mt-4 text-xs sm:text-sm text-slate-400 font-medium leading-relaxed relative z-10">Mendaftarkan identitas singkat wali murid untuk mendapatkan akses login.</p>
                </div>

                <div class="group bg-white rounded-[3.5rem] rounded-tr-xl rounded-bl-xl border border-slate-100 p-8 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.01)] hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative overflow-hidden flex flex-col items-center text-center">
                    <span class="absolute -right-4 -bottom-6 text-[7rem] font-mono font-black text-slate-100 select-none pointer-events-none group-hover:text-green-50/50 group-hover:scale-105 transition-all duration-500">02</span>
                    
                    <div class="w-20 h-20 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mb-8 relative z-10 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-green-700 group-hover:to-emerald-600 group-hover:text-white group-hover:rotate-[360deg] transition-all duration-[700ms] shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                        </svg>
                    </div>
                    
                    <h3 class="font-black text-slate-800 text-lg group-hover:text-green-700 transition-colors duration-300 relative z-10">Isi Formulir</h3>
                    <p class="mt-4 text-xs sm:text-sm text-slate-400 font-medium leading-relaxed relative z-10">Lengkapi data pribadi, alamat, serta informasi detail orang tua/wali.</p>
                </div>

                <div class="group bg-white rounded-[3.5rem] rounded-tr-xl rounded-bl-xl border border-slate-100 p-8 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.01)] hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative overflow-hidden flex flex-col items-center text-center">
                    <span class="absolute -right-4 -bottom-6 text-[7rem] font-mono font-black text-slate-100 select-none pointer-events-none group-hover:text-green-50/50 group-hover:scale-105 transition-all duration-500">03</span>
                    
                    <div class="w-20 h-20 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mb-8 relative z-10 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-green-700 group-hover:to-emerald-600 group-hover:text-white group-hover:rotate-[360deg] transition-all duration-[700ms] shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                        </svg>
                    </div>
                    
                    <h3 class="font-black text-slate-800 text-lg group-hover:text-green-700 transition-colors duration-300 relative z-10">Upload Berkas</h3>
                    <p class="mt-4 text-xs sm:text-sm text-slate-400 font-medium leading-relaxed relative z-10">Unggah scan Kartu Keluarga, Akta Kelahiran, dan foto calon siswa.</p>
                </div>

                <div class="group bg-white rounded-[3.5rem] rounded-tr-xl rounded-bl-xl border border-slate-100 p-8 shadow-[0_30px_60px_-15px_rgba(0,0,0,0.01)] hover:-translate-y-3 hover:shadow-[0_40px_80px_-15px_rgba(15,118,110,0.12)] hover:border-green-200/60 transition-all duration-500 relative overflow-hidden flex flex-col items-center text-center">
                    <span class="absolute -right-4 -bottom-6 text-[7rem] font-mono font-black text-slate-100 select-none pointer-events-none group-hover:text-green-50/50 group-hover:scale-105 transition-all duration-500">04</span>
                    
                    <div class="w-20 h-20 bg-green-50 text-green-700 rounded-2xl flex items-center justify-center mb-8 relative z-10 group-hover:scale-110 group-hover:bg-gradient-to-br group-hover:from-green-700 group-hover:to-emerald-600 group-hover:text-white group-hover:rotate-[360deg] transition-all duration-[700ms] shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                        </svg>
                    </div>
                    
                    <h3 class="font-black text-slate-800 text-lg group-hover:text-green-700 transition-colors duration-300 relative z-10">Hasil Seleksi</h3>
                    <p class="mt-4 text-xs sm:text-sm text-slate-400 font-medium leading-relaxed relative z-10">Pantau status pendaftaran dan unduh kartu bukti penerimaan.</p>
                </div>

            </div> </div>
    </div>
</section>

<style>
/* Gerakan Grid Latar Belakang Penjaga Keselarasan Tema Web */
@keyframes gridMove {
    0% { background-position: 0 0; }
    100% { background-position: 5rem 5rem; }
}
</style>


   <footer class="relative bg-gradient-to-b from-slate-950 via-[#02180b] to-slate-950 text-white pt-28 pb-12 overflow-hidden border-t border-white/5">
    
    <div class="absolute -top-40 -left-40 w-[500px] h-[500px] bg-emerald-500/10 rounded-full blur-[140px] pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] bg-green-500/5 rounded-full blur-[140px] pointer-events-none"></div>
    
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff_1px,transparent_1px),linear-gradient(to_bottom,#ffffff_1px,transparent_1px)] bg-[size:5rem_5rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_40%,transparent_100%)] opacity-[0.02] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 relative z-10">
        
        <div class="grid grid-cols-1 md:grid-cols-12 gap-16 md:gap-8 items-start">
            
            <div class="md:col-span-5 flex flex-col items-start space-y-6">
                <div class="flex flex-col items-start group">
                    <img src="assets/img/logo.png" 
                         alt="Logo MI Nurul Falah" 
                         class="h-24 w-auto mb-3 brightness-110 contrast-105 drop-shadow-[0_0_25px_rgba(16,185,129,0.2)] transition-transform duration-500 group-hover:scale-105">
                    <div class="h-[3px] w-14 bg-gradient-to-r from-emerald-500 to-green-400 rounded-full mt-2"></div>
                </div>
                <p class="text-slate-400 text-sm leading-relaxed max-w-sm font-medium">
                    Menjadi Sekolah Terpercaya di Masyarakat untuk menciptakan generasi yang beriman, bertaqwa, berpengetahuan luas, disiplin dan berakhlakul karimah.
                </p>
            </div>

            <div class="md:col-span-4 flex flex-col">
                <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-400 mb-8 font-mono">Hubungi Kami</h4>
                <ul class="text-slate-300 text-sm space-y-5">
                    
                    <li class="flex items-start gap-4 group/item">
                        <div class="w-10 h-10 bg-white/[0.03] border border-white/10 rounded-xl flex items-center justify-center shrink-0 group-hover/item:border-emerald-500/40 group-hover/item:bg-white/[0.07] transition-all duration-300 shadow-inner text-base">
                            📍
                        </div>
                        <span class="leading-relaxed font-medium text-slate-300 group-hover/item:text-white transition-colors pt-1.5">
                            Jl. H.Jali RT 01/03 Kel Kunciran Jaya <br><span class="text-slate-400">Kecamatan Pinang Kota Tangerang, Banten</span>
                        </span>
                    </li>
                    
                    <li class="flex items-center gap-4 group/item">
                        <div class="w-10 h-10 bg-white/[0.03] border border-white/10 rounded-xl flex items-center justify-center shrink-0 group-hover/item:border-emerald-500/40 group-hover/item:bg-white/[0.07] transition-all duration-300 shadow-inner text-base">
                            📞
                        </div>
                        <span class="font-medium text-slate-300 group-hover/item:text-white transition-colors">(021) 1234567</span>
                    </li>
                    
                    <li class="flex items-center gap-4 group/item">
                        <div class="w-10 h-10 bg-white/[0.03] border border-white/10 rounded-xl flex items-center justify-center shrink-0 group-hover/item:border-emerald-500/40 group-hover/item:bg-white/[0.07] transition-all duration-300 shadow-inner text-base">
                            ✉️
                        </div>
                        <span class="font-medium text-slate-400 hover:text-emerald-400 transition-colors cursor-pointer break-all font-mono text-xs sm:text-sm">mi.nurulfalahpinangtng@gmail.com</span>
                    </li>
                </ul>
            </div>

            <div class="md:col-span-3 flex flex-col">
                <h4 class="text-[10px] font-black uppercase tracking-[0.25em] text-emerald-400 mb-8 font-mono">Navigasi</h4>
                <ul class="text-slate-400 text-sm space-y-4 font-medium">
                    <li>
                        <a href="login.php" class="hover:text-emerald-400 transition-all flex items-center gap-3 group/link">
                            <span class="h-[2px] w-0 bg-emerald-500 group-hover:w-4 transition-all duration-300 rounded-full"></span>
                            Portal Admin
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-emerald-400 transition-all flex items-center gap-3 group/link">
                            <span class="h-[2px] w-0 bg-emerald-500 group-hover:w-4 transition-all duration-300 rounded-full"></span>
                            Panduan Pendaftaran
                        </a>
                    </li>
                    <li>
                        <a href="#" class="hover:text-emerald-400 transition-all flex items-center gap-3 group/link">
                            <span class="h-[2px] w-0 bg-emerald-500 group-hover:w-4 transition-all duration-300 rounded-full"></span>
                            Tentang Sekolah
                        </a>
                    </li>
                </ul>
            </div>

        </div> <div class="mt-24 pt-8 border-t border-white/5 flex flex-col sm:flex-row justify-between items-center gap-6">
            
            <div class="flex flex-col items-center sm:items-start gap-1">
                <p class="text-slate-500 text-[10px] uppercase tracking-[0.4em] font-mono">
                    &copy; 2026 MI Nurul Falah
                </p>
                <p class="text-[9px] text-slate-600 font-bold uppercase tracking-wider">Pinang, Kota Tangerang — Indonesia</p>
            </div>
            
            <div class="flex items-center gap-3 bg-white/[0.02] backdrop-blur-sm px-4 py-2 rounded-xl border border-white/5 shadow-2xl">
                <div class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></div>
                <span class="text-[10px] text-slate-400 uppercase tracking-[0.15em] font-black font-mono">MI Nurul Falah</span>
            </div>
        </div>

    </div> </footer>

</body>
</html>