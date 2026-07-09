<?php
session_start();
include '../../config/database.php'; 

// 🔒 SATPAM KETAT: Hanya peran 'admin' yang boleh masuk
if (!isset($_SESSION['nama_lengkap']) || $_SESSION['role'] !== 'admin') {
    echo "<script>
            alert('Akses Ditolak! Halaman ini hanya diperuntukkan bagi Administrator Utama.');
            window.location.href = 'dashboard.php';
          </script>";
    exit();
}

$current_role = $_SESSION['role'];
$user_name = $_SESSION['nama_lengkap'];
$message = "";
$error = "";

// ➕ ENGINE 1: PROSES TAMBAH USER BARU
if (isset($_POST['tambah_user'])) {
    $username_baru = trim($_POST['username'] ?? '');
    $password_baru = trim($_POST['password'] ?? '');
    $nama_baru     = trim($_POST['nama_lengkap'] ?? '');
    $role_baru     = $_POST['role'] ?? 'operator';

    if (empty($username_baru) || empty($password_baru) || empty($nama_baru)) {
        $error = "Semua kolom formulir wajib diisi!";
    } else {
        try {
            $stmt_cek = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
            $stmt_cek->execute([$username_baru]);
            
            if ($stmt_cek->fetchColumn() > 0) {
                $error = "Username '$username_baru' sudah terdaftar di sistem!";
            } else {
                $stmt_insert = $pdo->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
                $stmt_insert->execute([$username_baru, $password_baru, $nama_baru, $role_baru]);
                
                $message = "Pengguna baru berhasil ditambahkan!";
                header("Refresh:1; url=kelola_user.php"); // 🔥 Jalur disinkronkan
            }
        } catch (PDOException $e) {
            $error = "Gagal menyimpan data: " . $e->getMessage();
        }
    }
}

// ❌ ENGINE 2: PROSES HAPUS USER
if (isset($_GET['hapus_id'])) {
    $id_hapus = $_GET['hapus_id'];
    
    if ($id_hapus == $_SESSION['id_user']) {
        $error = "Aksi Ilegal! Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.";
    } else {
        try {
            $stmt_hapus = $pdo->prepare("DELETE FROM users WHERE id_user = ? OR id = ?");
            $stmt_hapus->execute([$id_hapus, $id_hapus]);
            
            $message = "Pengguna berhasil dihapus dari sistem.";
            header("Refresh:1; url=kelola_user.php"); // 🔥 Jalur disinkronkan
        } catch (PDOException $e) {
            $error = "Gagal menghapus data: " . $e->getMessage();
        }
    }
}

