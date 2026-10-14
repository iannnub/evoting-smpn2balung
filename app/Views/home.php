<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-br from-pink-500 via-pink-600 to-purple-700 text-white py-16 sm:py-24">
  <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#fff_1px,transparent_1px)] [background-size:16px_16px]"></div>
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative text-center">
    
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md text-xs font-semibold mb-6 border border-white/20">
      <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
      <span><?= $period ? e($period['name']) : 'Pemilihan OSIS' ?></span>
    </div>

    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-4 drop-shadow-xs">
      Pemilihan Ketua &amp; Wakil Ketua OSIS
    </h1>
    <p class="text-lg sm:text-xl text-pink-100 max-w-2xl mx-auto mb-8 font-medium">
      <?= e(config('app.school')) ?> &bull; <?= e(config('app.region')) ?>
    </p>

    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="<?= url('vote') ?>" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-full bg-white text-pink-600 font-bold text-base shadow-xl hover:bg-pink-50 hover:scale-105 active:scale-95 transition-all">
        <svg class="w-5 h-5 mr-2 text-pink-500" fill="currentColor" viewBox="0 0 20 20">
          <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
        </svg>
        VOTE SEKARANG
      </a>
      <a href="<?= url('hasil') ?>" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-full bg-white/10 hover:bg-white/20 text-white font-semibold text-base border border-white/30 backdrop-blur-xs transition-colors">
        Lihat Hasil Sementara
      </a>
    </div>

  </div>
</section>

<!-- Candidates List Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
  <div class="text-center max-w-2xl mx-auto mb-12">
    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 tracking-tight">Kandidat Ketua OSIS</h2>
    <p class="text-sm sm:text-base text-gray-500 mt-2">Kenali visi, misi, dan program kerja calon pemimpin sekolah sebelum menentukan pilihan terbaikmu.</p>
  </div>

  <?php if (empty($candidates)): ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center max-w-md mx-auto shadow-xs">
      <div class="w-16 h-16 mx-auto rounded-full bg-pink-50 text-pink-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-1">Kandidat Belum Ditetapkan</h3>
      <p class="text-sm text-gray-500">Panitia sedang mempersiapkan data kandidat untuk periode ini.</p>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <?php foreach ($candidates as $c): ?>
        <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-xs card-hover flex flex-col">
          <!-- Photo -->
          <div class="relative aspect-square bg-pink-50 overflow-hidden group">
            <img src="<?= candidate_photo($c['photo_filename']) ?>" alt="Foto <?= e($c['full_name']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
            <div class="absolute top-4 left-4">
              <span class="inline-flex items-center justify-center w-10 h-10 rounded-full font-extrabold text-base text-white gradient-pink shadow-md">
                <?= $c['order_number'] ?: '?' ?>
              </span>
            </div>
            <div class="absolute bottom-4 right-4">
              <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/95 text-pink-700 shadow-sm backdrop-blur-xs">
                Kelas <?= e($c['class']) ?>
              </span>
            </div>
          </div>

          <!-- Body -->
          <div class="p-6 flex-1 flex flex-col justify-between">
            <div>
              <h3 class="text-xl font-bold text-gray-900 mb-2 leading-snug">
                <?= e($c['full_name']) ?>
              </h3>
              
              <?php if (!empty($c['vision'])): ?>
                <div class="mb-4">
                  <span class="text-xs font-semibold uppercase tracking-wider text-pink-600 block mb-1">Visi Singkat</span>
                  <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
                    <?= e(str_limit($c['vision'], 140)) ?>
                  </p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Action buttons -->
            <div class="pt-4 border-t border-gray-100 flex items-center gap-3">
              <a href="<?= url('kandidat/' . $c['id']) ?>" class="flex-1 py-2.5 px-4 rounded-xl border border-pink-200 text-pink-600 text-center font-semibold text-sm hover:bg-pink-50 transition-colors">
                Detail &amp; Visi Misi
              </a>
              <a href="<?= url('vote') ?>" class="py-2.5 px-4 rounded-xl gradient-pink text-white text-center font-semibold text-sm hover:brightness-105 shadow-sm transition-all">
                Pilih
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Mobile Sticky Bottom CTA -->
<div class="md:hidden fixed bottom-0 inset-x-0 p-3 bg-white/95 backdrop-blur-md border-t border-gray-200 z-20 shadow-lg">
  <a href="<?= url('vote') ?>" class="block w-full py-3.5 rounded-full text-center text-white font-bold gradient-pink shadow-md text-base">
    VOTE SEKARANG
  </a>
</div>
<div class="md:hidden h-16"></div>
