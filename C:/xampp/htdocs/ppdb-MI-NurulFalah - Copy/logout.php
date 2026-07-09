<?php
session_start();
// Hapus semua session
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&display=swap" rel="stylesheet">
    <title>Logging Out...</title>
    <style>
        body { font-family: 'Quicksand', sans-serif; }
    </style>
</head>
<body class="bg-slate-50">

    <div class="fixed inset-0 flex items-center justify-center bg-white/90 backdrop-blur-xl">
        <div class="text-center">
            <div class="relative inline-block mb-8">
                <div class="absolute inset-0 bg-green-200 rounded-full blur-3xl opacity-40 animate-pulse"></div>
                <img src="assets/img/logo.png" alt="Logo" class="relative w-28 h-28 object-contain">
            </div>
            
            <h2 class="text-green-900 font-bold text-2xl tracking-tight">Berhasil Keluar</h2>
            <p class="text-slate-400 text-sm mt-3">Mengalihkan Anda kembali ke Beranda Utama...</p>
            
            <div class="mt-10 flex justify-center">
                <div class="w-10 h-10 border-4 border-green-50 border-t-green-600 rounded-full animate-spin"></div>
            </div>
        </div>
    </div>

    <script>
        setTimeout(function() {
            window.location.href = "index.php"; 
        }, 2500);
    </script>
</body>
</html>