// 👥 ENGINE 3: AMBIL DAFTAR USER AKTIF
try {
    $stmt_all = $pdo->query("SELECT * FROM users ORDER BY role ASC, username ASC");
    $daftar_user = $stmt_all->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna Sistem - MI Nurul Falah</title>
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

    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-gradient-to-bl from-green-200/20 to-emerald-100/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-6xl mx-auto space-y-8">
                
                <header>
                    <span class="px-3 py-1 bg-green-50 border border-green-100 text-green-700 text-[10px] font-black uppercase font-mono tracking-widest rounded-lg">Sistem Inti Keamanan</span>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-800 tracking-tight mt-2">Kelola Pengguna</h2>
                    <p class="text-sm text-slate-400 font-medium mt-1">Manajemen kendali akun administrator, operator internal, kepala sekolah, dan akun akses siswa.</p>
                </header>

                <?php if ($message): ?>
                    <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 font-bold text-xs rounded-2xl text-center shadow-sm"><?= $message ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="p-4 bg-red-50 border border-red-100 text-red-700 font-bold text-xs rounded-2xl text-center shadow-sm">⚠️ <?= $error ?></div>
                <?php endif; ?>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                    <div class="bg-white p-6 sm:p-8 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-black text-slate-800 tracking-tight">Tambah Akun Baru</h3>
                            <p class="text-[11px] text-slate-400 font-medium mt-0.5">Daftarkan operator pelaksana baru.</p>
                        </div>
                        
                        <form action="" method="POST" class="space-y-4">
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 font-mono mb-2">Nama Lengkap Personal</label>
                                <input type="text" name="nama_lengkap" placeholder="Contoh: Ahmad Subagja, S.Pd" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold outline-none focus:border-green-700 focus:bg-white transition-all" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 font-mono mb-2">Username Akses</label>
                                <input type="text" name="username" placeholder="Huruf kecil tanpa spasi" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold outline-none focus:border-green-700 focus:bg-white transition-all font-mono" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 font-mono mb-2">Password</label>
                                <input type="text" name="password" placeholder="Masukkan password" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-semibold outline-none focus:border-green-700 focus:bg-white transition-all" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase tracking-wider text-slate-400 font-mono mb-2">Hak Akses Peran</label>
                                <select name="role" class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-bold outline-none focus:border-green-700 focus:bg-white transition-all">
                                    <option value="operator">Operator (Panitia PPDB)</option>
                                    <option value="kepala_sekolah">Kepala Sekolah</option>
                                    <option value="admin">Administrator</option>
                                    <option value="siswa">Siswa</option>
                                </select>
                            </div>
                            <div class="pt-2">
                                <button type="submit" name="tambah_user" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg transition-all active:scale-95">
                                    💾 Daftarkan Akun Resmi
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="lg:col-span-2 bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h3 class="text-base font-black text-slate-800 tracking-tight">Otoritas Kredensial</h3>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Total terdaftar: <span class="font-bold text-green-700"><?= count($daftar_user); ?> Pengguna</span></p>
                            </div>
                            <input type="text" id="userSearch" placeholder="Cari nama..." class="px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs outline-none focus:border-green-700 font-semibold w-full sm:w-56">
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[9px] uppercase tracking-[0.25em] text-slate-400 border-b border-slate-100 bg-slate-50/40 font-mono">
                                        <th class="px-8 py-4">Nama Lengkap</th>
                                        <th class="px-8 py-4">Username</th>
                                        <th class="px-8 py-4">Password</th>
                                        <th class="px-8 py-4">Hak Akses</th>
                                        <th class="px-8 py-4 text-right">Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody id="tabelUserBody" class="divide-y divide-slate-100 text-xs">
                                    <?php foreach ($daftar_user as $u): ?>
                                    <tr class="user-row hover:bg-green-50/10 transition-all duration-200">
                                        <td class="px-8 py-4 font-black text-slate-800 class-nama-user"><?= htmlspecialchars($u['nama_lengkap'] ?? $u['nama'] ?? 'User') ?></td>
                                        <td class="px-8 py-4 font-bold font-mono text-slate-500 class-username-user"><?= htmlspecialchars($u['username']) ?></td>
                                        <td class="px-8 py-4 font-mono text-slate-400 text-[10px] truncate max-w-[120px]"><?= htmlspecialchars($u['password'] ?? 'Encrypted') ?></td>
                                        <td class="px-8 py-4">
                                            <?php 
                                            $role = $u['role'] ?? 'operator';
                                            $badge = "bg-slate-50 text-slate-500 border-slate-100";
                                            if ($role === 'admin') $badge = "bg-red-50 text-red-600 border-red-100";
                                            elseif ($role === 'operator') $badge = "bg-blue-50 text-blue-600 border-blue-100";
                                            elseif ($role === 'kepala_sekolah') $badge = "bg-purple-50 text-purple-600 border-purple-100";
                                            elseif ($role === 'siswa') $badge = "bg-emerald-50 text-emerald-600 border-emerald-100";
                                            ?>
                                            <span class="px-2.5 py-1 border text-[9px] font-black uppercase font-mono tracking-wider rounded-lg <?= $badge ?>"><?= str_replace('_', ' ', $role) ?></span>
                                        </td>
                                        <td class="px-8 py-4 text-right">
                                            <?php if (($u['id_user'] ?? $u['id']) != $_SESSION['id_user']): ?>
                                                <a href="kelola_user.php?hapus_id=<?= $u['id_user'] ?? $u['id'] ?>" onclick="return confirm('Hapus akun?')" class="text-red-500 hover:text-red-700 font-bold uppercase text-[9px] tracking-wider bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition-all border border-red-100/50">Hapus</a>
                                            <?php else: ?>
                                                <span class="text-slate-300 font-mono text-[10px] italic">Aktif (Anda)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

<script>
document.getElementById('userSearch')?.addEventListener('input', function(e) {
    const keyword = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    rows.forEach(row => {
        const nama = row.querySelector('.class-nama-user').textContent.toLowerCase();
        const username = row.querySelector('.class-username-user').textContent.toLowerCase();
        if (nama.includes(keyword) || username.includes(keyword)) row.style.display = '';
        else row.style.display = 'none';
    });
});
document.querySelectorAll('.nav-link').forEach(nav => {
    if(nav.getAttribute('href') === window.location.pathname.split('/').pop()) nav.classList.add('active-link');
});
</script>
</body>
</html>