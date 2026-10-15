<div class="max-w-md w-full mx-auto">
  <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-xl border border-gray-100">
    
    <!-- Logo & Title -->
    <div class="text-center mb-8">
      <img src="<?= asset('img/logo.png') ?>" alt="Logo" class="h-16 w-auto mx-auto mb-3">
      <span class="inline-block text-xs uppercase font-bold tracking-wider text-pink-600 bg-pink-50 px-3 py-1 rounded-full mb-1">
        PANEL ADMINISTRATOR
      </span>
      <h1 class="text-xl sm:text-2xl font-extrabold text-gray-900 leading-tight">
        <?= e(config('app.school')) ?>
      </h1>
      <p class="text-xs text-gray-400 mt-1">Masuk untuk mengelola periode, kandidat, dan DPT siswa.</p>
    </div>

    <!-- Login Form -->
    <form action="<?= url('admin/login') ?>" method="POST" class="space-y-5">
      <?= csrf_field() ?>

      <div>
        <label for="email" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
          Email Administrator
        </label>
        <input type="email" id="email" name="email" value="<?= e(old('email', '')) ?>" placeholder="nama@smpn2balung.sch.id" required autofocus autocomplete="email" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 text-sm font-medium transition-all">
      </div>

      <div>
        <div class="flex items-center justify-between mb-1.5">
          <label for="password" class="block text-xs font-bold text-gray-700 uppercase tracking-wider">
            Kata Sandi
          </label>
        </div>
        <input type="password" id="password" name="password" placeholder="••••••••" required autocomplete="current-password" class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-pink-500 focus:outline-none focus:ring-4 focus:ring-pink-100 text-sm font-medium transition-all">
      </div>

      <button type="submit" class="w-full py-3.5 rounded-xl text-white font-bold text-sm bg-pink-600 hover:bg-pink-700 shadow-sm transition-colors">
        Masuk ke Panel Admin
      </button>

      <div class="pt-4 border-t border-gray-100 text-center">
        <a href="<?= url() ?>" class="text-xs text-pink-600 hover:underline font-medium">
          ← Kembali ke Halaman Pemilihan Publik
        </a>
      </div>
    </form>

  </div>
</div>
