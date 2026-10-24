<footer class="bg-white border-t border-gray-100 py-10 mt-auto text-sm text-slate-600">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <img src="<?= asset('img/logo.png') ?>" alt="Logo <?= e(config('app.school')) ?>" width="32" height="32" class="h-8 w-auto">
      <div>
        <p class="font-semibold text-gray-800"><?= e(config('app.school')) ?></p>
        <p class="text-xs text-slate-500 font-medium">Sistem Pemilihan Ketua & Wakil Ketua OSIS</p>
      </div>
    </div>
    <div class="text-xs text-slate-500 font-medium text-center md:text-right">
      <p>&copy; <?= date('Y') ?> OSIS <?= e(config('app.school')) ?>. Hak Cipta Dilindungi.</p>
      <p class="mt-1">Dirancang untuk pemilihan jujur, transparan, dan berkelanjutan.</p>
    </div>
  </div>
</footer>
