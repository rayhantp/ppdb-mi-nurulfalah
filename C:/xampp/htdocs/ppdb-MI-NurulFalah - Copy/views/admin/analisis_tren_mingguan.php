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

// FETCH DATA PER MINGGU
$query = "
    SELECT 
        YEARWEEK(tgl_daftar, 1) as yrweek,
        MIN(DATE(tgl_daftar)) as start_date,
        COUNT(*) as total
    FROM pendaftar
    WHERE tgl_daftar IS NOT NULL
    GROUP BY YEARWEEK(tgl_daftar, 1)
    ORDER BY yrweek ASC
";
$stmt = $pdo->query($query);
$data_mentah = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($data_mentah)) {
    $data_mentah = [];
}

// Helper Bahasa Indonesia untuk Tanggal/Bulan
function tgl_indo($date_str) {
    $months = [
        'Jan' => 'Jan', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Apr', 'May' => 'Mei', 'Jun' => 'Jun',
        'Jul' => 'Jul', 'Aug' => 'Agt', 'Sep' => 'Sep', 'Oct' => 'Okt', 'Nov' => 'Nov', 'Dec' => 'Des'
    ];
    $time = strtotime($date_str);
    $m = date('M', $time);
    $indo_m = $months[$m] ?? $m;
    return date('d ', $time) . $indo_m . date(' Y', $time);
}

$labels = [];
$actual_data = [];
$growth_rate = [];
$moving_average = [];
$n_ma = 3; // 3-Week Moving Average

for ($i = 0; $i < count($data_mentah); $i++) {
    $label_minggu = "Mgg " . tgl_indo($data_mentah[$i]['start_date']);
    $labels[] = $label_minggu;
    $current_total = (float)$data_mentah[$i]['total'];
    $actual_data[] = $current_total;

    // 1. Hitung Growth Rate Mingguan
    if ($i === 0) {
        $growth_rate[] = 0;
    } else {
        $prev_total = (float)$data_mentah[$i - 1]['total'];
        if ($prev_total > 0) {
            $gr = (($current_total - $prev_total) / $prev_total) * 100;
        } else {
            $gr = $current_total > 0 ? 100 : 0;
        }
        $growth_rate[] = round($gr, 2);
    }

    // 2. Hitung Moving Average (3-Minggu)
    if ($i >= ($n_ma - 1)) {
        $sum = 0;
        for ($j = 0; $j < $n_ma; $j++) {
            $sum += (float)$data_mentah[$i - $j]['total'];
        }
        $ma = $sum / $n_ma;
        $moving_average[] = round($ma, 2);
    } else {
        $moving_average[] = null;
    }
}

// Analisis Insight
$busiest_week_label = '-';
$busiest_week_val = 0;
$latest_trend_desc = 'Stabil';

