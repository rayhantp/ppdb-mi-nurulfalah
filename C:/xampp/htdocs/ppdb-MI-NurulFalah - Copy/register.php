<?php
session_start();
require_once 'config/database.php';

$message = "";
$status_type = "error"; // Indikator warna alert (error / success)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama     = $_POST['nama_lengkap'];
    $username = $_POST['username']; // Pada praktiknya bisa digunakan sebagai NISN
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role     = 'siswa';

    try {
        // Cek apakah username sudah ada
        $stmt = $pdo->prepare("SELECT id_user FROM users WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->rowCount() > 0) {
            $message = "Username / NISN sudah terdaftar di sistem, silakan gunakan yang lain!";
            $status_type = "error";
        } else {
            // Mulai Database Transaction agar kedua insert aman (jika satu gagal, semua batal)
            $pdo->beginTransaction();

            // 1. Insert ke tabel users
            $query_user = "INSERT INTO users (nama_lengkap, username, password, role) VALUES (?, ?, ?, ?)";
            $stmt_user  = $pdo->prepare($query_user);
            $stmt_user->execute([$nama, $username, $password, $role]);
            
            // Ambil ID User yang barusan terbuat
            $id_user_baru = $pdo->lastInsertId();

            // 2. REVISI MANDATORI: Otomatis daftarkan baris baru di tabel PENDAFTAR agar sinkron
            $query_siswa = "INSERT INTO pendaftar (id_user, nama_siswa, nisn) VALUES (?, ?, ?)";
            $stmt_siswa  = $pdo->prepare($query_siswa);
            $stmt_siswa->execute([$id_user_baru, $nama, $username]);

            // Jika keduanya sukses, simpan permanen ke database
            $pdo->commit();

            $message = "Registrasi Berhasil! Akun siswa Anda telah aktif. Mengalihkan ke login...";
            $status_type = "success";
            
            // Redirect ke login setelah 2 detik
            header("refresh:2;url=login.php");
        }
    } catch (PDOException $e) {
        // Batalkan insert jika terjadi error database
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $message = "Ups! Terjadi kesalahan sistem: " . $e->getMessage();
        $status_type = "error";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi PPDB - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-green-900 via-green-800 to-slate-900">

    <div class="bg-white/95 backdrop-blur-md w-full max-w-4xl rounded-[2.5rem] shadow-2xl shadow-black/30 overflow-hidden grid md:grid-cols-2 min-h-[580px] transition-all duration-300">
        
        <div class="bg-gradient-to-tr from-green-800 to-emerald-600 p-12 text-white flex flex-col justify-between relative overflow-hidden hidden md:flex">
            <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-lg pointer-events-none"></div>

            <div class="flex items-center space-x-3 relative z-10">
                <img src="assets/img/logo.png" alt="Logo MI" class="w-10 h-10 brightness-110 drop-shadow-md">
                <div>
                    <h1 class="text-sm font-black uppercase tracking-wider leading-none">MI Nurul Falah</h1>
                    <p class="text-[9px] text-green-200 tracking-widest uppercase mt-0.5">Sistem Penerimaan Siswa Baru</p>
                </div>
            </div>

            <div class="my-auto space-y-6 relative z-10">
                <h2 class="text-2xl font-black tracking-tight leading-tight">3 Langkah Mudah Menjadi Bagian dari Kami:</h2>
                
                <div class="space-y-4 text-xs">
                    <div class="flex items-center gap-3 bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="w-6 h-6 bg-white text-green-800 rounded-full flex items-center justify-center font-bold">1</span>
                        <span>Buat akun akses portal siswa menggunakan NISN aktif.</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/5 p-3 rounded-xl border border-white/5">
                        <span class="w-6 h-6 bg-white/20 text-white rounded-full flex items-center justify-center font-bold">2</span>
                        <span>Isi biodata lengkap & unggah lampiran berkas PDF.</span>
                    </div>
                    <div class="flex items-center gap-3 bg-white/5 p-3 rounded-xl border border-white/5">
                        <span class="w-6 h-6 bg-white/20 text-white rounded-full flex items-center justify-center font-bold">3</span>
                        <span>Pantau hasil kelulusan langsung dari dashboard Anda.</span>
                    </div>
                </div>
            </div>

            <p class="text-[10px] text-green-200/50 relative z-10 uppercase tracking-widest">&copy; 2026 MI Nurul Falah</p>
        </div>

        <div class="p-8 sm:p-12 flex flex-col justify-center">
            
            <div class="text-center md:text-left mb-6">
                <img src="assets/img/logo.png" alt="Logo MI" class="w-16 h-16 mx-auto md:mx-0 mb-4 brightness-105 md:hidden">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Daftar Akun Baru</h3>
                <p class="text-sm text-slate-400 mt-1">Lengkapi kolom di bawah untuk mendapatkan akses login pendaftaran.</p>
            </div>

            <?php if ($message): ?>
                <?php $alertClass = ($status_type == 'success') ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-600'; ?>
                <div class="border p-4 rounded-2xl mb-6 text-xs font-bold flex items-center gap-2 <?= $alertClass; ?>">
                    <span><?= ($status_type == 'success') ? '✨' : '⚠️'; ?></span>
                    <p><?= $message; ?></p>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Nama Lengkap Calon Siswa</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">📝</span>
                        <input type="text" name="nama_lengkap" placeholder="Masukkan nama sesuai akta" required 
                               class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Username / NISN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">👤</span>
                        <input type="text" name="username" placeholder="Gunakan nomor NISN / Angka unik" required 
                               class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Buat Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">🔒</span>
                        <input type="password" name="password" placeholder="Minimal 6 karakter" required 
                               class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                    </div>
                </div>

                <div class="pt-3">
                    <button type="submit" class="w-full bg-green-700 text-white py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-green-800 transition-all shadow-lg shadow-green-700/20 hover:shadow-xl hover:shadow-green-700/30">
                        Daftar Sekarang
                    </button>
                </div>
            </form>

            <div class="mt-6 text-center text-xs font-medium text-slate-400">
                <p>Sudah terdaftar sebelumnya? 
                    <a href="login.php" class="text-green-700 font-bold hover:text-green-900 hover:underline transition-all ml-1">Masuk Portal</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>