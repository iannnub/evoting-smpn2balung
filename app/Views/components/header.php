<header class="bg-white/95 backdrop-blur-md border-b border-gray-100 sticky top-0 z-30 shadow-xs">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-18">
      
      <!-- Brand & Logo -->
      <a href="<?= url() ?>" class="flex items-center gap-3 group">
        <img src="<?= asset('img/logo.png') ?>" alt="Logo <?= e(config('app.school')) ?>" class="h-12 w-auto object-contain transition-transform group-hover:scale-105">
        <div>
          <span class="block text-xs font-semibold tracking-wider text-pink-600 uppercase">E-VOTING OSIS</span>
          <span class="block text-lg font-bold text-gray-900 group-hover:text-pink-600 transition-colors leading-tight"><?= e(config('app.school')) ?></span>
          <span class="block text-xs text-gray-400"><?= e(config('app.region')) ?></span>
        </div>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
        <a href="<?= url() ?>" class="transition-colors <?= current_path_is('/') ? 'text-pink-600 font-semibold' : 'text-gray-600 hover:text-pink-600' ?>">Beranda</a>
        <a href="<?= url('hasil') ?>" class="transition-colors <?= current_path_is('/hasil') ? 'text-pink-600 font-semibold' : 'text-gray-600 hover:text-pink-600' ?>">Hasil Sementara</a>
        <a href="<?= url('admin') ?>" class="text-gray-400 hover:text-gray-600 text-xs">Admin Panel</a>
        <a href="<?= url('vote') ?>" class="inline-flex items-center justify-center px-5 py-2.5 rounded-full text-white font-semibold text-sm gradient-pink shadow-md hover:shadow-lg hover:brightness-105 transition-all">
          Vote Sekarang
        </a>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="flex md:hidden">
        <button id="btn-mobile-menu" type="button" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none" aria-label="Menu">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Dropdown -->
  <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 pt-3 pb-5 space-y-2">
    <a href="<?= url() ?>" class="block px-3 py-2 rounded-lg text-base font-medium <?= current_path_is('/') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' ?>">Beranda</a>
    <a href="<?= url('hasil') ?>" class="block px-3 py-2 rounded-lg text-base font-medium <?= current_path_is('/hasil') ? 'bg-pink-50 text-pink-600' : 'text-gray-700 hover:bg-gray-50' ?>">Hasil Sementara</a>
    <a href="<?= url('admin') ?>" class="block px-3 py-2 rounded-lg text-sm text-gray-500 hover:bg-gray-50">Admin Panel</a>
    <div class="pt-2">
      <a href="<?= url('vote') ?>" class="block w-full text-center py-3 rounded-xl text-white font-semibold gradient-pink shadow-md">
        Vote Sekarang
      </a>
    </div>
  </div>
</header>
