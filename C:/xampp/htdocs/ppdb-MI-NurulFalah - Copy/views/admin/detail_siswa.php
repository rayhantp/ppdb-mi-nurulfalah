<?php
session_start();
// Memanggil file konfigurasi database (Menggunakan variabel $pdo Anda yang sudah fixed)
include '../../config/database.php'; 

if (!isset($_SESSION['nama_lengkap'])) {
    header("Location: login.php");
    exit();
}

// 💡 DYNAMIC ROLE CAPABILITY: Membaca hak akses user yang sedang login dari session
$current_role = $_SESSION['role'] ?? 'operator';

$id_siswa = $_GET['id'] ?? null;
if (!$id_siswa) {
    header("Location: pendaftar.php");
    exit();
}

$message = "";

// Daftar rujukan kolom dokumen berkas di database (Utuh)
$list_dokumen = [
    'file_kk' => '📄 Scan Kartu Keluarga',
    'file_akta' => '📄 Scan Akta Kelahiran',
    'file_ktp' => '📄 Scan KTP Orang Tua',
    'file_ijazah' => '🎓 Scan Ijazah TK / RA (Opsional)',
    'file_nisn' => '🔢 Scan Lembar NISN (Opsional)'
];

// Ambil data awal siswa untuk pengecekan file lama
try {
    $stmt = $pdo->prepare("SELECT p.*, b.file_kk, b.file_akta, b.file_ktp, b.file_ijazah, b.file_nisn, b.file_foto FROM pendaftar p LEFT JOIN berkas b ON p.id_pendaftar = b.id_pendaftar WHERE p.id_pendaftar = :id_siswa");
    $stmt->execute([':id_siswa' => $id_siswa]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$siswa) { header("Location: pendaftar.php"); exit(); }
    $status = $siswa['status_pendaftaran'];
} catch (PDOException $e) {
    die("Error Database: " . $e->getMessage());
}

