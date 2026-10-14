<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  
  <!-- Breadcrumbs -->
  <nav class="flex items-center gap-2 text-xs text-gray-500 mb-8" aria-label="Breadcrumb">
    <a href="<?= url() ?>" class="hover:text-pink-600 transition-colors">Beranda</a>
    <span>/</span>
    <span class="text-gray-400">Kandidat</span>
    <span>/</span>
    <span class="text-pink-600 font-semibold truncate"><?= e($candidate['full_name']) ?></span>
  </nav>

  <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm p-6 sm:p-10">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start">
      
      <!-- Photo Column -->
      <div class="flex flex-col items-center">
        <div class="relative w-full max-w-[280px] aspect-square rounded-2xl overflow-hidden bg-pink-50 shadow-md">
          <img src="<?= candidate_photo($candidate['photo_filename']) ?>" alt="Foto <?= e($candidate['full_name']) ?>" class="w-full h-full object-cover">
          <div class="absolute top-3 left-3">
            <span class="inline-flex items-center justify-center w-10 h-10 rounded-full font-bold text-white gradient-pink shadow-md">
              <?= $candidate['order_number'] ?: '?' ?>
            </span>
          </div>
        </div>

        <div class="mt-4 text-center">
          <span class="inline-block px-4 py-1.5 rounded-full text-sm font-bold bg-pink-100 text-pink-700">
            Kelas <?= e($candidate['class']) ?>
          </span>
        </div>

        <div class="mt-6 w-full">
          <a href="<?= url('vote') ?>" class="block w-full py-3.5 px-4 rounded-xl text-center text-white font-bold gradient-pink shadow-md hover:brightness-105 active:scale-98 transition-all">
            PILIH KANDIDAT INI
          </a>
        </div>
      </div>

      <!-- Info Column -->
      <div class="md:col-span-2 space-y-8">
        <div>
          <span class="text-xs uppercase font-bold text-pink-600 tracking-wider">Kandidat Nomor Urut <?= $candidate['order_number'] ?></span>
          <h1 class="text-2xl sm:text-4xl font-extrabold text-gray-900 mt-1"><?= e($candidate['full_name']) ?></h1>
          <p class="text-sm text-gray-400 mt-1"><?= e($candidate['period_name'] ?? 'Pemilihan OSIS') ?></p>
        </div>

        <!-- Visi -->
        <div class="bg-slate-50 p-6 rounded-2xl border border-gray-100">
          <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-pink-700 flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
            Visi
          </h2>
          <p class="text-gray-700 text-sm sm:text-base leading-relaxed whitespace-pre-line">
            <?= !empty($candidate['vision']) ? e($candidate['vision']) : 'Visi belum diisi oleh kandidat.' ?>
          </p>
        </div>

        <!-- Misi -->
        <div class="bg-slate-50 p-6 rounded-2xl border border-gray-100">
          <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider text-purple-700 flex items-center gap-2 mb-3">
            <svg class="w-5 h-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            Misi &amp; Program Kerja
          </h2>
          <?php $missions = mission_items($candidate['mission']); ?>
          <?php if (!empty($missions)): ?>
            <ul class="space-y-2.5">
              <?php foreach ($missions as $m): ?>
                <li class="flex items-start gap-3 text-sm sm:text-base text-gray-700">
                  <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-100 text-purple-700 text-xs font-bold mt-0.5 shrink-0">✓</span>
                  <span><?= e($m) ?></span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-gray-500 text-sm">Misi belum diisi oleh kandidat.</p>
          <?php endif; ?>
        </div>

        <div class="pt-4 flex justify-between items-center text-sm">
          <a href="<?= url() ?>" class="text-pink-600 hover:underline flex items-center gap-1 font-medium">
            ← Kembali ke Daftar Kandidat
          </a>
        </div>

      </div>

    </div>
  </div>
</div>
