<div class="max-w-3xl mx-auto space-y-6">

  <div class="flex items-center justify-between">
    <a href="<?= url('admin/kandidat') ?>" class="text-xs text-pink-600 hover:underline flex items-center gap-1 font-semibold">
      ← Kembali ke Daftar Kandidat
    </a>
    <span class="text-xs text-gray-400">Periode: <?= e($period['name'] ?? '-') ?></span>
  </div>

  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs">
    <h2 class="text-xl font-bold text-gray-900 mb-6">
      <?= $isEdit ? 'Edit Data Paslon / Kandidat' : 'Tambah Kandidat Baru' ?>
    </h2>

    <form action="<?= $isEdit ? url('admin/kandidat/' . $candidate['id']) : url('admin/kandidat') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
      <?= csrf_field() ?>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
          <label for="order_number" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Nomor Urut *
          </label>
          <input type="number" id="order_number" name="order_number" min="1" max="99" value="<?= e(old('order_number', $candidate['order_number'] ?? 1)) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 font-bold text-pink-600">
        </div>

        <div class="sm:col-span-2">
          <label for="class" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Kelas Kandidat *
          </label>
          <input type="text" id="class" name="class" value="<?= e(old('class', $candidate['class'] ?? '')) ?>" required placeholder="Contoh: 8A, 8B" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
          <span class="text-[11px] text-gray-400 mt-1 block">Format kelas: 7A - 9H (tanpa spasi atau dengan spasi otomatis dinormalisasi)</span>
        </div>
      </div>

      <div>
        <label for="full_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Nama Lengkap Kandidat *
        </label>
        <input type="text" id="full_name" name="full_name" value="<?= e(old('full_name', $candidate['full_name'] ?? '')) ?>" required placeholder="Contoh: Ahmad Fauzan" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
      </div>

      <!-- Photo upload -->
      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Foto Kandidat <?= $isEdit ? '(Biarkan kosong jika tidak diganti)' : '(Disarankan)' ?>
        </label>
        <div class="flex flex-col sm:flex-row items-center gap-6 p-4 rounded-2xl border-2 border-dashed border-pink-200 bg-pink-50/30">
          <div class="w-24 h-24 rounded-2xl bg-white border border-pink-100 shadow-xs overflow-hidden shrink-0 flex items-center justify-center">
            <?php if (!empty($candidate['photo_filename'])): ?>
              <img id="photo-preview" src="<?= candidate_photo_url($candidate['photo_filename']) ?>" alt="Preview" class="w-full h-full object-cover">
            <?php else: ?>
              <img id="photo-preview" src="<?= asset('img/default-avatar.svg') ?>" alt="Default" class="w-full h-full object-cover opacity-60">
            <?php endif; ?>
          </div>
          <div class="flex-1 w-full text-center sm:text-left">
            <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-pink-100 file:text-pink-700 hover:file:bg-pink-200 cursor-pointer">
            <span class="text-[11px] text-gray-400 mt-2 block">Format JPG, PNG, atau WebP. Maksimal 2MB. Foto akan otomatis dioptimasi & di-resize.</span>
          </div>
        </div>
      </div>

      <!-- Visi -->
      <div>
        <label for="vision" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Visi
        </label>
        <textarea id="vision" name="vision" rows="3" placeholder="Tuliskan visi kepemimpinan kandidat..." class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100"><?= e(old('vision', $candidate['vision'] ?? '')) ?></textarea>
      </div>

      <!-- Misi -->
      <div>
        <label for="mission" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Misi
        </label>
        <textarea id="mission" name="mission" rows="6" placeholder="Tuliskan butir-butir misi kandidat (pisahkan tiap poin dengan baris baru)..." class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 font-sans leading-relaxed"><?= e(old('mission', $candidate['mission'] ?? '')) ?></textarea>
        <span class="text-[11px] text-gray-400 mt-1 block">Tips: Tiap baris akan ditampilkan sebagai poin misi bernomor pada halaman profil kandidat.</span>
      </div>

      <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
        <a href="<?= url('admin/kandidat') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
          Batal
        </a>
        <button type="submit" class="px-6 py-2.5 rounded-xl gradient-pink text-white font-bold text-sm shadow-md hover:brightness-105 transition-all">
          <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Kandidat' ?>
        </button>
      </div>

    </form>
  </div>

</div>

<script>
document.getElementById('photo')?.addEventListener('change', function(e) {
  const file = e.target.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function(evt) {
      const preview = document.getElementById('photo-preview');
      if (preview) {
        preview.src = evt.target.result;
        preview.classList.remove('opacity-60');
      }
    };
    reader.readAsDataURL(file);
  }
});
</script>
