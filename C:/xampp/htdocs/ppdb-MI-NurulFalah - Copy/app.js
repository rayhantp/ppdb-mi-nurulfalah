// app.js - PPDB Trend Analysis
// Data: monthly totals (May, Jun, Jul) for years 2021-2025
// Approximated to weekly values (4 weeks per month)

const monthlyData = [
  // Year 2021
  { month: 'May 2021', total: 15 },
  { month: 'Jun 2021', total: 10 },
  { month: 'Jul 2021', total: 5 },
  // 2022
  { month: 'May 2022', total: 12 },
  { month: 'Jun 2022', total: 20 },
  { month: 'Jul 2022', total: 8 },
  // 2023
  { month: 'May 2023', total: 19 },
  { month: 'Jun 2023', total: 19 },
  { month: 'Jul 2023', total: 3 },
  // 2024
  { month: 'May 2024', total: 8 },
  { month: 'Jun 2024', total: 17 },
  { month: 'Jul 2024', total: 10 },
  // 2025
  { month: 'May 2025', total: 11 },
  { month: 'Jun 2025', total: 21 },
  { month: 'Jul 2025', total: 5 },
];

// Generate weekly data (approximate evenly across 4 weeks per month)
const weeklyData = [];
monthlyData.forEach((item, idx) => {
  const weeksInMonth = 4;
  const weeklyValue = item.total / weeksInMonth;
  for (let w = 1; w <= weeksInMonth; w++) {
    const weekLabel = `W${(idx * weeksInMonth) + w} (${item.month})`;
    weeklyData.push({ week: weekLabel, value: parseFloat(weeklyValue.toFixed(2)) });
  }
});

// Compute Growth Rate (%) week-over-week
const growthRates = [];
for (let i = 1; i < weeklyData.length; i++) {
  const prev = weeklyData[i - 1].value;
  const curr = weeklyData[i].value;
  const rate = ((curr - prev) / prev) * 100;
  growthRates.push({ week: weeklyData[i].week, rate: parseFloat(rate.toFixed(2)) });
}

// Compute Moving Average (3‑week window)
const maWindow = 3;
const movingAverages = [];
for (let i = maWindow - 1; i < weeklyData.length; i++) {
  const slice = weeklyData.slice(i - maWindow + 1, i + 1);
  const sum = slice.reduce((a, b) => a + b.value, 0);
  const avg = sum / maWindow;
  movingAverages.push({ week: weeklyData[i].week, ma: parseFloat(avg.toFixed(2)) });
}

// Helper to create Chart.js line chart
function createLineChart(ctx, labels, data, label, color) {
  return new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: label,
        data: data,
        borderColor: color,
        backgroundColor: 'rgba(0,0,0,0)',
        tension: 0.2,
        pointRadius: 3,
        pointHoverRadius: 5,
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: true },
        tooltip: { mode: 'index', intersect: false },
      },
      scales: {
        x: { display: false },
        y: { beginAtZero: true }
      }
    }
  });
}

// Render Weekly Applicants Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
createLineChart(
  weeklyCtx,
  weeklyData.map(d => d.week),
  weeklyData.map(d => d.value),
  'Pendaftar per Minggu',
  '#ff6b6b'
);

// Render Moving Average Chart
const maCtx = document.getElementById('maChart').getContext('2d');
createLineChart(
  maCtx,
  movingAverages.map(d => d.week),
  movingAverages.map(d => d.ma),
  'Moving Average (3 Minggu)',
  '#4ecdc4'
);

// Populate Growth Rate Table
const tbody = document.querySelector('#growthTable tbody');
growthRates.forEach(item => {
  const tr = document.createElement('tr');
  const tdWeek = document.createElement('td');
  tdWeek.textContent = item.week;
  const tdRate = document.createElement('td');
  tdRate.textContent = `${item.rate}%`;
  tr.appendChild(tdWeek);
  tr.appendChild(tdRate);
  tbody.appendChild(tr);
});

// Generate Insights & Recommendations
function generateInsights() {
  // Find week with max applicants
  const maxWeek = weeklyData.reduce((a, b) => (b.value > a.value ? b : a), weeklyData[0]);
  // Overall trend (net growth from first to last week)
  const overallGrowth = ((weeklyData[weeklyData.length - 1].value - weeklyData[0].value) / weeklyData[0].value) * 100;

  const insights = [];
  insights.push(`<p><strong>Periode terpadat:</strong> ${maxWeek.week} dengan sekitar <strong>${maxWeek.value}</strong> pendaftar per minggu.</p>`);
  insights.push(`<p><strong>Tren keseluruhan:</strong> ${overallGrowth > 0 ? 'kenaikan' : 'penurunan'} sebesar <strong>${overallGrowth.toFixed(2)}%</strong> dari minggu pertama hingga terakhir.</p>`);

  // Recommendations
  insights.push('<h4>Rekomendasi Strategi Promosi PPDB</h4>');
  insights.push('<ul><li>Fokuskan promosi intensif pada bulan April‑Mei menjelang periode pendaftaran puncak.</li><li>Manfaatkan media sosial dan kunjungan sekolah pada minggu‑minggu sebelum puncak untuk meningkatkan awareness.</li></ul>');
  insights.push('<h4>Rekomendasi Kesiapan Admin</h4>');
  insights.push('<ul><li>Siapkan tim admin tambahan pada minggu‑minggu dengan perkiraan pendaftar tertinggi (contoh: minggu dengan nilai > 80% rata‑rata).</li><li>Pastikan sarana pendaftaran (formulir, sistem online) siap menangani lonjakan trafik pada periode puncak.</li></ul>');

  document.getElementById('insightsContent').innerHTML = insights.join('\n');
}

generateInsights();