// ─── 🛠️ ENGINES INTERNAL CRUD BERKAS & BIODATA WITH MULTIROLE PROTECTION ───
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 💡 PROTEKSI BERLAPIS 1: Jika role adalah Kepala Sekolah, blokir total seluruh manipulasi data (Read-Only)
    if ($current_role === 'kepala_sekolah') {
        $message = "🔴 Akses ditolak! Akun Kepala Sekolah hanya diizinkan untuk meninjau data (Read-Only).";
    } else {
        
        // FIX AKSYON 1: PROSES HAPUS BERKAS SPESIFIK (DELETE FILE) - Admin & Operator Diizinkan
        if (isset($_POST['delete_specific_file'])) {
            $file_key = $_POST['delete_specific_file'];
            
            if (array_key_exists($file_key, $list_dokumen)) {
                try {
                    $uploadDir = '../../uploads/berkas/';
                    // Hapus file fisik dari penyimpanan lokal komputer/XAMPP
                    if (!empty($siswa[$file_key]) && file_exists($uploadDir . $siswa[$file_key])) {
                        unlink($uploadDir . $siswa[$file_key]);
                    }
                    
                    // Kosongkan nama file di kolom database
                    $stmtDelFile = $pdo->prepare("UPDATE berkas SET $file_key = NULL WHERE id_pendaftar = :id_siswa");
                    $stmtDelFile->execute([':id_siswa' => $id_siswa]);
                    
                    $message = "Berkas " . $list_dokumen[$file_key] . " berhasil dihapus secara permanen!";
                } catch (PDOException $e) {
                    $message = "Gagal menghapus berkas: " . $e->getMessage();
                }
            }
        }
        
        // FIX AKSYON 2: PROSES SIMPAN PERUBAHAN BIODATA & UPLOAD FILE BARU (UPDATE)
        $action_type = $_POST['action_type'] ?? 'update';
        if ($action_type === 'update' && !isset($_POST['delete_specific_file'])) {
            $nama_siswa = $_POST['nama_siswa'];
            $nisn = $_POST['nisn'];
            $no_pendaftaran = $_POST['no_pendaftaran'];
            
            // 💡 PROTEKSI BERLAPIS 2: Amankan kolom kelulusan seleksi berdasarkan kriteria role pendaftar
            if ($current_role === 'operator') {
                // Jika akun operator yang kirim form, paksa status pendaftaran tetap memakai nilai lama dari DB
                $status_pendaftaran = $siswa['status_pendaftaran']; 
            } else {
                // Jika admin yang kirim form, izinkan mengambil nilai pilihan baru dari dropdown select HTML
                $status_pendaftaran = $_POST['status_pendaftaran']; 
            }

            try {
                // Update data tekstual biodata pendaftar ke dalam database
                $sql = "UPDATE pendaftar SET 
                            nama_siswa = :nama_siswa, 
                            nisn = :nisn, 
                            no_pendaftaran = :no_pendaftaran, 
                            status_pendaftaran = :status_pendaftaran 
                        WHERE id_pendaftar = :id_siswa";
                
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    ':nama_siswa' => $nama_siswa,
                    ':nisn' => $nisn,
                    ':no_pendaftaran' => $no_pendaftaran,
                    ':status_pendaftaran' => $status_pendaftaran,
                    ':id_siswa' => $id_siswa
                ]);

                // Looping pengecekan apakah ada file baru PDF yang diunggah
                $uploadDir = '../../uploads/berkas/';
                foreach ($list_dokumen as $key => $label) {
                    if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                        $fileTmpPath = $_FILES[$key]['tmp_name'];
                        $fileName = $_FILES[$key]['name'];
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        
                        // Validasi ekstensi wajib PDF sesuai aturan Bab IV berkas skripsi
                        if ($fileExt === 'pdf') {
                            $newFileName = $key . '_' . $id_siswa . '_' . time() . '.pdf';
                            $dest_path = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                // Hapus file lama agar penyimpanan komputer tidak penuh berkas duplikat
                                if (!empty($siswa[$key]) && file_exists($uploadDir . $siswa[$key])) {
                                    unlink($uploadDir . $siswa[$key]);
                                }
                                // Tulis nama file baru ke database
                                $stmtFile = $pdo->prepare("UPDATE berkas SET $key = :new_file WHERE id_pendaftar = :id_siswa");
                                $stmtFile->execute([':new_file' => $newFileName, ':id_siswa' => $id_siswa]);
                                if ($stmtFile->rowCount() == 0) {
                                    $pdo->prepare("INSERT INTO berkas (id_pendaftar, $key) VALUES (:id_siswa, :new_file)")->execute([':id_siswa' => $id_siswa, ':new_file' => $newFileName]);
                                }
                            }
                        }
                    }
                }
                
                $message = "Seluruh pembaruan data biodata dan berkas dokumen berhasil disimpan!";
            } catch (PDOException $e) {
                $message = "Gagal memperbarui data: " . $e->getMessage();
            }
        } 
        
        // FIX AKSYON 3: PROSES HAPUS SELURUH SISWA (DELETE TOTAL)
        if ($action_type === 'delete') {
            // 💡 PROTEKSI BERLAPIS 3: Tolak keras aksi hapus records pendaftar jika pelakunya bukan Administrator utama
            if ($current_role !== 'admin') {
                $message = "🔴 Akses ditolak! Hanya Administrator Utama yang diizinkan untuk menghapus data pendaftar.";
            } else {
                try {
                    $sql = "DELETE FROM pendaftar WHERE id_pendaftar = :id_siswa";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([':id_siswa' => $id_siswa]);
                    
                    echo "<script>
                            alert('Data pendaftar telah berhasil dihapus dari sistem!');
                            window.location.href = 'pendaftar.php';
                          </script>";
                    exit();
                } catch (PDOException $e) {
                    $message = "Gagal menghapus data: " . $e->getMessage();
                }
            }
        }
        
    } // Akhir batas filter proteksi hak akses non-kepala_sekolah

    // Refresh ulang data siswa pasca operasi eksekusi agar input ter-update otomatis di halaman web
    $stmt = $pdo->prepare("SELECT p.*, b.file_kk, b.file_akta, b.file_ktp, b.file_ijazah, b.file_nisn, b.file_foto FROM pendaftar p LEFT JOIN berkas b ON p.id_pendaftar = b.id_pendaftar WHERE p.id_pendaftar = :id_siswa");
    $stmt->execute([':id_siswa' => $id_siswa]);
    $siswa = $stmt->fetch(PDO::FETCH_ASSOC);
    $status = $siswa['status_pendaftaran'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Manajemen Berkas - <?= htmlspecialchars($siswa['nama_siswa']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        html { font-size: 14px; }
        
        #content-area { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }

        @keyframes gridMove { 0% { background-position: 0 0; } 100% { background-position: 4rem 4rem; } }
        .moving-grid-bg { animation: gridMove 30s linear infinite; }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">

    <div class="absolute top-0 right-0 w-[550px] h-[550px] bg-gradient-to-bl from-green-200/20 to-emerald-100/10 rounded-full blur-[140px] pointer-events-none -z-10"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-[0.25] pointer-events-none -z-10 moving-grid-bg"></div>

    <div class="flex flex-col md:flex-row min-h-screen bg-slate-50/30">
        
        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            <div class="max-w-5xl mx-auto">
                
                <div class="mb-8">
                    <a href="pendaftar.php" class="inline-flex items-center text-xs font-black uppercase tracking-widest text-slate-400 hover:text-green-700 transition-all duration-300 gap-2 group">
                        <span class="transform transition-transform group-hover:-translate-x-1 font-mono text-sm">←</span> 
                        <span>Kembali ke Database Pendaftar</span>
                    </a>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" onsubmit="return confirmAction(this);" class="space-y-8">
                    
                    <header class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 pb-8 border-b border-slate-200/60">
                        <div class="flex items-center gap-5">
                            <div class="w-20 h-20 rounded-[1.5rem] border-4 border-white shadow-xl flex-shrink-0 relative overflow-hidden bg-slate-100 ring-4 ring-green-600/20">
                                <?php if (!empty($siswa['file_foto'])): ?>
                                    <img src="../../uploads/berkas/<?= htmlspecialchars($siswa['file_foto']) ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-green-50 to-emerald-100 text-green-700 font-black text-2xl uppercase">
                                        <?= substr(htmlspecialchars($siswa['nama_siswa']), 0, 1); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="flex flex-col">
                                <h2 class="text-2xl lg:text-3xl font-black text-slate-800 tracking-tight">Validasi Berkas</h2>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Kelola modifikasi data siswa, pembaruan lampiran, atau eksekusi sidang keputusan pendaftaran.</p>
                            </div>
                        </div>

                        <div>
                            <?php 
                            $badgeStyle = ($status == 'diterima') ? 'bg-emerald-50 text-emerald-600 border-emerald-100/60' : (($status == 'ditolak') ? 'bg-red-50 text-red-600 border-red-100/60' : 'bg-amber-50 text-amber-600 border-amber-100/60');
                            ?>
                            <span class="px-5 py-2.5 border rounded-full text-[10px] font-black uppercase tracking-widest font-mono shadow-sm flex items-center gap-2 <?= $badgeStyle; ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($status=='diterima')?'bg-emerald-500':(($status=='ditolak')?'bg-red-500':'bg-amber-500') ?> animate-pulse"></span>
                                Status Saat Ini: <?= $status ?>
                            </span>
                        </div>
                    </header>

                    <?php if ($message): ?>
                        <div class="p-4 bg-emerald-50 text-emerald-700 border border-emerald-100/80 rounded-2xl text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-2">
                            <span>✨</span> <span><?= $message ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
                        
                        <div class="md:col-span-2 space-y-6">
                            
                            <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 p-8 sm:p-10 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.01)] space-y-6">
                                <h3 class="text-lg font-black text-slate-800 tracking-tight border-b border-slate-100 pb-3">⚙️ Modifikasi Informasi Biodata</h3>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Nama Lengkap Calon Siswa</label>
                                        <input type="text" name="nama_siswa" value="<?= htmlspecialchars($siswa['nama_siswa']) ?>" <?= ($current_role === 'kepala_sekolah') ? 'readonly' : '' ?> class="w-full px-4 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl text-sm font-semibold focus:outline-none focus:border-green-600 focus:ring-2 focus:ring-green-500/10 transition-all readonly:opacity-70 readonly:cursor-not-allowed">
                                    </div>
                                    <div class="space-y-2">
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Nomor NISN</label>
                                        <input type="text" name="nisn" value="<?= htmlspecialchars($siswa['nisn'] ?? '') ?>" <?= ($current_role === 'kepala_sekolah') ? 'readonly' : '' ?> class="w-full px-4 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl text-sm font-mono font-semibold focus:outline-none focus:border-green-600 focus:ring-2 focus:ring-green-500/10 transition-all readonly:opacity-70 readonly:cursor-not-allowed">
                                    </div>
                                    <div class="space-y-2 sm:col-span-2">
                                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Nomor Registrasi Pendaftaran</label>
                                        <input type="text" name="no_pendaftaran" value="<?= htmlspecialchars($siswa['no_pendaftaran']) ?>" <?= ($current_role === 'kepala_sekolah') ? 'readonly' : '' ?> class="w-full px-4 py-3.5 bg-slate-50/80 border border-slate-200 rounded-2xl text-sm font-mono font-semibold focus:outline-none focus:border-green-600 focus:ring-2 focus:ring-green-500/10 transition-all readonly:opacity-70 readonly:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.01)] p-8 sm:p-10">
                                <h3 class="text-lg font-black text-slate-800 tracking-tight mb-2 border-b border-slate-100 pb-3">📄 Berkas Lampiran Digital</h3>
                                <p class="text-xs text-slate-400 font-medium mb-8">Silakan periksa dokumen, unggah berkas baru untuk memperbarui, atau hapus lampiran.</p>
                                
                                <div class="divide-y divide-slate-100 text-sm">
                                    <?php 
                                    foreach ($list_dokumen as $key => $label): 
                                        $nama_file = $siswa[$key];
                                    ?>
                                        <div class="py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 group/row hover:bg-slate-50/50 transition-all duration-300 rounded-xl px-2 -mx-2">
                                            
                                            <div class="flex flex-col flex-1">
                                                <span class="font-bold text-slate-600 group-hover/row:text-slate-800 transition-colors"><?= $label ?></span>
                                                <?php if ($current_role !== 'kepala_sekolah'): ?>
                                                    <input type="file" name="<?= $key ?>" accept=".pdf" class="text-xs text-slate-400 mt-2 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer transition-all">
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="flex items-center gap-2 self-end sm:self-center">
                                                <?php if (!empty($nama_file)): ?>
                                                    <a href="../../uploads/berkas/<?= htmlspecialchars($nama_file) ?>" target="_blank" class="inline-flex items-center text-[10px] font-black uppercase tracking-widest text-green-700 bg-green-50 border border-green-100/50 hover:bg-green-700 hover:text-white px-4 py-2.5 rounded-xl transition-all duration-300 shadow-sm font-mono">
                                                        Buka PDF ↗
                                                    </a>
                                                    
                                                    <?php if ($current_role !== 'kepala_sekolah'): ?>
                                                        <button type="submit" name="delete_specific_file" value="<?= $key ?>" onclick="return confirm('⚠️ KONFIRMASI BERKAS:\nApakah Anda yakin ingin menghapus file dokumen ini dari server?')" class="p-2.5 text-red-500 bg-red-50 border border-red-100/70 rounded-xl hover:bg-red-600 hover:text-white transition-all duration-300 flex items-center justify-center shadow-sm" title="Hapus Berkas Ini">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-[9px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 border border-slate-200/40 px-3 py-2 rounded-xl font-mono">Kosong</span>
                                                <?php endif; ?>
                                            </div>
                                            
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div> <div class="space-y-6">
                            <div class="bg-white/90 backdrop-blur-md rounded-[3rem] border border-slate-100 p-8 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.01)] relative overflow-hidden h-fit space-y-6">
                                <div class="absolute top-0 left-0 right-0 h-[4px] bg-gradient-to-r from-green-700 to-emerald-600"></div>
                                
                                <div>
                                    <h3 class="text-base font-black text-slate-800 tracking-tight mb-1">Aksi Manajemen</h3>
                                    <p class="text-xs text-slate-400 font-medium">Eksekusi penyimpanan perubahan data atau hapus akun pendaftar secara permanen.</p>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 font-mono">Pilih Status Hasil Seleksi</label>
                                    
                                    <select name="status_pendaftaran" <?= ($current_role !== 'admin') ? 'disabled' : ''; ?> class="w-full px-4 py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-black uppercase tracking-wider text-slate-700 focus:outline-none focus:border-green-600 transition-all font-mono cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed">
                                        <option value="proses" <?= ($status == 'proses' || $status == 'pending') ? 'selected' : '' ?>>🟡 PROSES / PENDING</option>
                                        <option value="diterima" <?= ($status == 'diterima') ? 'selected' : '' ?>>🟢 DITERIMA / LULUS</option>
                                        <option value="ditolak" <?= ($status == 'ditolak') ? 'selected' : '' ?>>🔴 TIDAK DITERIMA</option>
                                    </select>
                                    
                                    <?php if ($current_role === 'operator'): ?>
                                        <span class="text-[9px] text-amber-600 font-bold block mt-1.5">⚠️ Akun Operator tidak memiliki hak merubah keputusan status kelulusan.</span>
                                    <?php elseif ($current_role === 'kepala_sekolah'): ?>
                                        <span class="text-[9px] text-red-600 font-bold block mt-1.5">🔒 Akun Kepala Sekolah bersifat peninjauan penuh (Read-Only).</span>
                                    <?php endif; ?>
                                </div>
                                
                                <input type="hidden" name="action_type" id="action_type" value="update">

                                <div class="space-y-3 pt-4 border-t border-slate-100">
                                    <?php if ($current_role !== 'kepala_sekolah'): ?>
                                        <button type="submit" onclick="document.getElementById('action_type').value='update'" class="w-full bg-slate-900 text-white py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-green-700 transition-all duration-300 shadow-md transform hover:translate-y-[-2px]">
                                            💾 Simpan Perubahan
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if ($current_role === 'admin'): ?>
                                        <button type="submit" onclick="document.getElementById('action_type').value='delete'" class="w-full bg-red-50 border border-red-200 text-red-600 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-red-600 hover:text-white transition-all duration-300 shadow-sm transform hover:translate-y-[-2px]">
                                            🗑️ Hapus Pendaftar
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div> </div> </form>
            </div>
        </main>
    </div>

    <script>
    function confirmAction(form) {
        const action = document.getElementById('action_type').value;
        if (action === 'delete') {
            return confirm('⚠️ PERINGATAN MUTLAK:\nApakah Anda yakin ingin menghapus data calon siswa ini secara permanen dari sistem basis data? Tindakan ini tidak dapat dibatalkan.');
        }
        return true; 
    }
    </script>
</body>
</html>