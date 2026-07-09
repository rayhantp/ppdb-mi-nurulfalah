<?php
// 1. Jalankan session dan panggil file koneksi database Anda
session_start();
include '../../config/database.php'; // Memanggil file database.php Anda

// Proteksi halaman admin
if (!isset($_SESSION['nama_lengkap'])) {
    header("Location: login.php");
    exit();
}

$current_role = $_SESSION['role'] ?? 'operator';

// Proteksi Matriks: Kepala Sekolah tidak boleh menambah data pendaftar
if ($current_role === 'kepala_sekolah') {
    echo "<script>
            alert('Akses Ditolak! Akun Kepala Sekolah tidak memiliki otoritas menambah pendaftar.');
            window.location.href = 'pendaftar.php';
          </script>";
    exit();
}

$message = "";
$message_type = ""; // 'success' atau 'error'

// ─── 🛠️ BACKEND ENGINE: PROSES INPUT DATA KE DATABASE (CREATE) ───
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_siswa'])) {
    $nama_siswa = $_POST['nama_siswa'];
    $nisn = $_POST['nisn'];
    $status_pendaftaran = $_POST['status_pendaftaran'];
    
    // 🔥 FITUR PREMIUM SKRIPSI: Generate Nomor Pendaftaran Otomatis (Contoh: PPDB-2026-8472)
    $no_pendaftaran = "PPDB-" . date('Y') . "-" . rand(1000, 9999);

    try {
        // Query SQL menyisipkan data ke tabel siswa (Ganti nama tabel jika di DB Anda bukan 'siswa')
        $sql = "INSERT INTO siswa (nama_siswa, nisn, no_pendaftaran, status_pendaftaran) 
                VALUES (:nama_siswa, :nisn, :no_pendaftaran, :status_pendaftaran)";
        
        $stmt = $pdo->prepare($sql); // Menggunakan variabel $pdo asli dari database.php Anda
        $stmt->execute([
            ':nama_siswa'         => $nama_siswa,
            ':nisn'               => $nisn,
            ':no_pendaftaran'     => $no_pendaftaran,
            ':status_pendaftaran' => $status_pendaftaran
        ]);

        $message = "Siswa baru bernama <b>" . htmlspecialchars($nama_siswa) . "</b> berhasil didaftarkan dengan No Reg: <b>" . $no_pendaftaran . "</b>";
        $message_type = "success";
    } catch (PDOException $e) {
        $message = "Gagal menyimpan data ke database: " . $e->getMessage();
        $message_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pendaftar Baru - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }
        #content-area { animation: slideIn 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes slideIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes gridMove { 0% { background-position: 0 0; } 100% { background-position: 4rem 4rem; } }
        .moving-grid-bg { animation: gridMove 30s linear infinite; }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <div class="absolute top-0 right-0 w-[550px] h-[550px] bg-gradient-to-bl from-green-200/20 to-emerald-100/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_70%,transparent_100%)] opacity-[0.25] pointer-events-none -z-10 moving-grid-bg"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        
        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-3xl mx-auto">
                
                <div class="mb-8">
                    <a href="pendaftar.php" class="inline-flex items-center text-xs font-black uppercase tracking-widest text-slate-400 hover:text-green-700 transition-all duration-300 gap-2 group">
                        <span class="transform transition-transform group-hover:-translate-x-1 font-mono text-sm">←</span> 
                        <span>Kembali ke Database</span>
                    </a>
                </div>

                <?php if ($message): ?>
                    <div class="mb-6 p-4 rounded-2xl text-xs font-black uppercase tracking-wider shadow-sm border <?= ($message_type === 'success') ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200'; ?>">
                        <?= ($message_type === 'success') ? '✨' : '❌'; ?> <?= $message; ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 p-8 sm:p-12 shadow-[0_30px_60px_-20px_rgba(0,0,0,0.02)] relative overflow-hidden">
                    <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-green-700 to-emerald-600"></div>
                    
                    <header class="mb-10 border-b border-slate-100 pb-5">
                        <h2 class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">Tambah Calon Siswa</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1">Gunakan formulir ini untuk memasukkan data pendaftar baru secara manual melalui internal sekolah.</p>
                    </header>

                    <form action="" method="POST" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Nama Lengkap Siswa</label>
                                <input type="text" name="nama_siswa" required placeholder="Masukkan nama lengkap..." class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:border-green-600 transition-all">
                            </div>
                            
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Nomor NISN</label>
                                <input type="text" name="nisn" required placeholder="Masukkan 10 digit NISN..." class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-mono font-semibold focus:outline-none focus:border-green-600 transition-all">
                            </div>
                            
                            <div class="space-y-2 sm:col-span-2">
                                <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Status Awal Pendaftaran</label>
                                <select name="status_pendaftaran" class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black uppercase tracking-wider text-slate-700 focus:outline-none focus:border-green-600 transition-all font-mono cursor-pointer">
                                    <option value="proses">🟡 DALAM PROSES (PENDING)</option>
                                    <option value="diterima">🟢 LANGSUNG DITERIMA / LULUS</option>
                                </select>
                            </div>
                            
                        </div>

                        <div class="pt-6">
                            <button type="submit" name="tambah_siswa" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-green-700 transition-all duration-300 shadow-xl hover:shadow-green-700/20 transform hover:translate-y-[-2px]">
                                ➕ Daftarkan Calon Siswa Baru
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </main>
    </div>

</body>
</html>