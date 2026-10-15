<div class="max-w-2xl mx-auto space-y-6">

  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs">
    <div class="border-b border-gray-100 pb-5 mb-6">
      <h2 class="text-xl font-bold text-gray-900">Pengaturan Akun Admin</h2>
      <p class="text-xs text-gray-500 mt-1">
        Perbarui nama, alamat email login, atau ganti kata sandi akun administrator Anda.
      </p>
    </div>

    <form action="<?= url('admin/akun') ?>" method="POST" class="space-y-6">
      <?= csrf_field() ?>

      <!-- Profil Admin -->
      <div class="space-y-4">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Informasi Dasar</h3>

        <div>
          <label for="full_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Nama Lengkap Administrator *
          </label>
          <input type="text" id="full_name" name="full_name" value="<?= e(old('full_name', $admin['full_name'] ?? '')) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
        </div>

        <div>
          <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Alamat Email Login *
          </label>
          <input type="email" id="email" name="email" value="<?= e(old('email', $admin['email'] ?? '')) ?>" required class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
          <span class="text-[11px] text-gray-400 mt-1 block">Email ini digunakan untuk masuk ke panel administrator.</span>
        </div>
      </div>

      <!-- Ganti Password -->
      <div class="pt-6 border-t border-gray-100 space-y-4">
        <div>
          <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Ganti Kata Sandi</h3>
          <p class="text-xs text-gray-500 mt-0.5">Biarkan kolom-kolom password ini kosong jika Anda tidak ingin mengubah kata sandi.</p>
        </div>

        <div>
          <label for="current_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
            Kata Sandi Saat Ini
          </label>
          <input type="password" id="current_password" name="current_password" placeholder="Masukkan password lama" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="new_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Kata Sandi Baru
            </label>
            <input type="password" id="new_password" name="new_password" placeholder="Minimal 8 karakter" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
          </div>

          <div>
            <label for="confirm_password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
              Konfirmasi Sandi Baru
            </label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Ulangi kata sandi baru" class="w-full px-4 py-3 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100">
          </div>
        </div>
        <span class="text-[11px] text-gray-400 block">Sandi baru harus memiliki minimal 8 karakter dengan kombinasi huruf dan angka.</span>
      </div>

      <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
          Simpan Perubahan
        </button>
      </div>

    </form>
  </div>

</div>
