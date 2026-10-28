<header class="liquid-glass border-b border-gray-100/80 sticky top-0 z-30 shadow-xs">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between h-18">
      
      <!-- Brand & Logo -->
      <a href="<?= url() ?>" class="flex items-center gap-3 group">
        <img src="<?= asset('img/logo.png') ?>" alt="Logo <?= e(config('app.school')) ?>" width="48" height="48" class="h-12 w-auto object-contain transition-transform group-hover:scale-105">
        <div>
          <span class="block text-xs font-semibold tracking-wider text-pink-600 uppercase">E-VOTING OSIS</span>
          <span class="block text-lg font-bold text-gray-900 group-hover:text-pink-600 transition-colors leading-tight"><?= e(config('app.school')) ?></span>
          <span class="block text-xs text-slate-500 font-medium"><?= e(config('app.region')) ?></span>
        </div>
      </a>

      <!-- Desktop Navigation -->
      <nav class="hidden md:flex items-center gap-2 text-sm font-semibold" aria-label="Navigasi Utama">
        <a href="<?= url() ?>" class="px-3.5 py-2 rounded-xl transition-all <?= current_path_is('/') ? 'text-pink-600 bg-pink-50 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' ?>">
          Beranda
        </a>
        <a href="<?= url('vote') ?>" class="px-3.5 py-2 rounded-xl transition-all <?= current_path_is('/vote') ? 'text-pink-600 bg-pink-50 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' ?>">
          Bilik Suara
        </a>
        <a href="<?= url('hasil') ?>" class="px-3.5 py-2 rounded-xl transition-all <?= current_path_is('/hasil') ? 'text-pink-600 bg-pink-50 font-bold' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' ?>">
          Hasil Suara
        </a>
        <a href="<?= url('vote') ?>" class="ml-2 inline-flex items-center justify-center gap-2 px-5 py-2 rounded-xl text-white font-bold text-xs sm:text-sm bg-pink-600 hover:bg-pink-700 shadow-sm apple-btn">
          <span>Mulai Vote</span>
          <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
          </svg>
        </a>
      </nav>

      <!-- Mobile Menu Button -->
      <div class="flex md:hidden">
        <button id="btn-mobile-menu" type="button" class="p-2.5 rounded-xl text-gray-700 hover:text-gray-900 hover:bg-gray-100 focus:outline-none min-h-[44px] min-w-[44px] flex items-center justify-center transition-colors" aria-label="Menu Navigasi" aria-expanded="false" aria-controls="mobile-menu">
          <svg id="icon-menu-open" class="h-6 w-6 block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <svg id="icon-menu-close" class="h-6 w-6 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
      </div>

    </div>
  </div>

  <!-- Mobile Dropdown -->
  <div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white px-4 pt-3 pb-5 space-y-2 shadow-lg">
    <a href="<?= url() ?>" class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold <?= current_path_is('/') ? 'bg-pink-50 text-pink-600 font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">Beranda</a>
    <a href="<?= url('vote') ?>" class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold <?= current_path_is('/vote') ? 'bg-pink-50 text-pink-600 font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">Bilik Suara</a>
    <a href="<?= url('hasil') ?>" class="block px-3.5 py-2.5 rounded-xl text-sm font-semibold <?= current_path_is('/hasil') ? 'bg-pink-50 text-pink-600 font-bold' : 'text-gray-700 hover:bg-gray-50' ?>">Hasil Suara</a>
    <div class="pt-2">
      <a href="<?= url('vote') ?>" class="block w-full text-center py-3 rounded-xl text-white font-bold text-sm bg-pink-600 hover:bg-pink-700 shadow-sm transition-colors">
        Mulai Vote
      </a>
    </div>
  </div>
</header>
