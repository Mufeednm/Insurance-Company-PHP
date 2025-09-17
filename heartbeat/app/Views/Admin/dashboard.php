<?= $this->extend("layouts/default") ?>
<?= $this->section("title") ?>Policies Dashboard<?= $this->endSection() ?>

<?= $this->section("headercss") ?>
<link href="plugins/apex/apexcharts.css" rel="stylesheet" type="text/css">
<style>
/* --- Modern dashboard styles --- */
.dashboard-wrap {
  padding: 1.25rem;
  color: #0f172a;
  font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;
}

/* Stats row */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
@media (max-width: 900px) {
  .stats-row { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 480px) {
  .stats-row { grid-template-columns: 1fr; }
}

.stat-card {
  background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
  border-radius: 12px;
  padding: 1rem;
  box-shadow: 0 8px 20px rgba(15,23,42,0.06);
  border: 1px solid rgba(15,23,42,0.04);
  display:flex;
  align-items: center;
  gap: 0.9rem;
  min-height: 84px;
}
.stat-icon {
  width:56px;
  height:56px;
  border-radius:10px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size:20px;
  color: white;
  flex-shrink:0;
}
.bg-blue { background: linear-gradient(135deg,#3b82f6,#60a5fa); }
.bg-green { background: linear-gradient(135deg,#10b981,#34d399); }
.bg-amber { background: linear-gradient(135deg,#f59e0b,#fbbf24); }
.bg-rose  { background: linear-gradient(135deg,#ef4444,#fb7185); }

.stat-content { flex:1; }
.stat-value { font-size:1.6rem; font-weight:700; line-height:1; }
.stat-label { font-size:0.78rem; color:#64748b; font-weight:600; text-transform:uppercase; margin-top:4px; }

/* Main layout */
.main-grid {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 1rem;
}
@media (max-width: 900px) {
  .main-grid { grid-template-columns: 1fr; }
}

/* Chart card */
.card {
  background: #fff;
  border-radius: 12px;
  padding: 1rem;
  border: 1px solid rgba(15,23,42,0.04);
  box-shadow: 0 8px 24px rgba(2,6,23,0.04);
}
.card h4 { margin: 0 0 0.75rem 0; font-size:1.05rem; }

/* small table */
.small-table {
  width:100%;
  border-collapse: collapse;
  font-size:0.93rem;
}
.small-table th, .small-table td {
  padding: 8px 10px;
  border-bottom: 1px solid #eef2f7;
  text-align: left;
}
.small-table th { font-size:0.8rem; color:#475569; font-weight:700; text-transform:uppercase; }

/* utility buttons */
.btn-row { display:flex; gap:0.5rem; justify-content:flex-end; margin-bottom:0.75rem; }
.btn { padding:8px 12px; border-radius:8px; border: none; cursor:pointer; font-weight:600; }
.btn-outline { background:transparent; border:1px solid #e2e8f0; color:#0f172a; }
.btn-primary { background:#3b82f6; color:#fff; border:none; }

/* sparkline container */
.spark-wrap { display:flex; align-items:center; gap:0.75rem; }
.spark-value { font-weight:700; font-size:1.1rem; }
.spark-delta { font-size:0.9rem; color:#64748b; }
.positive { color:#10b981; }
.negative { color:#ef4444; }

</style>
<?= $this->endSection() ?>

<?= $this->section("content") ?>
<div class="dashboard-wrap">

  <!-- Stats -->
  <div class="stats-row">
    <div class="stat-card">
      <div class="stat-icon bg-blue">
        <!-- simple SVG product icon -->
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
          <path d="M7 8v8"/>
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-value"><?= esc($data['totalProducts'] ?? 0) ?></div>
        <div class="stat-label">Total Products</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-green">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 7v6a9 9 0 0 0 18 0V7" />
          <path d="M3 7l9-4 9 4" />
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-value"><?= esc($data['totalPolicies'] ?? 0) ?></div>
        <div class="stat-label">Total Policies</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-amber">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M12 8v4l3 3" />
          <path d="M21 12a9 9 0 0 1-18 0" />
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-value"><?= esc($data['activePolicies'] ?? 0) ?></div>
        <div class="stat-label">Active Policies</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon bg-rose">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 12v1a3 3 0 0 1-3 3H6" />
          <path d="M8 7v4" />
        </svg>
      </div>
      <div class="stat-content">
        <div class="stat-value"><?= esc($data['expiredPolicies'] ?? 0) ?></div>
        <div class="stat-label">Expired Policies</div>
      </div>
    </div>
  </div>

  <div class="card" style="margin-top:1.5rem;">
  <h4>Policies by Product</h4>
  <div class="stats-row" style="margin-top:1rem;">
    <?php foreach ($data['productPolicyCounts'] as $pp): ?>
      <div class="stat-card">
        <div class="stat-icon bg-blue">
          <!-- icon can be dynamic later -->
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
          </svg>
        </div>
        <div class="stat-content">
          <div class="stat-value"><?= esc($pp['totalPolicies']); ?></div>
          <div class="stat-label"><?= esc($pp['name']); ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>


  <!-- Main area -->
  <div class="main-grid">
    <!-- Left: big chart -->
    <div class="card">
      <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; margin-bottom:0.5rem;">
        <div>
          <h4>Policies Created Over Time</h4>
          <div style="color:#64748b; font-size:0.9rem;">Shows daily policies based on created date</div>
        </div>

        <div style="display:flex; gap:0.6rem; align-items:center;">
          <div class="spark-wrap" id="sparkWrap" aria-hidden="true">
            <div class="spark-value" id="sparkValue">0</div>
            <div id="sparkDelta" class="spark-delta">—</div>
            <!-- <div id="sparkChart" style="width:120px; height:36px;"></div> -->
          </div>

          <div class="btn-row" style="margin:0;">
            <button class="btn btn-outline" id="refreshBtn">Refresh</button>
            <button class="btn btn-primary" id="exportBtn">Export JSON</button>
          </div>
        </div>
      </div>

      <div id="policiesLineChart" style="height:360px;"></div>
    </div>

    <!-- Right: recent days list + small stats -->
    <div class="card">
      <h4>Recent Days</h4>
      <div style="color:#64748b; font-size:0.9rem; margin-bottom:0.75rem;">Latest counts by date</div>

      <table class="small-table" id="recentTable">
        <thead>
          <tr><th>Date</th><th style="text-align:right">Policies</th></tr>
        </thead>
        <tbody>
          <!-- rows inserted by JS -->
        </tbody>
      </table>

      <div style="margin-top:1rem; display:flex; gap:0.5rem; justify-content:space-between; align-items:center;">
        <div style="font-size:0.95rem; color:#475569; font-weight:600;">Total shown</div>
        <div id="totalShown" style="font-weight:700; font-size:1.05rem; color:#0f172a;">0</div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section("footerjs") ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
(function () {
  // policiesByDays arrives from controller as JSON-string
  const rawData = <?= $data['policiesByDays'] ?? '[]' ?>;

  // ensure array
  const seriesData = Array.isArray(rawData) ? rawData : [];
  // sort by date ascending (safe guard)
  seriesData.sort((a,b) => (a.date > b.date ? 1 : (a.date < b.date ? -1 : 0)));

  const categories = seriesData.map(d => d.date);
  const values = seriesData.map(d => d.total);

  // Apex main chart
  const opts = {
    chart: {
      type: 'area',
      height: 360,
      toolbar: { show: true },
      zoom: { enabled: true },
      animations: { enabled: true }
    },
    series: [{ name: 'Policies', data: values }],
    xaxis: { categories: categories, labels: { rotate: -45 }, title: { text: 'Date' } },
    yaxis: { title: { text: 'Policies' }, min: 0 },
    stroke: { curve: 'smooth', width: 2 },
    markers: { size: 4 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.45, opacityTo: 0.05 } },
    tooltip: { y: { formatter: v => v + ' policies' } },
    grid: { borderColor: '#eef2f7' }
  };

  const chart = new ApexCharts(document.querySelector("#policiesLineChart"), opts);
  chart.render();

  // Sparkline tiny chart for recent trend
  function renderSpark(targetEl, dataVals) {
    const options = {
      chart: { type: 'area', height: 36, sparkline: { enabled: true } },
      series: [{ data: dataVals }],
      stroke: { curve: 'smooth', width: 2 },
      fill: { opacity: 0.15 },
      colors: ['#3b82f6']
    };
    const spark = new ApexCharts(targetEl, options);
    spark.render();
    return spark;
  }

  // compute percent change from last two data points
  function computeDelta(vals) {
    if (!vals || vals.length < 2) return { pct: 0, delta: 0, dir: 'neutral' };
    const last = vals[vals.length-1], prev = vals[vals.length-2];
    if (prev === 0) return { pct: last === 0 ? 0 : 100, delta: last - prev, dir: last >= prev ? 'positive' : 'negative' };
    const pct = ((last - prev) / Math.abs(prev)) * 100;
    return { pct: Math.round(pct), delta: last - prev, dir: pct >= 0 ? 'positive' : 'negative' };
  }

  // populate Recent Days table (show last 7 days)
  function populateRecentTable(rows) {
  const tbody = document.querySelector('#recentTable tbody');
  tbody.innerHTML = '';

  const shown = rows.slice(-7).reverse(); // ✅ show only last 7 days
  let totalShown = 0;

  shown.forEach(r => {
    const tr = document.createElement('tr');
    const td1 = document.createElement('td');
    td1.textContent = r.date;
    const td2 = document.createElement('td');
    td2.style.textAlign = 'right';
    td2.textContent = r.total;
    tr.appendChild(td1);
    tr.appendChild(td2);
    tbody.appendChild(tr);
    totalShown += Number(r.total || 0);
  });

  document.getElementById('totalShown').textContent = totalShown;
}


  // populate spark and stats
  const sparkEl = document.querySelector('#sparkChart');
  const spark = renderSpark(sparkEl, values.slice(-12)); // last 12 points
  const delta = computeDelta(values);
  document.getElementById('sparkValue').textContent = values.length ? values[values.length-1] : 0;
  const deltaEl = document.getElementById('sparkDelta');
  deltaEl.textContent = delta.dir === 'neutral' ? '—' : (delta.dir === 'positive' ? `↑ ${delta.pct}%` : `↓ ${Math.abs(delta.pct)}%`);
  deltaEl.className = 'spark-delta ' + (delta.dir === 'positive' ? 'positive' : (delta.dir === 'negative' ? 'negative' : ''));

  populateRecentTable(seriesData);

  // Export JSON button (client-side)
  document.getElementById('exportBtn').addEventListener('click', function () {
    const blob = new Blob([JSON.stringify(seriesData, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'policiesByDays.json';
    document.body.appendChild(a);
    a.click();
    a.remove();
    URL.revokeObjectURL(url);
  });

  // Refresh button (simple re-render). If you plan server refresh, wire it to controller endpoint.
  document.getElementById('refreshBtn').addEventListener('click', function () {
    chart.updateSeries([{ data: values }]);
    populateRecentTable(seriesData);
    // re-render spark
    spark.updateOptions({ series: [{ data: values.slice(-12) }] });
  });
})();
</script>
<?= $this->endSection() ?>
