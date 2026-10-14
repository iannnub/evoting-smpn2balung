<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

  <?php if (!$period): ?>
    <div class="bg-white rounded-3xl p-10 text-center border border-gray-100 shadow-sm">
      <div class="w-16 h-16 mx-auto rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mb-4">
        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <h2 class="text-xl font-bold text-gray-900 mb-2">Tidak Ada Periode Aktif</h2>
      <p class="text-gray-500 text-sm mb-6">Pemilihan belum dimulai atau sedang ditutup oleh pihak sekolah.</p>
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full gradient-pink text-white font-semibold text-sm">Kembali ke Beranda</a>
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
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full gradient-pink text-white font-semibold text-sm">Kembali ke Beranda</a>
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
      <a href="<?= url() ?>" class="px-6 py-2.5 rounded-full gradient-pink text-white font-semibold text-sm">Kembali ke Beranda</a>
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

    <!-- 4-Step Indicator -->
    <div class="flex items-center justify-between max-w-md mx-auto mb-10 text-xs font-semibold">
      <div class="step-item is-active" id="step-nav-1">
        <div id="indicator-step-1" class="step-circle bg-pink-600 text-white shadow-xs">1</div>
        <span class="mt-1 block text-gray-700">Data Siswa</span>
      </div>
      <div class="step-item" id="step-nav-2">
        <div id="indicator-step-2" class="step-circle bg-gray-200 text-gray-500">2</div>
        <span class="mt-1 block text-gray-400">Pilih Kandidat</span>
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
    <div id="vote-form" data-base-url="<?= url() ?>" data-require-nisn="<?= !empty($period['require_nisn_verification']) ? '1' : '0' ?>" class="bg-white rounded-3xl p-6 sm:p-10 border border-gray-100 shadow-sm">
      
      <!-- STEP 1: IDENTIFIKASI SISWA -->
      <div id="step-1" class="space-y-6">
        <div>
          <h2 class="text-lg font-bold text-gray-900">Langkah 1: Temukan Nama Siswa</h2>
          <p class="text-xs sm:text-sm text-gray-500">Ketik minimal 3 huruf nama lengkap Anda untuk mencari dalam Daftar Pemilih Tetap (DPT).</p>
        </div>

        <input type="hidden" id="selected-student-id" name="student_id" value="">

        <!-- Search Input -->
        <div class="relative">
          <div class="relative">
            <input type="text" id="student-search" autocomplete="off" placeholder="Ketik nama lengkap atau NISN Anda di sini..." class="w-full px-4 py-3.5 pl-11 rounded-2xl border-2 border-gray-200 focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 text-sm font-medium transition-all">
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
          <button type="button" id="btn-step1-next" disabled class="px-8 py-3.5 rounded-full text-white font-bold text-sm gradient-pink shadow-md opacity-50 cursor-not-allowed transition-all">
            Lanjut Pilih Kandidat →
          </button>
        </div>
      </div>

      <!-- STEP 2: PILIH KANDIDAT -->
      <div id="step-2" class="hidden space-y-6">
        <div>
          <h2 class="text-lg font-bold text-gray-900">Langkah 2: Tentukan Pilihan Anda</h2>
          <p class="text-xs sm:text-sm text-gray-500">Pilih salah satu kandidat Ketua &amp; Wakil Ketua OSIS di bawah ini.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <?php foreach ($candidates as $c): ?>
            <div class="relative candidate-select-card" data-name="<?= e($c['full_name']) ?>" data-photo="<?= candidate_photo($c['photo_filename']) ?>">
              <input type="radio" name="candidate_id" id="cand-<?= $c['id'] ?>" value="<?= $c['id'] ?>" class="candidate-radio sr-only">
              <label for="cand-<?= $c['id'] ?>" class="block p-4 rounded-2xl border-2 border-gray-200 cursor-pointer transition-all hover:border-pink-300 hover:bg-pink-50/30">
                <div class="flex items-center gap-4">
                  <!-- Photo -->
                  <div class="relative w-16 h-16 rounded-xl overflow-hidden bg-pink-100 shrink-0">
                    <img src="<?= candidate_photo($c['photo_filename']) ?>" alt="<?= e($c['full_name']) ?>" class="w-full h-full object-cover">
                    <span class="absolute top-1 left-1 w-6 h-6 rounded-full bg-pink-600 text-white text-xs font-bold flex items-center justify-center">
                      <?= $c['order_number'] ?>
                    </span>
                  </div>

                  <!-- Details -->
                  <div class="flex-1 truncate">
                    <span class="inline-block text-xs font-bold px-2 py-0.5 rounded-full bg-pink-100 text-pink-700 mb-0.5">
                      Kelas <?= e($c['class']) ?>
                    </span>
                    <h3 class="font-bold text-gray-900 text-base truncate"><?= e($c['full_name']) ?></h3>
                    <p class="text-xs text-gray-500 line-clamp-1 mt-0.5"><?= e(str_limit($c['vision'], 40)) ?></p>
                  </div>
                </div>
              </label>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
          <button type="button" id="btn-step2-back" class="px-6 py-2.5 rounded-full border border-gray-200 text-gray-600 text-sm font-semibold hover:bg-gray-50 transition-colors">
            ← Kembali
          </button>
          <button type="button" id="btn-step2-next" disabled class="px-8 py-3.5 rounded-full text-white font-bold text-sm gradient-pink shadow-md opacity-50 cursor-not-allowed transition-all">
            Lanjut ke Konfirmasi →
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
          <a href="<?= url('hasil') ?>" class="w-full sm:w-auto px-8 py-3.5 rounded-full gradient-pink text-white font-bold text-sm shadow-md hover:brightness-105 transition-all">
            Lihat Hasil Sementara →
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
          <button type="button" id="btn-modal-submit" class="flex-1 py-3 rounded-xl gradient-pink text-white font-bold text-sm shadow-md hover:brightness-105 transition-all">
            Ya, Simpan Pilihan Saya
          </button>
        </div>

      </div>
    </div>

  <?php endif; ?>

</div>

<script src="<?= asset('js/vote.js') ?>"></script>
