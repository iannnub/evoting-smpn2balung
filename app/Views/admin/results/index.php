<div class="space-y-6">

  <!-- Top Bar & Period Selector -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
    <div>
    <div class="flex items-center gap-2 mb-1">
      <h2 class="text-xl font-bold text-gray-900">Rekapitulasi Hasil Pemilihan</h2>
      <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        <span>Real-time Aktif</span>
      </div>
    </div>
    <p class="text-xs text-gray-500">
      Periode: <strong class="text-pink-600"><?= e($period['name'] ?? 'Tidak ada periode') ?></strong> • Otomatis diperbarui terus-menerus
    </p>
  </div>

  <div class="flex flex-wrap items-center gap-3">
    <!-- Period dropdown -->
    <form action="<?= url('admin/hasil') ?>" method="GET" class="inline-block">
      <select name="period_id" onchange="this.form.submit()" class="px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 bg-white focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100">
        <?php foreach ($allPeriods as $p): ?>
          <option value="<?= $p['id'] ?>" <?= $p['id'] == $selectedPeriodId ? 'selected' : '' ?>>
            <?= e($p['name']) ?> <?= !empty($p['is_active']) ? '(Aktif)' : '' ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>

    <?php if (!empty($selectedPeriodId)): ?>
      <a href="<?= url('admin/hasil/export?period_id=' . $selectedPeriodId) ?>" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Export Excel (.xlsx)
      </a>

      <button type="button" onclick="document.getElementById('modal-reset-votes').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-xs transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
        Reset Suara (0)
      </button>
    <?php endif; ?>
  </div>
</div>

<!-- Key Metrics -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
  <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
    <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Pemilih (DPT)</div>
    <div class="text-3xl font-extrabold text-gray-900 mt-2 font-mono" id="admin-stat-total-students">
      <?= number_format($stats['total_students'] ?? 0, 0, ',', '.') ?>
    </div>
    <div class="text-[11px] text-gray-400 mt-1">Pemilih terdaftar (Siswa & Guru)</div>
  </div>

  <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
    <div class="text-xs font-bold text-pink-600 uppercase tracking-wider">Suara Masuk</div>
    <div class="text-3xl font-extrabold text-pink-600 mt-2 font-mono" id="admin-stat-total-voted">
      <?= number_format($stats['total_voted'] ?? 0, 0, ',', '.') ?>
    </div>
    <div class="text-[11px] text-gray-400 mt-1">Pemilih yang telah memberikan suara</div>
  </div>

  <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs">
    <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Tingkat Partisipasi</div>
    <div class="text-3xl font-extrabold text-emerald-600 mt-2 font-mono" id="admin-stat-percentage">
      <?= number_format($stats['percentage'] ?? 0, 1) ?>%
    </div>
    <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2.5 overflow-hidden">
      <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" id="admin-stat-progress" style="width: <?= min(100, $stats['percentage'] ?? 0) ?>%"></div>
    </div>
  </div>
