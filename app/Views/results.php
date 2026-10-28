<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <?php if (!$period): ?>
    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm max-w-md mx-auto">
      <div class="w-16 h-16 mx-auto rounded-full bg-pink-50 text-pink-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Periode Aktif</h2>
      <p class="text-gray-500 text-sm mb-6">Hasil belum dapat ditampilkan karena tidak ada pemilihan yang sedang aktif.</p>
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm transition-colors">Kembali ke Beranda</a>
    </div>
  <?php elseif (empty($period['is_results_visible'])): ?>
    <div class="bg-white rounded-3xl p-12 text-center border border-gray-100 shadow-sm max-w-lg mx-auto">
      <div class="w-20 h-20 mx-auto rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-6">
        <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 uppercase tracking-wider mb-2">
        Hasil Dirahasiakan
      </span>
      <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Hasil Belum Ditampilkan</h2>
      <p class="text-gray-600 text-sm sm:text-base leading-relaxed mb-6">
        Panitia pemilihan telah mengunci perolehan suara sementara. Hasil akhir akan ditampilkan secara resmi setelah masa pemungutan suara ditutup.
      </p>
      <a href="<?= url() ?>" class="inline-flex items-center justify-center px-6 py-3 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm shadow-sm transition-colors">
        Kembali ke Beranda
      </a>
    </div>
  <?php else: ?>

    <!-- Title and Live indicator -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
      <div>
        <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-700 uppercase tracking-wider mb-1">
          <?= e($period['name']) ?>
        </span>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 tracking-tight">Hasil Pemungutan Suara</h1>
        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Penghitungan suara otomatis dan transparan.</p>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-center bg-white px-3.5 py-2 rounded-full border border-gray-200 shadow-2xs text-xs font-medium text-gray-600">
        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
        <span class="font-semibold text-emerald-600">Live Auto-Refresh (10d)</span>
        <span id="live-updated-time" class="text-gray-400 border-l border-gray-200 pl-2"></span>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-10">
      <div class="liquid-glass-card p-6 rounded-3xl flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-pink-50 text-pink-600 flex items-center justify-center shrink-0">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
        <div>
          <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Suara Masuk</span>
          <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5" id="stat-total-voted">
            <?= format_number($stats['total_voted']) ?>
          </div>
        </div>
      </div>

      <div class="liquid-glass-card p-6 rounded-3xl flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <div>
          <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Total Siswa DPT</span>
          <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5" id="stat-total-students">
            <?= format_number($stats['total_students']) ?>
          </div>
        </div>
      </div>

      <div class="liquid-glass-card p-6 rounded-3xl flex items-center gap-4">
        <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
          <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
          </svg>
        </div>
        <div>
          <span class="text-xs uppercase font-bold text-slate-500 tracking-wider">Tingkat Partisipasi</span>
          <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-0.5" id="stat-participation">
            <?= $stats['percentage'] ?>%
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Container -->
    <div class="liquid-glass rounded-3xl p-6 sm:p-8 border border-gray-100/80 shadow-sm mb-10">
      <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
        <svg class="w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
        </svg>
        Grafik Perolehan Suara
      </h2>
      <div class="h-72 w-full">
        <canvas id="voteChart"></canvas>
      </div>
    </div>

    <!-- Ranking Table -->
    <div class="liquid-glass rounded-3xl overflow-hidden border border-gray-100/80 shadow-sm">
      <div class="p-6 border-b border-gray-100">
        <h2 class="text-lg font-bold text-gray-900">Peringkat &amp; Rekapitulasi Suara</h2>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
          <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500 tracking-wider border-b border-gray-200">
            <tr>
              <th scope="col" class="py-4 px-6 text-center w-20">Peringkat</th>
              <th scope="col" class="py-4 px-6">Kandidat</th>
              <th scope="col" class="py-4 px-6 text-right">Total Suara</th>
              <th scope="col" class="py-4 px-6 w-1/3">Persentase</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php 
            $rank = 0;
            foreach ($results as $r): 
              $rank++;
              $badgeClass = match ($rank) {
                1 => 'bg-amber-400 text-white font-extrabold shadow-sm',
                2 => 'bg-slate-300 text-slate-800 font-bold',
                3 => 'bg-amber-700 text-white font-bold',
                default => 'bg-gray-100 text-gray-600 font-medium'
              };
            ?>
              <tr class="hover:bg-pink-50/40 transition-colors">
                <td class="py-4 px-6 text-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full text-xs <?= $badgeClass ?>">
                    <?= $rank ?>
                  </span>
                </td>
                <td class="py-4 px-6">
                  <div class="flex items-center gap-3.5">
                    <img src="<?= candidate_photo($r['photo_filename'], (int) $r['order_number']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover bg-pink-100 shrink-0">
                    <div>
                      <div class="font-bold text-gray-900 text-base leading-tight">
                        <?= e($r['candidate_name']) ?>
                      </div>
                      <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full bg-pink-50 text-pink-700 mt-1">
                        Nomor <?= $r['order_number'] ?> &bull; Kelas <?= e($r['candidate_class']) ?>
                      </span>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-6 text-right">
                  <span class="font-extrabold text-gray-900 text-base" id="rank-votes-<?= $r['candidate_id'] ?>">
                    <?= format_number($r['total_votes']) ?>
                  </span>
                  <span class="text-xs text-gray-400 block">suara</span>
                </td>
                <td class="py-4 px-6">
                  <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-bold text-gray-700">
                      <span id="rank-pct-<?= $r['candidate_id'] ?>"><?= $r['vote_percentage'] ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                      <div id="rank-bar-<?= $r['candidate_id'] ?>" class="bg-pink-600 h-3 rounded-full transition-all duration-700" style="width: <?= $r['vote_percentage'] ?>%"></div>
                    </div>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  <?php endif; ?>

</div>

<!-- Chart.js and config -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="<?= asset('js/chart-config.js') ?>"></script>
<?php if ($period && !empty($period['is_results_visible'])): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const labels = <?= json_encode(array_column($results, 'candidate_name')) ?>;
    const data = <?= json_encode(array_map('intval', array_column($results, 'total_votes'))) ?>;
    initVoteChart('voteChart', labels, data);
    startLiveResultsPolling('<?= url("api/votes/results") ?>', 10000);
  });
</script>
<?php endif; ?>
