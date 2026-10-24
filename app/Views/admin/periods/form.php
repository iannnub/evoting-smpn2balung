<div class="max-w-2xl mx-auto space-y-6">

  <div class="flex items-center justify-between">
    <a href="<?= url('admin/periode') ?>" class="text-xs text-pink-600 hover:underline flex items-center gap-1 font-semibold">
      Kembali ke Daftar Periode
    </a>
  </div>

  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs">
    <h2 class="text-xl font-bold text-gray-900 mb-6">
      <?= $isEdit ? 'Edit Periode Pemilihan' : 'Buat Periode Pemilihan Baru' ?>
    </h2>

    <form action="<?= $isEdit ? url('admin/periode/' . $period['id']) : url('admin/periode') ?>" method="POST" class="space-y-6">
      <?= csrf_field() ?>

      <div>
        <label for="name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Nama Periode *
        </label>
        <input type="text" id="name" name="name" value="<?= e(old('name', $period['name'] ?? 'Pemilihan OSIS ' . date('Y') . '/' . (date('Y') + 1))) ?>" required placeholder="Contoh: Pemilihan OSIS 2026/2027" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label for="start_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Tanggal Mulai *
          </label>
          <input type="date" id="start_date" name="start_date" value="<?= e(old('start_date', $period['start_date'] ?? date('Y-m-d'))) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
        </div>

        <div>
          <label for="end_date" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Tanggal Selesai (Opsional)
          </label>
          <input type="date" id="end_date" name="end_date" value="<?= e(old('end_date', $period['end_date'] ?? '')) ?>" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
        </div>
      </div>

      <!-- Toggles -->
      <div class="space-y-4 pt-2">
        <label class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-gray-200 cursor-pointer hover:bg-slate-100 transition-colors">
          <input type="checkbox" name="is_results_visible" value="1" <?= (!empty($period['is_results_visible']) || !$isEdit) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 rounded text-pink-600 focus:ring-pink-500">
          <div>
            <span class="block text-sm font-bold text-gray-800">Tampilkan Hasil Pemilihan ke Publik</span>
            <span class="block text-xs text-gray-500 mt-0.5">Jika dicentang, siswa dapat memantau perolehan suara secara real-time di halaman Hasil.</span>
          </div>
        </label>

        <label class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-gray-200 cursor-pointer hover:bg-slate-100 transition-colors">
          <input type="checkbox" name="is_voting_open" value="1" <?= (!empty($period['is_voting_open']) || !$isEdit) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 rounded text-pink-600 focus:ring-pink-500">
          <div>
            <span class="block text-sm font-bold text-gray-800">Buka Bilik Suara (Voting Aktif)</span>
            <span class="block text-xs text-gray-500 mt-0.5">Jika tidak dicentang, siswa tidak dapat submit vote (bilik suara ditutup sementara).</span>
          </div>
        </label>

        <label class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-gray-200 cursor-pointer hover:bg-slate-100 transition-colors">
          <input type="checkbox" name="require_nisn_verification" value="1" <?= !empty($period['require_nisn_verification']) ? 'checked' : '' ?> class="mt-0.5 w-4 h-4 rounded text-pink-600 focus:ring-pink-500">
          <div>
            <span class="block text-sm font-bold text-gray-800">Wajibkan Verifikasi 4 Digit Terakhir NISN</span>
            <span class="block text-xs text-gray-500 mt-0.5">Pengaman ekstra: siswa harus memasukkan 4 angka terakhir NISN mereka sebelum memilih.</span>
          </div>
        </label>

        <?php if (!$isEdit): ?>
          <label class="flex items-start gap-3 p-4 rounded-2xl bg-pink-50 border border-pink-200 cursor-pointer">
            <input type="checkbox" name="is_active" value="1" checked class="mt-0.5 w-4 h-4 rounded text-pink-600 focus:ring-pink-500">
            <div>
              <span class="block text-sm font-bold text-pink-900">Jadikan Periode Ini Sebagai Periode Aktif</span>
              <span class="block text-xs text-pink-700 mt-0.5">Periode lama akan otomatis dinonaktifkan sebagai arsip.</span>
            </div>
          </label>
        <?php endif; ?>
      </div>

      <div class="pt-4 flex items-center justify-end gap-3">
        <a href="<?= url('admin/periode') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
          <?= $isEdit ? 'Simpan Perubahan' : 'Buat Periode Sekarang' ?>
        </button>
      </div>

    </form>
  </div>

</div>
