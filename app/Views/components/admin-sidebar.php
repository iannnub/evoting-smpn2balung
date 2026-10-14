<aside id="admin-sidebar" class="fixed inset-y-0 left-0 z-40 w-64 bg-gradient-to-b from-pink-600 via-pink-700 to-purple-800 text-white flex flex-col transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 shadow-xl">
  <!-- Brand -->
  <div class="h-20 flex items-center gap-3 px-6 border-b border-white/10">
    <img src="<?= asset('img/logo.png') ?>" alt="Logo" class="h-10 w-auto bg-white/10 p-1 rounded-lg">
    <div>
      <span class="block text-xs uppercase tracking-wider text-pink-200 font-semibold">PANEL ADMIN</span>
      <span class="block text-sm font-bold truncate leading-tight"><?= e(config('app.school')) ?></span>
    </div>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto text-sm">
    <a href="<?= url('admin/dashboard') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/dashboard') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
      </svg>
      Dashboard
    </a>

    <a href="<?= url('admin/periode') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/periode') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
      </svg>
      Periode Pemilihan
    </a>

    <a href="<?= url('admin/kandidat') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/kandidat') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
      </svg>
      Kelola Kandidat
    </a>

    <a href="<?= url('admin/siswa') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/siswa') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      Daftar Siswa / DPT
    </a>

    <a href="<?= url('admin/import') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/import') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
      </svg>
      Import DPT Excel
    </a>

    <a href="<?= url('admin/hasil') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/hasil') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
      <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>
      Hasil &amp; Laporan
    </a>

    <div class="pt-4 mt-4 border-t border-white/10">
      <a href="<?= url('admin/akun') ?>" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium transition-colors <?= current_path_is('/admin/akun') ? 'bg-white/20 text-white shadow-xs' : 'text-pink-100 hover:bg-white/10' ?>">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
        </svg>
        Pengaturan Akun
      </a>

      <a href="<?= url() ?>" target="_blank" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-medium text-pink-100 hover:bg-white/10 transition-colors">
        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
        </svg>
        Lihat Website Publik ↗
      </a>
    </div>
  </nav>

  <!-- User Card & Logout -->
  <div class="p-4 border-t border-white/10 bg-black/10">
    <div class="flex items-center justify-between">
      <div class="truncate mr-2">
        <span class="block text-xs text-pink-200">Login sebagai:</span>
        <span class="block text-sm font-semibold truncate"><?= e(\App\Core\Session::get('admin_name', 'Admin')) ?></span>
      </div>
      <form action="<?= url('admin/logout') ?>" method="POST">
        <?= csrf_field() ?>
        <button type="submit" title="Logout" class="p-2 rounded-lg bg-white/10 hover:bg-rose-600 text-white transition-colors" data-confirm="Keluar dari sesi admin?">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
          </svg>
        </button>
      </form>
    </div>
  </div>
</aside>
<div id="sidebar-overlay" class="hidden fixed inset-0 z-30 bg-black/50 lg:hidden"></div>
