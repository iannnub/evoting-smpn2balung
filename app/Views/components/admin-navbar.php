<header class="bg-white border-b border-gray-200 sticky top-0 z-20 shadow-xs">
  <div class="px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <!-- Mobile hamburger -->
      <button id="btn-sidebar-toggle" type="button" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 lg:hidden focus:outline-none" aria-label="Toggle Sidebar">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>

      <div class="flex items-center gap-2">
        <h1 class="text-base sm:text-lg font-bold text-gray-800 truncate"><?= isset($pageTitle) ? e($pageTitle) : 'Admin Area' ?></h1>
      </div>
    </div>

    <!-- Active period status pill -->
    <div class="flex items-center gap-3">
      <?php
      $activePeriod = (new \App\Models\Period())->getActive();
      if ($activePeriod):
      ?>
        <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-pink-50 text-pink-700 border border-pink-200">
          <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
          <span class="truncate max-w-[180px]"><?= e($activePeriod['name']) ?></span>
        </div>
      <?php else: ?>
        <a href="<?= url('admin/periode') ?>" class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-200">
          <span class="w-2 h-2 rounded-full bg-rose-500"></span>
          <span>Belum Ada Periode Aktif</span>
        </a>
      <?php endif; ?>

      <a href="<?= url('admin/akun') ?>" class="flex items-center gap-2 p-1.5 rounded-lg hover:bg-gray-100 transition-colors text-sm text-gray-700">
        <div class="w-8 h-8 rounded-full bg-pink-100 text-pink-700 flex items-center justify-center font-bold text-xs uppercase">
          <?= substr((string) \App\Core\Session::get('admin_name', 'A'), 0, 1) ?>
        </div>
      </a>
    </div>
  </div>
</header>