if (!empty($actual_data)) {
    // Cari minggu teramai
    $max_idx = array_search(max($actual_data), $actual_data);
    $busiest_week_label = $labels[$max_idx];
    $busiest_week_val = $actual_data[$max_idx];

    // Tren kenaikan/penurunan (perbandingan 3 minggu terakhir dengan 3 minggu sebelumnya)
    $cnt = count($actual_data);
    if ($cnt >= 6) {
        $last_3 = array_slice($actual_data, -3);
        $prev_3 = array_slice($actual_data, -6, 3);
        $avg_last = array_sum($last_3) / 3;
        $avg_prev = array_sum($prev_3) / 3;
        $diff = $avg_last - $avg_prev;
        if ($diff > 0.5) {
            $latest_trend_desc = "Kenaikan rata-rata pendaftar mingguan (+ " . round($diff, 1) . " siswa) dibandingkan periode sebelumnya.";
        } elseif ($diff < -0.5) {
            $latest_trend_desc = "Penurunan rata-rata pendaftar mingguan (" . round($diff, 1) . " siswa) dibandingkan periode sebelumnya.";
        } else {
            $latest_trend_desc = "Relatif stabil dengan perbedaan rata-rata di bawah 1 siswa per minggu.";
        }
    } else {
        $latest_trend_desc = "Data historis mingguan belum cukup untuk analisis tren jangka pendek komparatif (butuh minimal 6 minggu).";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Analisis Tren Mingguan - MI Nurul Falah</title>
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
                        Analisis <span class="bg-gradient-to-r from-green-700 to-emerald-600 bg-clip-text text-transparent">Tren Mingguan</span>
                    </h2>
                    <p class="text-sm text-slate-400 font-medium mt-1">Laporan Mingguan Kuantitatif PPDB MI Nurul Falah</p>
                </div>
            </header>

            <?php if (empty($data_mentah)): ?>
                <div class="bg-white rounded-3xl p-16 text-center border border-dashed border-slate-300 shadow-sm">
                    <span class="text-5xl mb-4 block opacity-50">📊</span>
                    <h3 class="text-xl font-bold text-slate-600">Belum Ada Data Pendaftar</h3>
                    <p class="text-slate-400 mt-2 text-sm">Data pendaftaran mingguan akan muncul secara otomatis setelah ada siswa baru yang mendaftar.</p>
                </div>
            <?php else: ?>

                <!-- KARTU STATISTIK RINGKASAN -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-green-50 rounded-full transition-transform duration-500 group-hover:scale-150"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Total Minggu Aktif</p>
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
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Growth Rate Minggu Ini</p>
                        <p class="text-4xl font-black <?= $gr_color ?> mt-2 relative z-10"><?= $latest_gr; ?>%</p>
                        <span class="absolute bottom-6 right-6 text-2xl z-10 opacity-60 group-hover:rotate-12 transition-transform"><?= $latest_gr >= 0 ? '📈' : '📉' ?></span>
                    </div>

                    <?php $latest_ma = end($moving_average); ?>
                    <div class="bg-white/80 backdrop-blur-md p-6 rounded-[2rem] border border-slate-100 shadow-sm relative overflow-hidden group">
                        <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full transition-transform duration-500 group-hover:scale-150"></div>
                        <p class="text-[10px] font-black uppercase tracking-widest text-slate-400 relative z-10">Moving Average (3-Minggu)</p>
                        <p class="text-4xl font-black text-slate-800 mt-2 relative z-10"><?= $latest_ma ?? '-'; ?></p>
                        <span class="absolute bottom-6 right-6 text-2xl z-10 opacity-60 group-hover:rotate-12 transition-transform">📉</span>
                    </div>
                </div>

                <!-- CHARTS SECTION -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                    
                    <!-- CHART MOVING AVERAGE -->
                    <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)]">
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-800">Jumlah Pendaftar & Moving Average (3-Minggu)</h3>
                            <p class="text-xs text-slate-400">Garis biru menunjukkan rata-rata bergerak 3 minggu terakhir.</p>
                        </div>
                        <div class="relative h-72">
                            <canvas id="maChart"></canvas>
                        </div>
                    </div>

                    <!-- CHART GROWTH RATE -->
                    <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)]">
                        <div class="mb-6">
                            <h3 class="text-lg font-black text-slate-800">Growth Rate Mingguan (%)</h3>
                            <p class="text-xs text-slate-400">Persentase kenaikan/penurunan pendaftar dari minggu ke minggu.</p>
                        </div>
                        <div class="relative h-72">
                            <canvas id="grChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA & INSIGHTS GRID -->
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                    
                    <!-- TABEL DATA LENGKAP (SPAN 2) -->
                    <div class="xl:col-span-2 bg-white/90 backdrop-blur-md rounded-[2.5rem] border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] overflow-hidden">
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-sm font-black text-slate-800 uppercase tracking-widest">Detail Perhitungan Mingguan</h3>
                        </div>
                        <div class="overflow-y-auto max-h-[500px]">
                            <table class="w-full text-sm">
                                <thead class="sticky top-0 z-20 bg-slate-50">
                                    <tr class="text-left text-[10px] uppercase tracking-[0.2em] text-slate-400 bg-slate-50 border-b border-slate-100 font-mono">
                                        <th class="px-8 py-4">Periode</th>
                                        <th class="px-8 py-4">Pendaftar (X)</th>
                                        <th class="px-8 py-4">Growth Rate</th>
                                        <th class="px-8 py-4">MA (3-Minggu)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    <?php for ($i = count($labels) - 1; $i >= 0; $i--): ?>
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
                                            <?= $moving_average[$i] !== null ? number_format($moving_average[$i], 2, '.', '') : '<span class="text-slate-300 text-[10px] italic">N/A</span>' ?>
                                        </td>
                                    </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- INSIGHTS & REKOMENDASI (SPAN 1) -->
                    <div class="bg-white/90 backdrop-blur-md rounded-[2.5rem] p-8 border border-slate-100 shadow-[0_20px_50px_-15px_rgba(0,0,0,0.02)] flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-black text-slate-800 mb-6">Insight &amp; Rekomendasi</h3>
                            
                            <!-- 1. Periode Teramai -->
                            <div class="mb-6">
                                <h4 class="text-xs font-bold text-emerald-600 uppercase tracking-wider mb-2">🔥 Periode Paling Ramai</h4>
                                <p class="text-sm text-slate-600">
                                    Puncak pendaftaran tercatat pada <strong><?= $busiest_week_label; ?></strong> dengan total <strong><?= $busiest_week_val; ?> pendaftar baru</strong> dalam satu minggu.
                                </p>
                            </div>

                            <!-- 2. Tren Kenaikan/Penurunan -->
                            <div class="mb-6">
                                <h4 class="text-xs font-bold text-blue-600 uppercase tracking-wider mb-2">📈 Tren Kenaikan / Penurunan</h4>
                                <p class="text-sm text-slate-600">
                                    <?= $latest_trend_desc; ?>
                                </p>
                            </div>

                            <!-- 3. Rekomendasi Promosi -->
                            <div class="mb-6">
                                <h4 class="text-xs font-bold text-amber-600 uppercase tracking-wider mb-2">📢 Rekomendasi Strategi Promosi</h4>
                                <ul class="text-sm text-slate-600 list-disc list-inside space-y-1">
                                    <li>Lakukan promosi masif 2 minggu sebelum periode teramai (Mei‑Juni).</li>
                                    <li>Gunakan branding berbasis prestasi sekolah untuk memicu minat orang tua murid.</li>
                                </ul>
                            </div>

                            <!-- 4. Kesiapan Admin -->
                            <div>
                                <h4 class="text-xs font-bold text-purple-600 uppercase tracking-wider mb-2">⚡ Kesiapan Administrasi</h4>
                                <ul class="text-sm text-slate-600 list-disc list-inside space-y-1">
                                    <li>Siapkan staf administrasi ekstra pada periode teramai untuk meminimalkan antrean pendaftaran.</li>
                                    <li>Sediakan panduan alur pendaftaran cepat agar proses verifikasi berkas lebih efisien.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- INIT CHART.JS -->
                <script>
                    const labels = <?= json_encode($labels) ?>;
                    const actualData = <?= json_encode($actual_data) ?>;
                    const movingAverage = <?= json_encode($moving_average) ?>;
                    const growthRate = <?= json_encode($growth_rate) ?>;

                    const cleanMA = movingAverage.map((val, index) => val === null ? actualData[index] : val);

                    // Chart 1: Moving Average
                    const ctxMA = document.getElementById('maChart').getContext('2d');
                    new Chart(ctxMA, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: 'Simple Moving Average (MA-3)',
                                    data: cleanMA,
                                    borderColor: '#0284c7',
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
                                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                                    borderColor: 'rgba(16, 185, 129, 0.8)',
                                    borderWidth: 1,
                                    borderRadius: 6,
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
                                }
                            }
                        }
                    });

                    // Chart 2: Growth Rate
                    const ctxGR = document.getElementById('grChart').getContext('2d');
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
                                borderRadius: 6
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
                                legend: { display: false }
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
            if (nav.getAttribute('href').includes('analisis_tren_mingguan.php')) {
                nav.classList.add('active-link');
                nav.classList.remove('text-slate-500');
            }
        });
    });
    </script>
</body>
</html>