</div>

  <!-- Candidate Standings Cards -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs space-y-6">
    <h3 class="text-lg font-bold text-gray-900">Perolehan Suara Kandidat</h3>

    <?php if (empty($results)): ?>
      <div class="py-8 text-center text-gray-400 text-xs">
        Belum ada kandidat atau belum ada suara yang tercatat pada periode ini.
      </div>
    <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($results as $c): ?>
          <div class="rounded-3xl border border-gray-100 p-5 bg-gradient-to-b from-white to-pink-50/20 shadow-xs flex flex-col justify-between relative overflow-hidden">
            <div class="absolute top-4 right-4 w-9 h-9 rounded-xl gradient-pink text-white font-extrabold flex items-center justify-center text-sm shadow-xs">
              #<?= $c['order_number'] ?>
            </div>

            <div>
              <div class="flex items-center gap-4 mb-4">
                <div class="w-16 h-16 rounded-2xl bg-pink-100 overflow-hidden shrink-0 border border-pink-200">
                  <img src="<?= candidate_photo_url($c['photo_filename'] ?? null) ?>" alt="<?= e($c['candidate_name']) ?>" class="w-full h-full object-cover">
                </div>
                <div class="pr-8">
                  <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-pink-50 text-pink-700 border border-pink-100 mb-1">
                    Kelas <?= e($c['candidate_class']) ?>
                  </span>
                  <h4 class="font-bold text-gray-900 text-sm leading-snug"><?= e($c['candidate_name']) ?></h4>
                </div>
              </div>

              <!-- Vote Count & Bar -->
              <div class="space-y-1.5 pt-2">
                <div class="flex items-baseline justify-between">
                  <span class="text-2xl font-black text-gray-900 font-mono" id="cand-votes-<?= $c['candidate_id'] ?>"><?= number_format($c['total_votes'], 0, ',', '.') ?> <span class="text-xs font-normal text-gray-500">suara</span></span>
                  <span class="text-base font-black text-pink-600 font-mono" id="cand-pct-<?= $c['candidate_id'] ?>"><?= $c['vote_percentage'] ?>%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                  <div class="h-full gradient-pink rounded-full transition-all duration-500" id="cand-bar-<?= $c['candidate_id'] ?>" style="width: <?= min(100, (float)$c['vote_percentage']) ?>%"></div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Participation Breakdowns: Tingkat & Kelas -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Tingkat Participation -->
    <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-xs space-y-4">
      <h4 class="font-bold text-gray-900 text-sm">Partisipasi Berdasarkan Tingkat</h4>
      
      <div class="space-y-3">
        <?php if (empty($byGrade)): ?>
          <p class="text-xs text-gray-400">Belum ada data tingkat.</p>
        <?php else: ?>
          <?php foreach ($byGrade as $g): ?>
            <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 space-y-1.5">
              <div class="flex items-center justify-between text-xs">
                <span class="font-bold text-gray-800"><?= grade_label((int)$g['grade']) ?></span>
                <span class="font-mono font-bold text-pink-600"><?= $g['percentage'] ?>%</span>
              </div>
              <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                <div class="h-full bg-pink-500 rounded-full" style="width: <?= min(100, (float)$g['percentage']) ?>%"></div>
              </div>
              <div class="flex justify-between text-[10px] text-gray-400 font-mono">
                <span><?= $g['total_voted'] ?> memilih</span>
                <span>dari <?= $g['total_students'] ?> siswa</span>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

    <!-- Kelas Participation -->
    <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-gray-100 shadow-xs space-y-4">
      <h4 class="font-bold text-gray-900 text-sm">Partisipasi Berdasarkan Kelas</h4>
      
      <div class="overflow-x-auto max-h-72 overflow-y-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 text-gray-500 font-semibold uppercase text-[10px] sticky top-0">
            <tr>
              <th class="py-2.5 px-3">Kelas</th>
              <th class="py-2.5 px-3 text-right">Total DPT</th>
              <th class="py-2.5 px-3 text-right">Sudah Vote</th>
              <th class="py-2.5 px-3 text-right">Partisipasi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($byClass)): ?>
              <tr><td colspan="4" class="py-4 text-center text-gray-400">Belum ada data per kelas.</td></tr>
            <?php else: ?>
              <?php foreach ($byClass as $cls): ?>
                <tr class="hover:bg-slate-50">
                  <td class="py-2 px-3 font-bold text-gray-800"><?= e($cls['class']) ?></td>
                  <td class="py-2 px-3 text-right font-mono text-gray-500"><?= $cls['total_students'] ?></td>
                  <td class="py-2 px-3 text-right font-mono font-semibold text-emerald-700"><?= $cls['total_voted'] ?></td>
                  <td class="py-2 px-3 text-right">
                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?= (float)$cls['percentage'] >= 75 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-700' ?>">
                      <?= $cls['percentage'] ?>%
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

  <!-- Audit Trail / Recent Votes Table -->
  <div class="bg-white rounded-3xl border border-gray-100 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <h4 class="font-bold text-gray-900 text-sm">Audit Trail: 100 Pemilih Terakhir</h4>
      <span class="text-xs text-gray-400">Data terlindungi & tersimpan permanen</span>
    </div>

    <div class="overflow-x-auto max-h-80 overflow-y-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 text-gray-500 font-semibold uppercase text-[10px] sticky top-0">
          <tr>
            <th class="py-3 px-4 w-12 text-center">#</th>
            <th class="py-3 px-4">Nama Siswa</th>
            <th class="py-3 px-4">Kelas</th>
            <th class="py-3 px-4">Pilihan Kandidat</th>
            <th class="py-3 px-4">Waktu Vote</th>
            <th class="py-3 px-4 font-mono text-[11px]">IP Address</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php if (empty($votesDetail)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-gray-400">Belum ada aktivitas pemberian suara yang tercatat.</td>
            </tr>
          <?php else: ?>
            <?php $i = 1; ?>
            <?php foreach ($votesDetail as $v): ?>
              <tr class="hover:bg-slate-50/70">
                <td class="py-2.5 px-4 text-center font-mono text-gray-400"><?= $i++ ?></td>
                <td class="py-2.5 px-4 font-bold text-gray-900"><?= e($v['student_name']) ?></td>
                <td class="py-2.5 px-4">
                  <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-pink-50 text-pink-700">
                    <?= e($v['student_class']) ?>
                  </span>
                </td>
                <td class="py-2.5 px-4 font-semibold text-gray-800">
                  <?= e($v['candidate_name']) ?> <span class="text-gray-400 text-[11px]">(<?= e($v['candidate_class']) ?>)</span>
                </td>
                <td class="py-2.5 px-4 text-gray-500 font-mono text-[11px]">
                  <?= date('d/m/Y H:i:s', strtotime($v['voted_at'])) ?>
                </td>
                <td class="py-2.5 px-4 font-mono text-[11px] text-gray-400">
                  <?= e($v['ip_address'] ?: '-') ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Reset Suara -->
  <div id="modal-reset-votes" class="hidden fixed inset-0 z-50 overflow-y-auto bg-black/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 animate-in fade-in zoom-in duration-200">
      <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center mx-auto mb-4">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
      </div>

      <h3 class="text-lg font-bold text-gray-900 text-center mb-1">Reset Suara Menjadi 0</h3>
      <p class="text-xs text-gray-500 text-center mb-5 leading-relaxed">
        Tindakan ini akan <strong>menghapus seluruh perolehan suara</strong> pada periode <strong class="text-gray-800"><?= e($period['name'] ?? '') ?></strong> agar perolehan suara kembali 0.
        <br><br>
        <span class="inline-block text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1.5 rounded-xl font-semibold text-[11px]">
          ✓ Data Pemilih (Siswa & Guru) dan Data Paslon TETAP AMAN / TIDAK TERHAPUS.
        </span>
      </p>

      <form action="<?= url('admin/periode/' . ($selectedPeriodId ?? 0) . '/reset-votes') ?>" method="POST" onsubmit="return confirm('Apakah Anda benar-benar yakin ingin mereset seluruh suara masuk menjadi 0?')">
        <?= csrf_field() ?>
        <div class="flex gap-3">
          <button type="button" onclick="document.getElementById('modal-reset-votes').classList.add('hidden')" class="flex-1 py-2.5 px-4 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors shadow-xs">
            Ya, Reset ke 0
          </button>
        </div>
      </form>
    </div>
  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const periodId = <?= json_encode((int)$selectedPeriodId) ?>;
  const apiUrl = '<?= url("api/admin/results") ?>?period_id=' + periodId;

  const pollInterval = setInterval(async () => {
    try {
      const res = await fetch(apiUrl, {
        headers: { 'Accept': 'application/json' }
      });
      if (!res.ok) return;
      const json = await res.json();
      if (!json.success || !json.data) return;

      const { statistics, results } = json.data;

      // Update Key Metrics
      const elStudents = document.getElementById('admin-stat-total-students');
      const elVoted = document.getElementById('admin-stat-total-voted');
      const elPct = document.getElementById('admin-stat-percentage');
      const elProg = document.getElementById('admin-stat-progress');

      if (elStudents && statistics) elStudents.textContent = Number(statistics.total_students).toLocaleString('id-ID');
      if (elVoted && statistics) elVoted.textContent = Number(statistics.total_voted).toLocaleString('id-ID');
      if (elPct && statistics) elPct.textContent = `${statistics.percentage}%`;
      if (elProg && statistics) elProg.style.width = `${Math.min(100, statistics.percentage)}%`;

      // Update Candidate Standing Cards
      if (results && Array.isArray(results)) {
        results.forEach(c => {
          const candId = c.candidate_id || c.id;
          const votesEl = document.getElementById(`cand-votes-${candId}`);
          const pctEl = document.getElementById(`cand-pct-${candId}`);
          const barEl = document.getElementById(`cand-bar-${candId}`);

          if (votesEl) votesEl.innerHTML = `${Number(c.total_votes).toLocaleString('id-ID')} <span class="text-xs font-normal text-gray-500">suara</span>`;
          if (pctEl) pctEl.textContent = `${c.vote_percentage}%`;
          if (barEl) barEl.style.width = `${Math.min(100, parseFloat(c.vote_percentage))}%`;
        });
      }
    } catch (e) {
      // quiet fail for background polling
    }
  }, 3000);

  window.addEventListener('beforeunload', () => clearInterval(pollInterval));
});
</script>
