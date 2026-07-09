<?php
session_start();
include '../../config/database.php';

$role_diizinkan = ['admin', 'kepala_sekolah', 'operator'];
if (!isset($_SESSION['nama_lengkap']) || !in_array($_SESSION['role'], $role_diizinkan)) {
    header("Location: ../../login.php");
    exit();
}

$current_role = $_SESSION['role'];
$user_name = $_SESSION['nama_lengkap'];

// --- DUMMY DATA SEEDER ---
if (isset($_GET['seed']) && $_GET['seed'] == '1') {
    // Generate dummy data for the last 12 months
    for ($i = 11; $i >= 0; $i--) {
        $month = date('Y-m', strtotime("-$i months"));
        $jumlah = rand(10, 60); // random enrollments
        for ($j = 0; $j < $jumlah; $j++) {
            $tgl = $month . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT) . ' ' . rand(10, 15) . ':00:00';
            $nama = "Siswa Dummy " . rand(1000, 9999);
            $stmt = $pdo->prepare("INSERT INTO pendaftar (nama_siswa, tgl_daftar, status_pendaftaran) VALUES (?, ?, 'diterima')");
            $stmt->execute([$nama, $tgl]);
        }
    }
    header("Location: analisis_tren.php");
    exit();
}

if (isset($_GET['clear_seed']) && $_GET['clear_seed'] == '1') {
    $pdo->query("DELETE FROM pendaftar WHERE nama_siswa LIKE 'Siswa Dummy%'");
    header("Location: analisis_tren.php");
    exit();
}
// -------------------------

// FETCH DATA PER BULAN
$query = "SELECT DATE_FORMAT(tgl_daftar, '%Y-%m') as bulan, COUNT(*) as total 
          FROM pendaftar 
          WHERE tgl_daftar IS NOT NULL
          GROUP BY DATE_FORMAT(tgl_daftar, '%Y-%m') 
          ORDER BY bulan ASC";
$stmt = $pdo->query($query);
$data_mentah = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Jika kosong, siapkan array default
if (empty($data_mentah)) {
    $data_mentah = [];
}

$labels = [];
$actual_data = [];
$growth_rate = [];
$moving_average = [];

$n_ma = 3; // Periode Moving Average (3 Bulan)

