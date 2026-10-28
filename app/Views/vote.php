<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <?php if (!$period): ?>
    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm">
      <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Periode Aktif</h2>
      <p class="text-gray-500 text-sm mb-6">Pemilihan belum dimulai atau sedang ditutup oleh pihak sekolah.</p>
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm transition-colors">Kembali ke Beranda</a>
    </div>
  <?php elseif (empty($period['is_voting_open'])): ?>
    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm">
      <div class="w-16 h-16 mx-auto rounded-full bg-amber-50 text-amber-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Bilik Suara Ditutup Sementara</h2>
      <p class="text-gray-500 text-sm mb-6">Panitia pemilihan sedang menghentikan proses pemungutan suara sementara waktu.</p>
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm transition-colors">Kembali ke Beranda</a>
    </div>
  <?php elseif (count($candidates) < 2): ?>
    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm">
      <div class="w-16 h-16 mx-auto rounded-full bg-pink-50 text-pink-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Kandidat Belum Memenuhi Syarat</h2>
      <p class="text-gray-500 text-sm mb-6">Dibutuhkan minimal 2 kandidat terdaftar untuk membuka bilik suara digital.</p>
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-semibold text-sm transition-colors">Kembali ke Beranda</a>
    </div>
  <?php else: ?>

    <!-- Title & Subtitle -->
    <div class="text-center mb-8">
      <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-700 uppercase tracking-wider mb-2">
        <?= e($period['name']) ?>
      </span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Bilik Suara Digital</h1>
      <p class="text-sm text-gray-500 mt-1">Satu siswa hanya dapat memberikan suara sebanyak satu kali.</p>
    </div>

