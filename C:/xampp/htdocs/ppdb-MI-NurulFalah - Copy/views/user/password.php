<?php
session_start();
require_once '../../config/database.php';

// Proteksi Halaman Siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../../login.php");
    exit;
}

$message = "";
$error = "";
$id_user = $_SESSION['id_user'];

// --- AMBIL DATA PASSWORD SEKARANG (Setiap kali halaman dimuat) ---
$stmt_get = $pdo->prepare("SELECT password FROM users WHERE id_user = ?");
$stmt_get->execute([$id_user]);
$user = $stmt_get->fetch();
$current_hashed_pass = $user['password'] ?? ''; // Ini yang akan muncul di box

// Logika Ganti Password (TANPA HASH)
if (isset($_POST['update_password'])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];

    // Cek password lama langsung (teks biasa vs teks biasa)
    if ($old_pass === $current_hashed_pass) { 
        // Update tanpa di-hash
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id_user = ?");
        $update->execute([$new_pass, $id_user]);
        
        $current_hashed_pass = $new_pass; // Update variabel lokal
        $message = "Password kamu berhasil diperbarui!";
    } else {
        $error = "Password lama kamu salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Quicksand', sans-serif; } html { font-size: 14px; }</style>
</head>
<body class="bg-[#FBFCFA] text-slate-700">

    <div class="min-h-screen flex flex-col md:flex-row">
        <div class="flex flex-col md:flex-row min-h-screen bg-slate-50">

    <?php include 'sidebar_siswa.php'; ?>

    <main class="flex-1 p-8 md:p-12 overflow-y-auto">
        </main>
</div>

        <!-- KONTEN UTAMA -->
        <main id="content-area" class="flex-1 p-8 md:p-12 overflow-y-auto">
            <div class="max-w-md mx-auto">
                <header class="mb-10 text-center md:text-left">
                    <h2 class="text-2xl font-bold text-green-900 leading-tight">Keamanan Akun</h2>
                    <p class="text-sm text-slate-400 mt-1">Ganti password kamu secara berkala demi keamanan data.</p>
                </header>

                <?php if ($message): ?>
                    <div class="mb-8 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-100 text-sm font-bold">✨ <?= $message ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="mb-8 p-4 bg-red-50 text-red-600 rounded-2xl border border-red-100 text-sm font-bold">⚠️ <?= $error ?></div>
                <?php endif; ?>

                <div class="bg-white p-8 rounded-[2rem] border border-slate-100 shadow-sm">
                    <form action="" method="POST" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password Lama</label>
                            <input type="password" name="old_password" required class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:ring-2 focus:ring-green-100 transition text-sm">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password Baru</label>
                            <input type="password" name="new_password" required class="w-full px-4 py-3 rounded-xl border border-slate-100 bg-slate-50/50 outline-none focus:ring-2 focus:ring-green-100 transition text-sm">
                        </div>
                        <button type="submit" name="update_password" class="w-full bg-green-700 text-white py-4 rounded-[1.5rem] font-bold hover:bg-green-800 shadow-xl shadow-green-100 transition-all uppercase tracking-widest text-[10px]">
                            Simpan Password Baru
                        </button>

                <div class="mb-6">
    <label class="block text-sm font-bold text-slate-700 mb-2">Password Kamu</label>
    <div class="relative">
        <input 
            type="password" 
            id="old_pass_input"
            value="<?= htmlspecialchars($current_hashed_pass) ?>" 
            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-slate-800"
        >
        <button type="button" onclick="toggleView()" class="absolute right-4 top-1/2 -translate-y-1/2">
            👁️
        </button>
    </div>
</div>

            <!-- Pesan Catatan sesuai Gambar -->
            <p class="text-[11px] text-orange-500 mt-3 italic leading-relaxed">
                *Catatan: Jika Tidak Muncul Harap Refresh.
            </p>
        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <!-- SCRIPT MAGIC SPA (Wajib ada di tiap file) -->
    <script>
// 1. LETAKKAN FUNGSI INI DI LUAR (GLOBAL SCOPE)
// Supaya bisa dipanggil oleh elemen yang baru dimuat via AJAX
function toggleView() {
    const input = document.getElementById('old_pass_input');
    if (input) {
        input.type = input.type === 'password' ? 'text' : 'password';
    }
}

document.addEventListener('click', function(e) {
    const link = e.target.closest('.nav-link');
    if (!link) return;
    if (link.href.includes('logout.php')) return;

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

                // Update Menu Aktif Manual pakai Tailwind
                document.querySelectorAll('.nav-link').forEach(nav => {
                    nav.classList.remove('font-bold', 'bg-green-50', 'text-green-700');
                    nav.classList.add('font-medium', 'text-slate-400');
                    
                    if (nav.getAttribute('href').split('/').pop() === url.split('/').pop()) {
                        nav.classList.add('font-bold', 'bg-green-50', 'text-green-700');
                        nav.classList.remove('text-slate-400', 'font-medium');
                    }
                });
            }
        });
});

window.addEventListener('popstate', () => location.reload());
</script>
</body>
</html>