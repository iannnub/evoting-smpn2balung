<div class="space-y-8">

  <!-- Active Period Banner -->
  <div class="bg-gradient-to-r from-pink-600 via-pink-700 to-purple-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
      <div>
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-white/20 backdrop-blur-md mb-2">
          <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
          <span>Periode Pemilihan Aktif</span>
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
          <?= $period ? e($period['name']) : 'Belum Ada Periode Aktif' ?>
        </h2>
        <p class="text-xs sm:text-sm text-pink-100 mt-1">
          Mulai: <?= $period ? format_date_id($period['start_date']) : '-' ?> &bull; 
          Selesai: <?= $period && $period['end_date'] ? format_date_id($period['end_date']) : 'Sesuai jadwal panitia' ?>
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2.5">
        <?php if ($period): ?>
          <form action="<?= url('admin/periode/' . $period['id'] . '/toggle-hasil') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-white/10 hover:bg-white/20 border border-white/20 transition-colors">
              <?= !empty($period['is_results_visible']) ? '🔒 Sembunyikan Hasil Publik' : '👁️ Buka Hasil ke Publik' ?>
            </button>
          </form>

          <form action="<?= url('admin/periode/' . $period['id'] . '/toggle-voting') ?>" method="POST">
            <?= csrf_field() ?>
            <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold <?= !empty($period['is_voting_open']) ? 'bg-amber-500 hover:bg-amber-600' : 'bg-emerald-500 hover:bg-emerald-600' ?> text-white transition-colors">
              <?= !empty($period['is_voting_open']) ? 'Tutup Bilik Suara' : 'Buka Bilik Suara' ?>
            </button>
          </form>
        <?php else: ?>
          <a href="<?= url('admin/periode/tambah') ?>" class="px-5 py-2.5 rounded-xl text-xs font-bold bg-white text-pink-700 hover:bg-pink-50 transition-colors">
            + Buat Periode Baru
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- 4 Stats Cards -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kandidat</span>
        <span class="w-10 h-10 rounded-xl bg-pink-50 text-pink-600 flex items-center justify-center font-bold text-sm">01</span>
      </div>
      <div class="text-3xl font-extrabold text-gray-900 mt-3"><?= count($candidates) ?></div>
      <span class="text-xs text-gray-400 mt-1 block">Calon terdaftar</span>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Siswa DPT</span>
        <span class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center font-bold text-sm">02</span>
      </div>
      <div class="text-3xl font-extrabold text-gray-900 mt-3"><?= format_number($stats['total_students']) ?></div>
      <span class="text-xs text-gray-400 mt-1 block">Pemilih terdaftar</span>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Suara Masuk</span>
        <span class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-sm">03</span>
      </div>
      <div class="text-3xl font-extrabold text-gray-900 mt-3"><?= format_number($stats['total_voted']) ?></div>
      <span class="text-xs text-gray-400 mt-1 block">Total suara sah</span>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
      <div class="flex items-center justify-between">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Partisipasi</span>
        <span class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold text-sm">04</span>
      </div>
      <div class="text-3xl font-extrabold text-gray-900 mt-3"><?= $stats['percentage'] ?>%</div>
      <span class="text-xs text-gray-400 mt-1 block">Tingkat kehadiran</span>
    </div>
  </div>

  <!-- Quick Action Buttons -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs">
    <h3 class="text-base font-bold text-gray-900 mb-4">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <a href="<?= url('admin/kandidat/tambah') ?>" class="p-4 rounded-2xl border border-gray-200 hover:border-pink-500 hover:bg-pink-50/40 text-center transition-all group">
        <div class="w-10 h-10 rounded-full bg-pink-100 text-pink-600 mx-auto flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-gray-800 block">Tambah Kandidat</span>
      </a>

      <a href="<?= url('admin/import') ?>" class="p-4 rounded-2xl border border-gray-200 hover:border-purple-500 hover:bg-purple-50/40 text-center transition-all group">
        <div class="w-10 h-10 rounded-full bg-purple-100 text-purple-600 mx-auto flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
          </svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-gray-800 block">Import DPT Excel</span>
      </a>

      <a href="<?= url('admin/periode/tambah') ?>" class="p-4 rounded-2xl border border-gray-200 hover:border-emerald-500 hover:bg-emerald-50/40 text-center transition-all group">
        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 mx-auto flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-gray-800 block">Buat Periode Baru</span>
      </a>

      <a href="<?= url('admin/hasil/export') ?>" class="p-4 rounded-2xl border border-gray-200 hover:border-blue-500 hover:bg-blue-50/40 text-center transition-all group">
        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 mx-auto flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
        </div>
        <span class="text-xs sm:text-sm font-semibold text-gray-800 block">Download Laporan</span>
      </a>
    </div>
  </div>

  <!-- Chart and Candidate Summary -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Chart -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-xs flex flex-col">
      <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center justify-between">
        <span>Grafik Hasil Suara</span>
        <a href="<?= url('admin/hasil') ?>" class="text-xs text-pink-600 hover:underline font-semibold">Lihat Detail →</a>
      </h3>
      <div class="h-64 flex-1">
        <canvas id="dashVoteChart"></canvas>
      </div>
    </div>

    <!-- Candidate List -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-gray-100 shadow-xs flex flex-col">
      <h3 class="text-base font-bold text-gray-900 mb-4 flex items-center justify-between">
        <span>Kandidat Terdaftar</span>
        <a href="<?= url('admin/kandidat') ?>" class="text-xs text-pink-600 hover:underline font-semibold">Kelola Semua →</a>
      </h3>

      <?php if (empty($candidates)): ?>
        <div class="flex-1 flex flex-col items-center justify-center text-center p-6 text-gray-400">
          <p class="text-sm">Belum ada kandidat di periode ini.</p>
          <a href="<?= url('admin/kandidat/tambah') ?>" class="mt-2 text-xs font-semibold text-pink-600 hover:underline">+ Tambah Kandidat Sekarang</a>
        </div>
      <?php else: ?>
        <div class="divide-y divide-gray-100 flex-1">
          <?php foreach ($candidates as $c): ?>
            <div class="py-3 flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img src="<?= candidate_photo($c['photo_filename']) ?>" alt="" class="w-10 h-10 rounded-xl object-cover bg-pink-50">
                <div>
                  <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-pink-50 text-pink-700">Nomor <?= $c['order_number'] ?></span>
                  <div class="font-bold text-sm text-gray-800"><?= e($c['full_name']) ?></div>
                </div>
              </div>
              <div class="text-right">
                <span class="font-bold text-gray-900 text-sm"><?= format_number($c['total_votes']) ?></span>
                <span class="text-xs text-gray-400 block">suara</span>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

</div>

<script src="<?= asset('js/chart-config.js') ?>"></script>
<?php if (!empty($results)): ?>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const labels = <?= json_encode(array_column($results, 'candidate_name')) ?>;
    const data = <?= json_encode(array_map('intval', array_column($results, 'total_votes'))) ?>;
    initVoteChart('dashVoteChart', labels, data);
  });
</script>
<?php endif; ?>