<?php $previewStep2 = (isset($_GET['step']) && $_GET['step'] == '2'); ?>
    <!-- 4-Step Indicator -->
    <div class="flex items-center justify-between max-w-md mx-auto mb-10 text-xs font-semibold">
      <div class="step-item <?= $previewStep2 ? '' : 'is-active' ?>" id="step-nav-1">
        <div id="indicator-step-1" class="step-circle <?= $previewStep2 ? 'bg-gray-200 text-gray-500' : 'bg-pink-600 text-white shadow-xs' ?>">1</div>
        <span class="mt-1 block <?= $previewStep2 ? 'text-gray-400' : 'text-gray-700' ?>">Data Pemilih</span>
      </div>
      <div class="step-item <?= $previewStep2 ? 'is-active' : '' ?>" id="step-nav-2">
        <div id="indicator-step-2" class="step-circle <?= $previewStep2 ? 'bg-pink-600 text-white shadow-xs' : 'bg-gray-200 text-gray-500' ?>">2</div>
        <span class="mt-1 block <?= $previewStep2 ? 'text-gray-700' : 'text-gray-400' ?>">Pilih Kandidat</span>
      </div>
      <div class="step-item" id="step-nav-3">
        <div id="indicator-step-3" class="step-circle bg-gray-200 text-gray-500">3</div>
        <span class="mt-1 block text-gray-400">Konfirmasi</span>
      </div>
      <div class="step-item" id="step-nav-4">
        <div id="indicator-step-4" class="step-circle bg-gray-200 text-gray-500">4</div>
        <span class="mt-1 block text-gray-400">Selesai</span>
      </div>
    </div>

    <!-- Vote Form Container -->
    <div id="vote-form" data-base-url="<?= rtrim(url(), '/') ?>" data-require-nisn="<?= !empty($period['require_nisn_verification']) ? '1' : '0' ?>" class="liquid-glass rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm">
      
      <!-- STEP 1: IDENTIFIKASI PEMILIH -->
      <div id="step-1" class="<?= $previewStep2 ? 'hidden' : '' ?> space-y-6">
        <div>
          <h2 class="text-lg font-bold text-gray-900">Langkah 1: Temukan Nama Pemilih</h2>
          <p class="text-xs sm:text-sm text-gray-500">Cukup ketik nama Anda (Siswa atau Guru). Anda tidak perlu menghafal NISN.</p>
        </div>

        <input type="hidden" id="selected-student-id" name="student_id" value="">

        <!-- Search Input -->
        <div class="relative">
          <div class="relative">
            <input type="text" id="student-search" autocomplete="off" placeholder="Ketik nama Anda di sini (contoh: Fatir, Nabila, atau Rofik)..." class="w-full px-4 py-3.5 pl-11 rounded-2xl border-2 border-gray-200 focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 text-sm font-medium transition-all">
            <svg class="w-5 h-5 text-gray-400 absolute left-3.5 top-4 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
          </div>

          <!-- Autocomplete Dropdown List -->
          <div id="student-results" class="hidden absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-2xl shadow-xl z-30 autocomplete-list"></div>
        </div>

        <!-- Selected Student Card -->
        <div id="selected-student-card" class="hidden p-5 rounded-2xl bg-pink-50/80 border border-pink-200 flex items-center justify-between">
          <div class="flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-full bg-pink-600 text-white flex items-center justify-center font-bold text-sm">
              ✓
            </div>
            <div>
              <h3 id="selected-student-name" class="font-bold text-gray-900 text-base"></h3>
              <div class="flex items-center gap-2 text-xs text-pink-700 mt-0.5">
                <span id="selected-student-class" class="font-semibold"></span>
                <span>•</span>
                <span id="selected-student-nis"></span>
              </div>
            </div>
          </div>
          <button type="button" id="btn-change-student" class="text-xs font-semibold text-pink-700 hover:text-pink-900 px-3 py-1.5 rounded-lg bg-pink-100 hover:bg-pink-200 transition-colors">
            Ganti
          </button>
        </div>

        <!-- Optional NISN Verification Box -->
        <?php if (!empty($period['require_nisn_verification'])): ?>
          <div id="nisn-verification-box" class="hidden p-4 rounded-2xl bg-amber-50 border border-amber-200 space-y-2">
            <label for="nisn-last4" class="block text-xs font-bold text-amber-900 uppercase tracking-wider">
              Verifikasi: Masukkan 4 Digit Terakhir NISN Anda
            </label>
            <input type="text" id="nisn-last4" maxlength="4" placeholder="Contoh: 5678" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-200 text-sm font-semibold tracking-widest text-gray-800">
            <p class="text-xs text-amber-700">Untuk memastikan identitas pemilih sah sesuai data rapor.</p>
          </div>
        <?php endif; ?>

        <!-- Next button -->
        <div class="pt-4 flex justify-end">
          <button type="button" id="btn-step1-next" disabled class="px-8 py-3.5 rounded-full text-white font-bold text-sm bg-pink-600 hover:bg-pink-700 shadow-sm opacity-50 cursor-not-allowed apple-btn">
            Lanjut Pilih Kandidat
          </button>
        </div>
      </div>

      <!-- STEP 2: PILIH KANDIDAT -->
      <div id="step-2" class="<?= $previewStep2 ? '' : 'hidden' ?> space-y-6">
        <div>
          <h2 class="text-lg font-bold text-gray-900">Langkah 2: Tentukan Pilihan Anda</h2>
          <p class="text-xs sm:text-sm text-gray-500">Pilih salah satu kandidat Ketua &amp; Wakil Ketua OSIS di bawah ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-stretch">
          <?php foreach ($candidates as $c): ?>
            <div class="relative candidate-select-card h-full" data-name="<?= e($c['full_name']) ?>" data-photo="<?= candidate_photo($c['photo_filename'], (int) $c['order_number']) ?>">
              <input type="radio" name="candidate_id" id="cand-<?= $c['id'] ?>" value="<?= $c['id'] ?>" class="candidate-radio sr-only">
              <label for="cand-<?= $c['id'] ?>" class="candidate-card-label flex flex-col justify-between h-full p-5 sm:p-6 rounded-3xl border-2 border-gray-200 bg-white cursor-pointer transition-all hover:border-pink-300 hover:shadow-lg">
                <div>
                  <!-- Header: Photo, Name, Order Number -->
                  <div class="flex items-center gap-4 pb-4 border-b border-gray-100">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24 rounded-2xl overflow-hidden bg-pink-100 shrink-0 border border-pink-200 shadow-xs">
                      <img src="<?= candidate_photo($c['photo_filename'], (int) $c['order_number']) ?>" alt="<?= e($c['full_name']) ?>" class="w-full h-full object-cover">
                      <span class="absolute top-1.5 left-1.5 w-7 h-7 rounded-xl bg-pink-600 text-white text-sm font-black flex items-center justify-center shadow-xs">
                        <?= $c['order_number'] ?>
                      </span>
                    </div>

                    <div class="flex-1 min-w-0">
                      <span class="inline-block text-xs font-bold px-2.5 py-0.5 rounded-full bg-pink-50 text-pink-700 border border-pink-100 mb-1">
                        Kelas <?= e($c['class']) ?>
                      </span>
                      <h3 class="font-extrabold text-gray-900 text-base sm:text-lg leading-snug break-words">
                        <?= e($c['full_name']) ?>
                      </h3>
                      <span class="text-xs text-gray-400 font-medium block mt-0.5">Paslon Nomor Urut <?= $c['order_number'] ?></span>
                    </div>

                    <div class="select-indicator shrink-0 w-7 h-7 rounded-full border-2 border-gray-300 flex items-center justify-center text-transparent transition-all">
                      <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                  </div>

                  <!-- Visi Section -->
                  <div class="mt-4 bg-slate-50 rounded-2xl p-4 border border-gray-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-pink-600 flex items-center gap-1.5 mb-1.5">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      Visi
                    </span>
                    <p class="text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                      <?= !empty($c['vision']) ? e($c['vision']) : '<span class="text-gray-400 italic">Visi belum diisi.</span>' ?>
                    </p>
                  </div>

                  <!-- Misi Section -->
                  <div class="mt-3 bg-slate-50 rounded-2xl p-4 border border-gray-100">
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-700 flex items-center gap-1.5 mb-2">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                      </svg>
                      Misi &amp; Program Kerja
                    </span>
                    <?php $missions = mission_items($c['mission'] ?? ''); ?>
                    <?php if (!empty($missions)): ?>
                      <ul class="space-y-2">
                        <?php foreach ($missions as $idx => $m): ?>
                          <li class="flex items-start gap-2.5 text-xs sm:text-sm text-gray-700 leading-relaxed">
                            <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-purple-100 text-purple-700 text-[10px] font-bold shrink-0 mt-0.5">
                              <?= $idx + 1 ?>
                            </span>
                            <span><?= e($m) ?></span>
                          </li>
                        <?php endforeach; ?>
                      </ul>
                    <?php else: ?>
                      <p class="text-xs text-gray-400 italic">Misi belum diisi.</p>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Card Footer / Select CTA -->
                <div class="mt-5 pt-4 border-t border-gray-100">
                  <div class="card-select-badge w-full py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold text-center transition-all bg-slate-100 text-gray-700 flex items-center justify-center gap-2">
                    <span>Pilih Paslon #<?= $c['order_number'] ?></span>
                  </div>
                </div>
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
          <button type="button" id="btn-step2-back" class="px-6 py-2.5 rounded-full border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 apple-btn">
            Kembali
          </button>
          <button type="button" id="btn-step2-next" disabled class="px-8 py-3.5 rounded-full text-white font-bold text-sm bg-pink-600 hover:bg-pink-700 shadow-sm opacity-50 cursor-not-allowed apple-btn">
            Lanjut ke Konfirmasi
          </button>
        </div>
      </div>

      <!-- STEP 4: SUKSES -->
      <div id="step-4-success" class="hidden py-8 text-center space-y-6">
        <div class="w-20 h-20 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shadow-inner">
          <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <div>
          <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 uppercase tracking-wider mb-2">Suara Berhasil Dicatat</span>
          <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900">Terima Kasih!</h2>
          <p class="text-sm sm:text-base text-gray-600 max-w-md mx-auto mt-2">
            Suara dari <strong id="success-student-name" class="text-gray-900"></strong> telah resmi tersimpan ke dalam sistem e-voting OSIS.
          </p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 border border-gray-100 max-w-sm mx-auto text-xs text-gray-500">
          Hak pilih Anda telah selesai digunakan. Satu suara Anda turut menentukan masa depan organisasi siswa di <?= e(config('app.school')) ?>.
        </div>

        <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
          <a href="<?= url('hasil') ?>" class="w-full sm:w-auto px-8 py-3.5 rounded-full bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
            Lihat Hasil Sementara
          </a>
          <a href="<?= url() ?>" class="w-full sm:w-auto px-6 py-3.5 rounded-full border border-gray-200 text-gray-600 font-semibold text-sm hover:bg-gray-50 transition-colors">
            Kembali ke Beranda
          </a>
        </div>
      </div>

    </div>

    <!-- STEP 3: MODAL KONFIRMASI -->
    <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 modal-backdrop">
      <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 modal-box text-center shadow-2xl space-y-6">
        
        <div>
          <span class="inline-block p-2 rounded-full bg-pink-100 text-pink-600 mb-2">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </span>
          <h3 class="text-xl font-bold text-gray-900">Konfirmasi Pilihan Anda</h3>
          <p class="text-xs text-gray-500 mt-1">Pastikan pilihan Anda sudah benar. Pilihan tidak dapat diubah setelah disimpan.</p>
        </div>

        <!-- Chosen candidate card in modal -->
        <div class="p-4 rounded-2xl bg-pink-50 border border-pink-200 flex items-center gap-4 text-left">
          <img id="modal-candidate-photo" src="" alt="" class="w-14 h-14 rounded-xl object-cover bg-white shrink-0 shadow-xs">
          <div class="truncate">
            <span class="text-xs font-semibold text-pink-700 block uppercase">Kandidat Pilihan</span>
            <h4 id="modal-candidate-name" class="font-bold text-gray-900 text-base truncate"></h4>
          </div>
        </div>

        <p class="text-xs text-gray-500 text-left bg-gray-50 p-3 rounded-xl">
          Pemilih: <strong id="modal-student-name" class="text-gray-800"></strong>
        </p>

        <div class="flex items-center gap-3 pt-2">
          <button type="button" id="btn-modal-cancel" class="flex-1 py-3 rounded-xl border border-gray-200 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition-colors">
            Batal
          </button>
          <button type="button" id="btn-modal-submit" class="flex-1 py-3 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
            Ya, Simpan Pilihan Saya
          </button>
        </div>

      </div>
    </div>

  <?php endif; ?>

</div>

<script src="<?= asset('js/vote.js') ?>"></script>
