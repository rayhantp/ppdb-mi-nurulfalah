<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$message = "";
$error = "";

// 1. AMBIL ID PENDAFTAR (Ganti tabel dari 'siswa' ke 'pendaftar')
$stmt = $pdo->prepare("SELECT id_pendaftar FROM pendaftar WHERE id_user = ?");
$stmt->execute([$id_user]);
$pendaftar = $stmt->fetch();

if (!$pendaftar) {
    $error = "Silakan isi biodata/formulir terlebih dahulu sebelum mengunggah berkas.";
    $id_pendaftar = null;
} else {
    $id_pendaftar = $pendaftar['id_pendaftar'];
}

// 2. LOGIKA UPLOAD
if (isset($_POST['upload_berkas']) && $id_pendaftar !== null) {
    $ekstensi_pdf = ['pdf'];
    $foto_diizinkan = ['pdf', 'jpg', 'jpeg', 'png']; 
    $valid = true;

    // Cek apakah data berkas pendaftar sudah ada di tabel 'berkas'
    $stmt_cek = $pdo->prepare("SELECT id_berkas FROM berkas WHERE id_pendaftar = ?");
    $stmt_cek->execute([$id_pendaftar]);
    $berkas_exists = $stmt_cek->fetch();

    $folder_upload = "../../uploads/berkas/";
    if (!is_dir($folder_upload)) mkdir($folder_upload, 0777, true);

    $semua_input = ['file_kk', 'file_akta', 'file_ktp', 'file_ijazah', 'file_nisn', 'file_foto'];
    $files_to_update = [];

    foreach ($semua_input as $input_name) {
        if (!empty($_FILES[$input_name]['name'])) {
            $nama_asli = $_FILES[$input_name]['name'];
            $ekstensi = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
            
            // Validasi format
            if ($input_name == 'file_foto') {
                if (!in_array($ekstensi, $foto_diizinkan)) { $valid = false; break; }
            } else {
                if (!in_array($ekstensi, $ekstensi_pdf)) { $valid = false; break; }
            }

            $prefix = strtoupper(str_replace('file_', '', $input_name));
            $nama_file_baru = $prefix . "_" . $id_pendaftar . "_" . time() . "." . $ekstensi;
            
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $folder_upload . $nama_file_baru)) {
                $files_to_update[$input_name] = $nama_file_baru;
            }
        }
    }

    if ($valid && !empty($files_to_update)) {
        try {
            if ($berkas_exists) {
                // UPDATE jika sudah ada
                $set_clause = [];
                $values = [];
                foreach ($files_to_update as $col => $val) {
                    $set_clause[] = "$col = ?";
                    $values[] = $val;
                }
                $values[] = $id_pendaftar;
                $sql = "UPDATE berkas SET " . implode(", ", $set_clause) . " WHERE id_pendaftar = ?";
                $pdo->prepare($sql)->execute($values);
            } else {
                // INSERT jika belum ada
                $cols = implode(", ", array_keys($files_to_update)) . ", id_pendaftar";
                $placeholders = str_repeat("?, ", count($files_to_update)) . "?";
                $values = array_values($files_to_update);
                $values[] = $id_pendaftar;
                $sql = "INSERT INTO berkas ($cols) VALUES ($placeholders)";
                $pdo->prepare($sql)->execute($values);
            }
            $message = "Berhasil! Berkas telah tersimpan.";
        } catch (PDOException $e) {
            $error = "Gagal menyimpan ke database: " . $e->getMessage();
        }
    } else {
        $error = $valid ? "Tidak ada berkas yang diupload." : "Format berkas tidak didukung.";
    }
}
// ... (Sisa HTML form tetap sama)
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Berkas - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; }
        
        /* Animasi halus saat ganti konten */
        #content-area {
            animation: fadeIn 0.3s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .active-link { 
            background-color: #f0fdf4 !important; 
            color: #15803d !important; 
            font-weight: 700;
        }
    </style>
