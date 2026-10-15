<!-- Hero Section -->
<section class="relative overflow-hidden bg-slate-50 border-b border-gray-100 pt-12 pb-20 sm:pt-20 sm:pb-28">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
    
    <!-- School Badge -->
    <div class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white border border-pink-200 shadow-xs mb-6">
      <img src="<?= asset('img/logo.png') ?>" alt="Logo <?= e(config('app.school')) ?>" class="w-6 h-6 object-contain">
      <span class="text-xs sm:text-sm font-bold text-gray-800 tracking-wide uppercase">
        <?= e(config('app.school')) ?> &bull; <?= e(config('app.region')) ?>
      </span>
    </div>

    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-gray-950 tracking-tight max-w-4xl mx-auto leading-tight">
      Pemilihan Ketua &amp; Wakil Ketua OSIS
    </h1>
    
    <p class="mt-4 text-base sm:text-xl text-gray-600 max-w-2xl mx-auto font-medium">
      <?= $period ? e($period['name']) : 'Periode Pemilihan Aktif' ?>
    </p>

    <!-- Period Status Badge -->
    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
      <?php if (!$period): ?>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-rose-50 text-rose-800 border border-rose-200 text-xs sm:text-sm font-semibold">
          Belum Ada Periode Aktif
        </span>
      <?php elseif (empty($period['is_voting_open'])): ?>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-amber-50 text-amber-800 border border-amber-200 text-xs sm:text-sm font-semibold">
          <span class="w-2 h-2 rounded-full bg-amber-500"></span>
          Pemungutan Suara Sedang Ditutup
        </span>
      <?php else: ?>
        <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs sm:text-sm font-semibold">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          Pemungutan Suara Sedang Berlangsung
        </span>
      <?php endif; ?>
    </div>

    <!-- Action Buttons -->
    <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4 max-w-md mx-auto sm:max-w-none">
      <a href="<?= url('vote') ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-2xl bg-pink-600 text-white font-bold text-base shadow-lg shadow-pink-600/25 hover:bg-pink-700 hover:shadow-xl transition-all min-h-[48px]">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>Masuk ke Bilik Suara</span>
      </a>
      <a href="<?= url('hasil') ?>" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 rounded-2xl bg-white text-gray-800 border border-gray-200 font-bold text-base hover:bg-gray-50 hover:border-gray-300 transition-all shadow-xs min-h-[48px]">
        <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
        </svg>
        <span>Lihat Hasil Suara</span>
      </a>
    </div>

  </div>
</section>

<!-- Candidates List Section -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
  <div class="mb-12 max-w-4xl mx-auto text-center">
    <span class="text-xs font-extrabold uppercase tracking-wider text-pink-600 block mb-1">Daftar Paslon</span>
    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kandidat Ketua OSIS</h2>
    <p class="text-sm sm:text-base text-gray-500 mt-2 max-w-xl mx-auto">
      Pelajari visi dan misi setiap pasangan calon sebelum menentukan pilihanmu di bilik suara.
    </p>
  </div>

  <?php if (empty($candidates)): ?>
    <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center max-w-md mx-auto shadow-xs">
      <div class="w-16 h-16 mx-auto rounded-full bg-pink-50 text-pink-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
      </div>
      <h3 class="text-lg font-bold text-gray-800 mb-1">Kandidat Belum Ditetapkan</h3>
      <p class="text-sm text-gray-500">Panitia sedang mempersiapkan data kandidat untuk periode ini.</p>
    </div>
  <?php else: ?>
    <?php 
      // Responsive grid centered when exactly 2 candidates
      $gridClass = count($candidates) === 2 
        ? 'max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 justify-center' 
        : 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8';
    ?>
    <div class="<?= $gridClass ?>">
      <?php foreach ($candidates as $c): ?>
        <div class="group bg-white rounded-3xl overflow-hidden border border-gray-200/80 shadow-xs hover:shadow-xl transition-all duration-300 flex flex-col">
          
          <!-- Explicit photo container height to prevent image collapse or overflow -->
          <div class="relative h-64 sm:h-72 w-full overflow-hidden bg-pink-50">
            <img 
              src="<?= candidate_photo($c['photo_filename'], (int) $c['order_number']) ?>" 
              alt="Foto <?= e($c['full_name']) ?>" 
              class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105" 
              loading="lazy"
            >
            
            <!-- Order Number Badge -->
            <div class="absolute top-4 left-4">
              <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl font-black text-xl text-white bg-pink-600 shadow-md">
                <?= $c['order_number'] ?: '?' ?>
              </span>
            </div>

            <!-- Class Badge -->
            <div class="absolute bottom-4 right-4">
              <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-white/95 text-pink-700 shadow-xs backdrop-blur-xs">
                Kelas <?= e($c['class']) ?>
              </span>
            </div>
          </div>

          <!-- Card Body -->
          <div class="p-6 sm:p-7 flex-1 flex flex-col justify-between">
            <div>
              <span class="text-xs font-bold uppercase tracking-wider text-pink-600 block mb-1">
                Kandidat Nomor <?= $c['order_number'] ?>
              </span>
              <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug">
                <?= e($c['full_name']) ?>
              </h3>
              
              <?php if (!empty($c['vision'])): ?>
                <div class="mb-4">
                  <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 block mb-1">Visi Utama</span>
                  <p class="text-sm text-gray-600 line-clamp-3 leading-relaxed">
                    <?= e(str_limit($c['vision'], 140)) ?>
                  </p>
                </div>
              <?php endif; ?>
            </div>

            <!-- Action buttons -->
            <div class="pt-5 border-t border-gray-100 flex items-center gap-3">
              <a href="<?= url('kandidat/' . $c['id']) ?>" class="flex-1 py-3 px-4 rounded-xl border border-pink-200 text-pink-600 text-center font-bold text-sm hover:bg-pink-50 transition-colors">
                Detail Visi Misi
              </a>
              <a href="<?= url('vote') ?>" class="py-3 px-5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-center font-bold text-sm shadow-xs transition-colors">
                Pilih
              </a>
            </div>
          </div>

        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</section>

