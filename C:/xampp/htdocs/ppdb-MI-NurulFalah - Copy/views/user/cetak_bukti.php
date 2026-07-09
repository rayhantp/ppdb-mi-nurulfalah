<?php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: ../../login.php"); exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data siswa + pendaftaran + foto
$query = "SELECT p.*, b.file_foto 
          FROM pendaftar p 
          LEFT JOIN berkas b ON p.id_pendaftar = b.id_pendaftar 
          WHERE p.id_user = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id_user]);
$d = $stmt->fetch();

if (!$d || $d['status_pendaftaran'] !== 'diterima') {
    die("<script>alert('Status belum diterima!'); window.close();</script>");
}

// Cek Foto (Jika tidak ada, pakai placeholder)
$foto_path = "../../assets/uploads/" . ($d['file_foto'] ?? 'default.jpg');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kelulusan - <?= htmlspecialchars($d['nama_siswa']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; background-color: #f8fafc; }
    
    @media print {
        /* TAMBAHKAN !important DI SINI */
        .no-print { display: none !important; }
        
        body { background: white; padding: 0; }
        .cert-card { 
            box-shadow: none !important; 
            border: 2px solid #e2e8f0 !important;
            -webkit-print-color-adjust: exact; 
        }
    }
        .bg-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%2394a3b8' fill-opacity='0.08'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="p-4 md:p-10 flex justify-center">

    <div class="cert-card relative w-full max-w-4xl bg-white rounded-[3rem] shadow-2xl overflow-hidden border-8 border-green-50 bg-pattern">
        
        <div class="bg-green-600 p-8 text-white flex justify-between items-center relative overflow-hidden">
            <div class="relative z-10">
                <div class="flex items-center space-x-4">
                    <img src="../../assets/img/logo.png" class="w-16 h-16 object-contain rounded-2xl shadow-lg">
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight">MI NURUL FALAH</h1>
                        <p class="text-sm opacity-90 font-medium">Penerimaan Peserta Didik Baru 2026/2027</p>
                    </div>
                </div>
            </div>
            <div class="text-right relative z-10">
                <div class="bg-white/20 px-4 py-2 rounded-xl backdrop-blur-sm">
                    <span class="text-[10px] font-bold uppercase tracking-widest block opacity-70">No. Pendaftaran</span>
                    <span class="text-lg font-mono font-bold"><?= $d['no_pendaftaran'] ?></span>
                </div>
            </div>
            <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full"></div>
            <div class="absolute left-1/2 -bottom-10 w-20 h-20 bg-black/5 rounded-full"></div>
        </div>

        <div class="p-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 items-center">
                
                <div class="flex flex-col items-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-green-200 rotate-6 rounded-[2rem]"></div>
                        <div class="relative w-48 h-60 bg-slate-100 rounded-[2rem] overflow-hidden border-4 border-white shadow-xl">
                            <img src="<?= $foto_path ?>" class="w-full h-full object-cover">
                        </div>
                    </div>
                    <div class="mt-6 text-center">
                        <span class="bg-green-100 text-green-700 px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-wider">
                            Foto Calon Siswa
                        </span>
                    </div>
                </div>

                <div class="md:col-span-2 space-y-6">
                    <div class="border-b-2 border-slate-50 pb-4">
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Nama Lengkap</h2>
                        <p class="text-3xl font-bold text-slate-800 leading-tight"><?= strtoupper($d['nama_siswa']) ?></p>
                    </div>

                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <h2 class="text-[10px] font-bold text-slate-400 uppercase mb-1">NISN</h2>
                            <p class="text-lg font-bold text-slate-700 font-mono"><?= $d['nisn'] ?></p>
                        </div>
                        <div>
                            <h2 class="text-[10px] font-bold text-slate-400 uppercase mb-1">Tempat, Tgl Lahir</h2>
                            <p class="font-bold text-slate-700"><?= $d['tempat_lahir'] ?>, <?= date('d/m/Y', strtotime($d['tanggal_lahir'])) ?></p>
                        </div>
                        <div class="col-span-2">
                            <h2 class="text-[10px] font-bold text-slate-400 uppercase mb-1">Asal Sekolah</h2>
                            <p class="font-bold text-slate-700 text-lg"><?= $d['asal_sekolah'] ?></p>
                        </div>
                    </div>

                    <div class="bg-green-50 border-2 border-green-100 p-6 rounded-[2rem] flex items-center space-x-6 relative overflow-hidden">
                        <div class="text-4xl">🎉</div>
                        <div>
                            <p class="text-[10px] font-bold text-green-600 uppercase tracking-widest mb-1">Hasil Seleksi</p>
                            <p class="text-2xl font-bold text-green-900 leading-none">DINYATAKAN DITERIMA</p>
                        </div>
                        <div class="absolute right-4 top-4 text-green-200">✨</div>
                    </div>
                </div>
            </div>

            <div class="mt-12 flex justify-between items-end border-t-2 border-slate-50 pt-8">
                <div class="max-w-[250px]">
                    <p class="text-[10px] text-slate-400 font-medium italic">
                        * Harap membawa kartu ini saat melakukan daftar ulang ke sekolah. Syarat dan ketentuan berlaku sesuai kebijakan MI Nurul Falah.
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-xs font-bold text-slate-400 mb-16">Panitia PPDB MI Nurul Falah,</p>
                    <div class="h-1 bg-slate-800 w-full mb-1"></div>
                    <p class="text-sm font-bold uppercase tracking-widest italic">PINTU MASA DEPAN</p>
                </div>
            </div>
        </div>

        <div class="h-4 bg-gradient-to-r from-green-400 via-yellow-300 to-green-600"></div>
    </div>

    <div class="no-print fixed bottom-8 flex space-x-4">
        <button onclick="window.print()" class="bg-green-700 text-white px-10 py-4 rounded-2xl font-bold shadow-2xl hover:bg-green-800 transition transform hover:-translate-y-1">
            🖨️ Cetak Kartu Keren
        </button>
        <button onclick="window.close()" class="bg-white text-slate-700 border border-slate-200 px-10 py-4 rounded-2xl font-bold shadow-lg hover:bg-slate-50 transition">
            Tutup
        </button>
    </div>

</body>
</html>