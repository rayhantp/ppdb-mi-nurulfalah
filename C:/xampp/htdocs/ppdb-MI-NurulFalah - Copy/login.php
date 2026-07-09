<?php
session_start();
include 'config/database.php'; 

$message = ""; 
$error = ""; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? ''); 

    if (empty($username) || empty($password)) {
        $error = "Username dan Password wajib diisi!";
        $message = "Username dan Password wajib diisi!";
    } else {
        try {
            $sql = "SELECT * FROM users WHERE username = :username LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                $db_password = $user['password'] ?? $user['pass'] ?? '';
                $password_valid = false;

                // Cek Validasi Kata Sandi (Polos, MD5, atau Bcrypt)
                if ($password === $db_password) {
                    $password_valid = true; 
                } elseif (md5($password) === $db_password) {
                    $password_valid = true; 
                } elseif (password_verify($password, $db_password)) {
                    $password_valid = true; 
                }

                if ($password_valid) {
                    // Perekaman Session Identitas
                    $_SESSION['id_user']      = $user['id_user'] ?? $user['id'] ?? ''; 
                    $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? $user['nama'] ?? 'User';
                    $_SESSION['role']         = $user['role'] ?? 'siswa'; 

                    // ─── 🔥 GERBANG ROUTING SYSTEM 4 PINTU EKSKLUSIF (PILIHAN TERBAIK UNTUK SIDANG) ───
switch ($_SESSION['role']) {
    case 'admin':
        header("Location: views/admin/dashboard.php");
        break;

    case 'operator':
        // 🟡 Ditendang langsung ke folder khusus operator
        header("Location: views/admin/dashboard.php");
        break;

    case 'kepala_sekolah':
        // 🔒 Ditendang langsung ke folder khusus kepala sekolah (kepsek)
        header("Location: views/admin/dashboard.php");
        break;

    case 'siswa':
        // 🟢 Ditendang langsung ke portal siswa
        header("Location: views/user/dashboard.php");
        break;

    default:
        session_destroy();
        header("Location: login.php");
        break;
}
exit();
                    
                } else {
                    $error = "Kata sandi yang Anda masukkan salah!";
                    $message = "Kata sandi yang Anda masukkan salah!";
                }
            } else {
                $error = "Username tidak terdaftar di dalam sistem!";
                $message = "Username tidak terdaftar di dalam sistem!";
            }
        } catch (PDOException $e) {
            $error = "Gangguan Sistem: " . $e->getMessage();
            $message = "Gangguan Sistem: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PPDB - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 sm:p-6 bg-gradient-to-br from-green-900 via-green-800 to-slate-900">

    <div class="bg-white/95 backdrop-blur-md w-full max-w-4xl rounded-[2.5rem] shadow-2xl shadow-black/30 overflow-hidden grid md:grid-cols-2 min-h-[550px] transition-all duration-300">
        
        <div class="bg-gradient-to-tr from-green-800 to-emerald-600 p-12 text-white flex flex-col justify-between relative overflow-hidden hidden md:flex">
            <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
            <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full blur-lg pointer-events-none"></div>

            <div class="flex items-center space-x-3 relative z-10">
                <img src="assets/img/logo.png" alt="Logo MI" class="w-10 h-10 brightness-110 drop-shadow-md">
                <div>
                    <h1 class="text-sm font-black uppercase tracking-wider leading-none">MI Nurul Falah</h1>
                    <p class="text-[9px] text-green-200 tracking-widest uppercase mt-0.5">Pinang, Kota Tangerang</p>
                </div>
            </div>

            <div class="my-auto space-y-4 relative z-10">
                <span class="px-3 py-1 bg-white/10 border border-white/10 text-[10px] font-bold uppercase tracking-widest rounded-full">
                    Sistem Digitalisasi Sekolah
                </span>
                <h2 class="text-3xl font-black tracking-tight leading-snug">Gerbang Transformasi Akademik Generasi Shalih</h2>
                <p class="text-xs text-green-100/80 leading-relaxed max-w-xs">
                    Selamat datang di Portal PPDB Online. Silakan masuk untuk melengkapi data formulir dan memantau berkas verifikasi.
                </p>
            </div>

            <p class="text-[10px] text-green-200/50 relative z-10 uppercase tracking-widest">&copy; 2026 MI Nurul Falah</p>
        </div>

        <div class="p-8 sm:p-12 flex flex-col justify-center">
            
            <div class="text-center md:text-left mb-8">
                <img src="assets/img/logo.png" alt="Logo MI" class="w-16 h-16 mx-auto md:mx-0 mb-4 brightness-105 md:hidden">
                <h3 class="text-2xl font-black text-slate-800 tracking-tight">Selamat Datang</h3>
                <p class="text-sm text-slate-400 mt-1">Masukkan kredensial akun Anda untuk melanjutkan.</p>
            </div>

            <?php if ($message): ?>
                <div class="bg-red-50 border border-red-100 text-red-600 p-4 rounded-2xl mb-6 text-xs font-bold flex items-center gap-2">
                    <span>⚠️</span> <?= $message; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-5">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Username / NISN</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">👤</span>
                        <input type="text" name="username" placeholder="Masukkan username Anda" required 
                               class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-400">Kata Sandi</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-sm">🔒</span>
                        <input type="password" name="password" placeholder="••••••••" required 
                               class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-green-700 text-white py-4 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-green-800 transition-all shadow-lg shadow-green-700/20 hover:shadow-xl hover:shadow-green-700/30">
                        Masuk Ke Portal
                    </button>
                </div>
            </form>

            <div class="mt-8 text-center text-xs font-medium text-slate-400">
                <p>Belum memiliki akun pendaftaran? 
                    <a href="register.php" class="text-green-700 font-bold hover:text-green-900 hover:underline transition-all ml-1">Daftar Akun Baru</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>

<?php include 'includes/loader.php'; ?>

<script>
document.querySelector('form').addEventListener('submit', function() {
    const loader = document.getElementById('loading-overlay');
    if(loader) {
        loader.classList.remove('hidden');
        loader.classList.add('flex');
    }
});
</script>