<!-- Voting Guide Section (Cara Memberikan Suara) -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
  <div class="rounded-3xl bg-white border border-gray-200/80 p-8 sm:p-12 shadow-xs">
    <div class="max-w-2xl mb-10">
      <span class="text-xs font-extrabold uppercase tracking-wider text-pink-600 block mb-1">Panduan Pemilihan</span>
      <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Cara Memberikan Suara</h2>
      <p class="text-sm sm:text-base text-gray-600 mt-2">
        Ikuti tiga langkah berikut untuk menggunakan hak suaramu secara sah dan rahasia.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
      <div class="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
        <div class="w-12 h-12 rounded-xl bg-pink-100 text-pink-700 flex items-center justify-center font-black text-lg mb-4">
          1
        </div>
        <h3 class="font-bold text-gray-900 text-base mb-2">Cari Nama Kamu</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          Buka bilik suara, lalu ketik minimal 3 huruf nama lengkap kamu sesuai data DPT sekolah.
        </p>
      </div>

      <div class="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
        <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-lg mb-4">
          2
        </div>
        <h3 class="font-bold text-gray-900 text-base mb-2">Verifikasi Identitas</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          Jika verifikasi diaktifkan panitia, masukkan 4 digit terakhir NISN kamu sebagai bukti pemilih sah.
        </p>
      </div>

      <div class="flex flex-col items-start p-6 rounded-2xl bg-slate-50 border border-slate-100">
        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg mb-4">
          3
        </div>
        <h3 class="font-bold text-gray-900 text-base mb-2">Pilih &amp; Konfirmasi</h3>
        <p class="text-sm text-gray-600 leading-relaxed">
          Pilih paslon yang kamu dukung, periksa kembali pada dialog konfirmasi, lalu simpan suara.
        </p>
      </div>
    </div>

    <div class="mt-10 pt-8 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-3 text-xs sm:text-sm text-gray-500">
        <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
        <span>Satu siswa hanya dapat memilih satu kali. Pilihan bersifat langsung, bebas, dan rahasia.</span>
      </div>
      <a href="<?= url('vote') ?>" class="inline-flex items-center gap-2 text-sm font-bold text-pink-600 hover:text-pink-700">
        <span>Mulai Memilih Sekarang</span>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
        </svg>
      </a>
    </div>

  </div>
</section>

<!-- Mobile Sticky Bottom CTA -->
<div class="md:hidden fixed bottom-0 inset-x-0 p-3 bg-white/95 backdrop-blur-md border-t border-gray-200 z-20 shadow-lg">
  <a href="<?= url('vote') ?>" class="block w-full py-3.5 rounded-full text-center text-white font-bold bg-pink-600 hover:bg-pink-700 shadow-sm text-base transition-colors">
    VOTE SEKARANG
  </a>
</div>
<div class="md:hidden h-16"></div>