for ($i = 0; $i < count($data_mentah); $i++) {
    $bulan_label = date("M Y", strtotime($data_mentah[$i]['bulan'] . "-01"));
    $labels[] = $bulan_label;
    $current_total = (float)$data_mentah[$i]['total'];
    $actual_data[] = $current_total;

    // 1. Hitung Growth Rate: ((Current - Prev) / Prev) * 100
    if ($i === 0) {
        $growth_rate[] = 0; // Bulan pertama tidak ada growth
    } else {
        $prev_total = (float)$data_mentah[$i - 1]['total'];
        if ($prev_total > 0) {
            $gr = (($current_total - $prev_total) / $prev_total) * 100;
        } else {
            $gr = $current_total > 0 ? 100 : 0;
        }
        $growth_rate[] = round($gr, 2);
    }

    // 2. Hitung Simple Moving Average (3 Periode)
    if ($i >= ($n_ma - 1)) {
        $sum = 0;
        for ($j = 0; $j < $n_ma; $j++) {
            $sum += (float)$data_mentah[$i - $j]['total'];
        }
        $ma = $sum / $n_ma;
        $moving_average[] = round($ma, 2);
    } else {
        $moving_average[] = null; // Belum cukup data untuk MA 3 periode
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Tren - MI Nurul Falah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Quicksand', sans-serif; letter-spacing: -0.01em; }
        .active-link { background-color: #f0fdf4 !important; color: #15803d !important; border-right: 4px solid #15803d; font-weight: 700; }
        #content-area { animation: fadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes gridMove { 0% { background-position: 0 0; } 100% { background-position: 4rem 4rem; } }
        .moving-grid-bg { animation: gridMove 30s linear infinite; }
    </style>
</head>
<body class="bg-[#FAFBF9] text-slate-700 min-h-screen relative overflow-x-hidden">
    <!-- Ambient Background -->
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-gradient-to-bl from-green-200/20 to-emerald-100/10 rounded-full blur-[120px] pointer-events-none -z-10"></div>
    <div class="absolute bottom-10 left-1/4 w-[400px] h-[400px] bg-blue-100/10 rounded-full blur-[100px] pointer-events-none -z-10"></div>
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#e2e8f0_1px,transparent_1px),linear-gradient(to_bottom,#e2e8f0_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_40%,#000_70%,transparent_100%)] opacity-[0.25] pointer-events-none -z-10 moving-grid-bg"></div>

    <div class="flex flex-col md:flex-row min-h-screen">
        <?php include 'sidebar_admin.php'; ?>

        <main id="content-area" class="flex-1 p-6 md:p-12 overflow-y-auto relative z-10">
            
            <header class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-10">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-black text-slate-800 tracking-tight">
                        Analisis <span class="bg-gradient-to-r from-green-700 to-emerald-600 bg-clip-text text-transparent">Tren Pendaftaran</span>
                    </h2>
                    <p class="text-sm text-slate-400 font-medium mt-1">Growth Rate & Moving Average Pendaftar MI Nurul Falah</p>
                </div>
                
            </header>

            <?php if (empty($data_mentah)): ?>
                <div class="bg-white rounded-3xl p-16 text-center border border-dashed border-slate-300 shadow-sm">
                    <span class="text-5xl mb-4 block opacity-50">📊</span>
                    <h3 class="text-xl font-bold text-slate-600">Belum Ada Data Pendaftar</h3>
                    <p class="text-slate-400 mt-2 text-sm">Gunakan tombol "Generate Data Sampel" di atas untuk melihat simulasi analisis tren.</p>
                </div>
            <?php else: ?>

                <!-- KARTU STATISTIK RINGKASAN -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full transition-transform duration-500 group-hover:scale-150"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Total Bulan Tercatat</p>
                        <p class="text-4xl font-black text-slate-800 mt-2 relative z-10"><?= count($data_mentah); ?></p>
                        <span class="absolute bottom-6 right-6 text-2xl z-10 opacity-60 group-hover:rotate-12 transition-transform">📅</span>
                    </div>

                    <?php 
                        $latest_gr = end($growth_rate); 
                        $gr_color = $latest_gr >= 0 ? 'text-emerald-600' : 'text-red-600';
                        $gr_bg = $latest_gr >= 0 ? 'bg-emerald-50' : 'bg-red-50';
                    ?>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 <?= $gr_bg ?> rounded-full transition-transform duration-500 group-hover:scale-150"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Growth Rate Bulan Terakhir</p>
                        <p class="text-4xl font-black <?= $gr_color ?> mt-2 relative z-10"><?= $latest_gr; ?>%</p>
                        <span class="absolute bottom-6 right-6 text-2xl z-10 opacity-60 group-hover:rotate-12 transition-transform"><?= $latest_gr >= 0 ? '📈' : '📉' ?></span>
                    </div>

                    <?php $latest_ma = end($moving_average); ?>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full transition-transform duration-500 group-hover:scale-150"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Moving Average (3 Bulan Terakhir)</p>
                        <p class="text-4xl font-black text-slate-800 mt-2 relative z-10"><?= $latest_ma ?? '-'; ?></p>
                        <span class="absolute bottom-6 right-6 text-2xl z-10 opacity-60 group-hover:rotate-12 transition-transform">📉</span>
                    </div>
                </div>

                <!-- AREA CHART -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                    
                    <!-- CHART MOVING AVERAGE -->
                    <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)]">
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-800">Tren Pendaftar & Moving Average (MI-3)</h3>
                            <p class="text-xs text-slate-400">Garis biru menunjukkan tren pendaftaran yang dihaluskan.</p>
                        </div>
                        <div class="relative h-72">
                            <canvas id="maChart"></canvas>
                        </div>
                    </div>

                    <!-- CHART GROWTH RATE -->
                    <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)]">
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-800">Growth Rate Bulanan (%)</h3>
                            <p class="text-xs text-slate-400">Persentase pertumbuhan pendaftar dibandingkan bulan sebelumnya.</p>
                        </div>
                        <div class="relative h-72">
                            <canvas id="grChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA LENGKAP -->
                <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                        <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Detail Perhitungan Analisis</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-[10px] uppercase tracking-[0.2em] text-slate-400 bg-slate-50 border-b border-slate-100 font-mono">
                                    <th class="px-8 py-4">Periode (Bulan)</th>
                                    <th class="px-8 py-4">Total Pendaftar (X)</th>
                                    <th class="px-8 py-4">Growth Rate (%)</th>
                                    <th class="px-8 py-4">Moving Average (3-Bulan)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <?php for ($i = 0; $i < count($labels); $i++): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-4 text-slate-600 font-bold"><?= $labels[$i] ?></td>
                                    <td class="px-8 py-4"><?= $actual_data[$i] ?> Siswa</td>
                                    <td class="px-8 py-4">
                                        <?php if ($growth_rate[$i] > 0): ?>
                                            <span class="inline-block text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg font-bold">+<?= $growth_rate[$i] ?>%</span>
                                        <?php elseif ($growth_rate[$i] < 0): ?>
                                            <span class="inline-block text-red-600 bg-red-50 px-2 py-1 rounded-lg font-bold"><?= $growth_rate[$i] ?>%</span>
                                        <?php else: ?>
                                            <span class="inline-block text-slate-400 bg-slate-50 px-2 py-1 rounded-lg font-bold">0%</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-8 py-4 font-mono text-slate-500">
                                        <?= $moving_average[$i] !== null ? number_format($moving_average[$i], 2, '.', '') : '<span class="text-slate-300 text-[10px] italic">N/A (< 3 bln)</span>' ?>
                                    </td>
                                </tr>
                                <?php endfor; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- INIT CHART.JS -->
                <script>
                    const labels = <?= json_encode($labels) ?>;
                    const actualData = <?= json_encode($actual_data) ?>;
                    const movingAverage = <?= json_encode($moving_average) ?>;
                    const growthRate = <?= json_encode($growth_rate) ?>;

                    // Hilangkan nilai null agar chart MA tidak terputus (bisa diganti dengan data aslinya jika null)
                    const cleanMA = movingAverage.map((val, index) => val === null ? actualData[index] : val);

                    // Chart 1: Moving Average (Kombinasi Bar & Line)
                    const ctxMA = document.getElementById('maChart').getContext('2d');
                    new Chart(ctxMA, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Simple Moving Average (MI-3)',
                                    data: cleanMA,
                                    borderColor: '#0284c7', // light blue 600
                                    backgroundColor: '#0284c7',
                                    borderWidth: 3,
                                    tension: 0.4,
                                    type: 'line',
                                    pointBackgroundColor: '#fff',
                                    pointBorderColor: '#0284c7',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    yAxisID: 'y'
                                },
                                {
                                    label: 'Data Pendaftar Aktual',
                                    data: actualData,
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)', // emerald 500 with opacity
                                    borderColor: 'rgba(16, 185, 129, 0.8)',
                                    borderWidth: 1,
                                    borderRadius: 8,
                                    type: 'bar',
                                    yAxisID: 'y'
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false,
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: { color: '#f1f5f9' },
                                    ticks: { font: { family: 'Quicksand' }, color: '#94a3b8' }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { family: 'Quicksand' }, color: '#94a3b8' }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { font: { family: 'Quicksand', weight: 'bold' }, usePointStyle: true, boxWidth: 8 }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { family: 'Quicksand', size: 13 },
                                    bodyFont: { family: 'Quicksand', size: 12 },
                                    padding: 12,
                                    cornerRadius: 12
                                }
                            }
                        }
                    });

                    // Chart 2: Growth Rate (Bar Chart)
                    const ctxGR = document.getElementById('grChart').getContext('2d');
                    
                    // Generate colors based on value
                    const grColors = growthRate.map(val => val >= 0 ? 'rgba(16, 185, 129, 0.7)' : 'rgba(239, 68, 68, 0.7)');
                    const grBorders = growthRate.map(val => val >= 0 ? '#10b981' : '#ef4444');

                    new Chart(ctxGR, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Growth Rate (%)',
                                data: growthRate,
                                backgroundColor: grColors,
                                borderColor: grBorders,
                                borderWidth: 1,
                                borderRadius: 8
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    grid: { color: '#f1f5f9' },
                                    ticks: { 
                                        font: { family: 'Quicksand' }, 
                                        color: '#94a3b8',
                                        callback: function(value) { return value + '%'; }
                                    }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: { font: { family: 'Quicksand' }, color: '#94a3b8' }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                    titleFont: { family: 'Quicksand', size: 13 },
                                    bodyFont: { family: 'Quicksand', size: 12, weight: 'bold' },
                                    padding: 12,
                                    cornerRadius: 12,
                                    callbacks: {
                                        label: function(context) {
                                            return context.parsed.y + '% Growth';
                                        }
                                    }
                                }
                            }
                        }
                    });
                </script>
            <?php endif; ?>

        </main>
    </div>

    <!-- Active Menu Script -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('.nav-link').forEach(nav => {
            if (nav.getAttribute('href').includes('analisis_tren.php')) {
                nav.classList.add('active-link');
                nav.classList.remove('text-slate-500');
            }
        });
    });
    </script>
</body>
</html>