</head>
<body class="bg-[#FBFCFA] text-slate-700">

    <div class="min-h-screen flex flex-col md:flex-row">
        <div class="flex flex-col md:flex-row min-h-screen bg-slate-50">

    <?php include 'sidebar_siswa.php'; ?>

    <main class="flex-1 p-8 md:p-12 overflow-y-auto">
        </main>
</div>

        <main class="flex-1 p-8 md:p-12 overflow-y-auto">
    <main id="content-area" class="flex-1 p-8 md:p-12 overflow-y-auto">
        <header class="mb-10">
            <h2 class="text-2xl font-bold text-green-900">Upload Dokumen Pendukung</h2>
            <p class="text-sm text-slate-400 mt-1">Lengkapi berkas pendaftaran sesuai persyaratan sekolah (Format wajib PDF).</p>
        </header>

        <?php if (!empty($error)): ?>
            <div class="mb-8 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 text-sm font-bold">
                ❌ <?= $error; ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($message)): ?>
            <div class="mb-8 p-4 bg-green-50 text-green-700 rounded-2xl border border-green-100 text-sm font-bold animate-pulse">
                ✨ <?= $message; ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="max-w-4xl grid md:grid-cols-2 gap-6">
            
            <div class="space-y-6">
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">1. Scan Kartu Keluarga (PDF)</span>
                        <input type="file" name="file_kk" accept="application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 font-semibold" required>
                    </label>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">2. Scan Akta Kelahiran (PDF)</span>
                        <input type="file" name="file_akta" accept="application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 font-semibold" required>
                    </label>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">3. Scan KTP Orang Tua (PDF)</span>
                        <input type="file" name="file_ktp" accept="application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-green-50 file:text-green-700 font-semibold" required>
                    </label>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">4. Pas Foto Digital (3x4)</span>
                        <input type="file" name="file_foto" accept="image/jpeg, image/png, application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 font-semibold" required>
                    </label>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm border-dashed">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest italic">5. Ijazah TK / RA (Opsional PDF)</span>
                        <input type="file" name="file_ijazah" accept="application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-slate-50 file:text-slate-600 font-semibold">
                    </label>
                </div>

                <div class="bg-white p-6 rounded-[2rem] border border-slate-100 shadow-sm border-dashed">
                    <label class="block cursor-pointer text-center">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-widest italic">6. Scan NISN (Opsional PDF)</span>
                        <input type="file" name="file_nisn" accept="application/pdf" class="mt-4 block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-slate-50 file:text-slate-600 font-semibold">
                    </label>
                </div>
            </div>

                <div class="md:col-span-2">
                    <button type="submit" name="upload_berkas" class="w-full bg-green-700 text-white py-5 rounded-[2rem] font-bold hover:bg-green-800 shadow-xl shadow-green-100 transition-all uppercase tracking-widest text-xs">
                        Simpan & Unggah Semua Berkas
                    </button>
                </div>
            </form>
        </main>
    </div>

<script>
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
                // 1. Ganti isi konten tengah
                document.querySelector('#content-area').innerHTML = newContent.innerHTML;
                
                // 2. Ganti URL di browser
                history.pushState(null, '', url);

                // 3. LOGIKA UPDATE WARNA SIDEBAR
                document.querySelectorAll('.nav-link').forEach(nav => {
                    // Reset semua ke warna abu-abu
                    nav.classList.remove('active-link', 'bg-green-50', 'text-green-700', 'font-bold');
                    nav.classList.add('text-slate-400');

                    // Cari link yang href-nya cocok dengan halaman baru
                    // Kita bersihkan dulu string-nya agar perbandingannya akurat
                    const navHref = nav.getAttribute('href').split('/').pop();
                    const targetUrl = url.split('/').pop();

                    if (navHref === targetUrl) {
                        nav.classList.add('active-link');
                        nav.classList.remove('text-slate-400');
                    }
                });
            }
        })
        .catch(err => {
            console.error('SPA Error:', err);
            window.location.href = url; // Fallback kalau fetch gagal
        });
});

window.addEventListener('popstate', () => location.reload());
</script>
</body>
</html>