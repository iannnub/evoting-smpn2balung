<div class="space-y-6">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Kelola Data Kandidat OSIS</h2>
      <p class="text-xs sm:text-sm text-gray-500">Daftarkan calon ketua &amp; wakil ketua OSIS lengkap dengan foto, visi, dan misi.</p>
    </div>
    
    <div class="flex items-center gap-3">
      <a href="<?= url('admin/kandidat/tambah') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
        + Tambah Kandidat
      </a>
    </div>
  </div>

  <!-- Filter Period Bar if multiple periods exist -->
  <?php if (count($allPeriods) > 1): ?>
    <div class="bg-white p-4 rounded-2xl border border-gray-100 flex items-center justify-between gap-4 text-sm">
      <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Periode Pemilihan:</span>
      <form method="GET" action="<?= url('admin/kandidat') ?>" class="flex items-center gap-2">
        <select name="period_id" onchange="this.form.submit()" class="px-3 py-1.5 rounded-lg border border-gray-200 text-sm font-semibold text-gray-800 focus:outline-none focus:border-pink-500">
          <?php foreach ($allPeriods as $p): ?>
            <option value="<?= $p['id'] ?>" <?= $p['id'] == $selectedPeriodId ? 'selected' : '' ?>>
              <?= e($p['name']) ?> <?= $p['is_active'] ? '(Aktif)' : '(Arsip)' ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>
  <?php endif; ?>

  <!-- Candidates Table -->
  <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xs">
    <?php if (empty($candidates)): ?>
      <div class="p-12 text-center text-gray-500 max-w-sm mx-auto">
        <div class="w-16 h-16 rounded-full bg-pink-50 text-pink-500 flex items-center justify-center mx-auto mb-3">
          <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <h3 class="font-bold text-gray-800 text-base mb-1">Belum Ada Kandidat</h3>
        <p class="text-xs text-gray-400 mb-4">Mulai dengan menambahkan kandidat pertama untuk periode ini.</p>
        <a href="<?= url('admin/kandidat/tambah') ?>" class="inline-flex px-4 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-sm transition-colors">+ Tambah Kandidat Sekarang</a>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
          <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500 tracking-wider border-b border-gray-200">
            <tr>
              <th class="py-4 px-6 text-center w-20">No. Urut</th>
              <th class="py-4 px-6">Foto &amp; Nama Lengkap</th>
              <th class="py-4 px-6">Kelas</th>
              <th class="py-4 px-6">Visi (Kutipan)</th>
              <th class="py-4 px-6 text-right">Suara Masuk</th>
              <th class="py-4 px-6 text-right">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($candidates as $c): ?>
              <tr class="hover:bg-pink-50/20 transition-colors">
                <td class="py-4 px-6 text-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full font-extrabold text-sm bg-pink-600 text-white shadow-xs">
                    <?= $c['order_number'] ?>
                  </span>
                </td>
                <td class="py-4 px-6">
                  <div class="flex items-center gap-3.5">
                    <img src="<?= candidate_photo($c['photo_filename'], (int) $c['order_number']) ?>" alt="" class="w-12 h-12 rounded-xl object-cover bg-pink-50 shrink-0">
                    <div>
                      <div class="font-bold text-gray-900 text-base leading-tight"><?= e($c['full_name']) ?></div>
                      <a href="<?= url('kandidat/' . $c['id']) ?>" target="_blank" class="text-xs text-pink-600 hover:underline inline-block mt-0.5">Lihat di Publik ↗</a>
                    </div>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-pink-100 text-pink-700">
                    Kelas <?= e($c['class']) ?>
                  </span>
                </td>
                <td class="py-4 px-6 text-xs text-gray-500 max-w-xs truncate">
                  <?= e(str_limit($c['vision'], 80)) ?: '-' ?>
                </td>
                <td class="py-4 px-6 text-right">
                  <span class="font-bold text-gray-900 text-base"><?= format_number($c['total_votes']) ?></span>
                  <span class="text-xs text-gray-400 block">suara</span>
                </td>
                <td class="py-4 px-6 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <a href="<?= url('admin/kandidat/' . $c['id'] . '/edit') ?>" class="p-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors" title="Edit">
                      <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                      </svg>
                    </a>

                    <form action="<?= url('admin/kandidat/' . $c['id'] . '/hapus') ?>" method="POST" class="inline" data-confirm="Hapus kandidat '<?= e($c['full_name']) ?>'? Suara yang telah masuk untuk kandidat ini akan ikut terhapus.">
                      <?= csrf_field() ?>
                      <button type="submit" class="p-2 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

</div>
