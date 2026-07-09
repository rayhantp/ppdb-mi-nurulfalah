<?php
session_start();
require_once '../../config/database.php';

// Proteksi Halaman Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../login.php");
    exit;
}

$message = "";
$error = "";

// Logika Ganti Password
if (isset($_POST['update_password'])) {
    $id_user = $_SESSION['id_user'];
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];

    $stmt = $pdo->prepare("SELECT password FROM users WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user = $stmt->fetch();

    if ($user && password_verify($old_pass, $user['password'])) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        $update->execute([$hashed, $id_user]);
        $message = "Sandi administrator berhasil diperbarui dengan aman!";
    } else {
        $error = "Gagal memproses! Kata sandi lama yang Anda masukkan tidak akurat.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keamanan Akun Admin - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }
        
        .active-link { 
            background-color: #f0fdf4 !important; 
            color: #15803d !important; 
            border-right: 4px solid #15803d; 
            font-weight: 700; 
        }

        #content-area {
            animation: lockFade 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes lockFade {
            from { opacity: 0; transform: scale(0.98) translateY(5px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }
    </style>
</head>
<body class="bg-[#FBFCFA] text-slate-700">

    <div class="flex flex-col md:flex-row min-h-screen bg-slate-50">

        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 flex flex-col justify-center items-center overflow-y-auto min-h-screen">
            <div class="max-w-md w-full mx-auto">
                
                <header class="text-center mb-10">
                    <div class="w-16 h-16 bg-green-50 text-green-700 rounded-full flex items-center justify-center mx-auto mb-4 border border-green-100 shadow-inner text-xl">
                        🛡️
                    </div>
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight">Keamanan Admin</h2>
                    <p class="text-sm text-slate-400 mt-1">Perbarui kredensial kata sandi panel pelacak enkripsi secara berkala.</p>
                </header>

                <?php if ($message): ?>
                    <div class="mb-6 p-4 bg-green-50 text-green-700 border border-green-100 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                        <span>✨</span> <?= $message ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="mb-6 p-4 bg-red-50 text-red-600 border border-red-100 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-sm">
                        <span>⚠️</span> <?= $error ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white p-8 md:p-10 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-100/40 relative overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-green-600 to-emerald-500"></div>

                    <form action="" method="POST" class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Password Lama Anda</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-xs">🔑</span>
                                <input type="password" name="old_password" placeholder="••••••••" required 
                                       class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400">Password Baru Ditargetkan</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-4 flex items-center text-slate-400 text-xs">🔒</span>
                                <input type="password" name="new_password" placeholder="Min. 8 Karakter" required 
                                       class="w-full pl-11 pr-5 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500/20 focus:border-green-600 transition-all font-medium">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" name="update_password" 
                                    class="w-full bg-green-700 text-white py-4 rounded-xl font-bold uppercase tracking-widest text-[10px] hover:bg-green-800 transition-all shadow-lg shadow-green-700/20 hover:shadow-xl hover:shadow-green-700/30">
                                Perbarui Kata Sandi
                            </button>
                        </div>
                    </form>
                </div>
                
            </div>
        </main>
    </div>

    <script>
    document.addEventListener('click', function(e) {
        const link = e.target.closest('.nav-link');
        if (!link) return;
        if (link.href.includes('logout.php') || link.getAttribute('target') === '_blank') return;

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
                    // Reset kelas class flex center khusus agar halaman lain tidak terpengaruh jalurnya
                    if(url.includes('password.php')) {
                        document.querySelector('#content-area').classList.add('flex', 'flex-col', 'justify-center', 'items-center');
                    } else {
                        document.querySelector('#content-area').classList.remove('flex', 'flex-col', 'justify-center', 'items-center');
                    }
                    
                    history.pushState(null, '', url);

                    // Sinkronisasi kelas active menu di sidebar secara akurat
                    document.querySelectorAll('.nav-link').forEach(nav => {
                        nav.classList.remove('active-link');
                        if (nav.getAttribute('href').split('/').pop() === url.split('/').pop()) {
                            nav.classList.add('active-link');
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