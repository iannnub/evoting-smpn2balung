<div class="space-y-8">

  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Kelola Periode Pemilihan</h2>
      <p class="text-xs sm:text-sm text-gray-500">Buat, aktifkan, atau arsipkan periode pemilihan OSIS setiap tahunnya.</p>
    </div>
    <div class="flex items-center gap-3">
      <a href="<?= url('admin/periode/tambah') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors">
        + Buat Periode Baru
      </a>
    </div>
  </div>

  <!-- Periods Table -->
  <div class="bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-xs">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm text-gray-600">
        <thead class="bg-slate-50 text-xs uppercase font-bold text-gray-500 tracking-wider border-b border-gray-200">
          <tr>
            <th class="py-4 px-6">Nama Periode</th>
            <th class="py-4 px-6">Tanggal</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6 text-center">Bilik Suara</th>
            <th class="py-4 px-6 text-center">Hasil Publik</th>
            <th class="py-4 px-6 text-center">Statistik</th>
            <th class="py-4 px-6 text-right">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($periods as $p): ?>
            <tr class="hover:bg-slate-50/60 transition-colors <?= $p['is_active'] ? 'bg-pink-50/20' : '' ?>">
              <td class="py-4 px-6">
                <div class="font-bold text-gray-900 text-base"><?= e($p['name']) ?></div>
                <span class="text-xs text-gray-400">ID: #<?= $p['id'] ?></span>
              </td>
              <td class="py-4 px-6 text-xs text-gray-500">
                <div><?= format_date_id($p['start_date']) ?></div>
                <div class="text-gray-400">s/d <?= $p['end_date'] ? format_date_id($p['end_date']) : 'Selesai' ?></div>
              </td>
              <td class="py-4 px-6 text-center">
                <?php if ($p['is_active']): ?>
                  <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Aktif
                  </span>
                <?php else: ?>
                  <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                    Arsip
                  </span>
                <?php endif; ?>
              </td>
              <td class="py-4 px-6 text-center">
                <form action="<?= url('admin/periode/' . $p['id'] . '/toggle-voting') ?>" method="POST" class="inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-xs font-bold px-3 py-1 rounded-full border transition-colors <?= !empty($p['is_voting_open']) ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-gray-100 text-gray-500 border-gray-200 hover:bg-gray-200' ?>">
                    <?= !empty($p['is_voting_open']) ? 'Buka' : 'Tutup' ?>
                  </button>
                </form>
              </td>
              <td class="py-4 px-6 text-center">
                <form action="<?= url('admin/periode/' . $p['id'] . '/toggle-hasil') ?>" method="POST" class="inline">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-xs font-bold px-3 py-1 rounded-full border transition-colors <?= !empty($p['is_results_visible']) ? 'bg-pink-50 text-pink-700 border-pink-200 hover:bg-pink-100' : 'bg-amber-50 text-amber-700 border-amber-200 hover:bg-amber-100' ?>">
                    <?= !empty($p['is_results_visible']) ? '👁️ Tampil' : '🔒 Rahasia' ?>
                  </button>
                </form>
              </td>
              <td class="py-4 px-6 text-center text-xs">
                <div><strong class="text-gray-900"><?= $p['total_candidates'] ?></strong> kandidat</div>
                <div><strong class="text-gray-900"><?= $p['total_students'] ?></strong> siswa &bull; <strong class="text-gray-900"><?= $p['total_votes'] ?></strong> suara</div>
              </td>
              <td class="py-4 px-6 text-right">
                <div class="flex items-center justify-end gap-2">
                  <?php if (!$p['is_active']): ?>
                    <form action="<?= url('admin/periode/' . $p['id'] . '/aktifkan') ?>" method="POST" class="inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-pink-100 hover:bg-pink-200 text-pink-700 text-xs font-bold transition-colors">
                        Aktifkan
                      </button>
                    </form>
                  <?php endif; ?>

                  <a href="<?= url('admin/periode/' . $p['id'] . '/edit') ?>" class="p-1.5 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 transition-colors" title="Edit">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                  </a>

                  <?php if (!$p['is_active']): ?>
                    <form action="<?= url('admin/periode/' . $p['id'] . '/hapus') ?>" method="POST" class="inline" data-confirm="Hapus periode '<?= e($p['name']) ?>' beserta seluruh data kandidat, DPT, dan suara terkait?">
                      <?= csrf_field() ?>
                      <button type="submit" class="p-1.5 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition-colors" title="Hapus">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                      </button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Danger Zone: Reset & New Period -->
  <div class="bg-rose-50/70 border border-rose-200 rounded-3xl p-6 sm:p-8">
    <div class="flex items-start gap-4">
      <div class="w-12 h-12 rounded-2xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
      </div>
      <div class="flex-1">
        <h3 class="text-base font-bold text-rose-900">Reset Voting &amp; Buat Periode Baru (Ganti Tahun / Periode)</h3>
        <p class="text-xs sm:text-sm text-rose-700 mt-1 leading-relaxed">
          Gunakan fitur ini ketika masa pemilihan tahun ini telah usai dan Anda ingin memulai pemilihan baru tahun depan. Tindakan ini akan menonaktifkan periode saat ini dan membuat periode baru yang kosong. Data pemilih dan perolehan suara lama <strong>tetap tersimpan aman sebagai arsip</strong>.
        </p>

        <form action="<?= url('admin/periode-reset') ?>" method="POST" class="mt-6 space-y-4 max-w-lg bg-white p-5 rounded-2xl border border-rose-200">
          <?= csrf_field() ?>

          <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
              Nama Periode Baru
            </label>
            <input type="text" name="new_period_name" required placeholder="Contoh: Pemilihan OSIS 2027/2028" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-100">
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
              Tanggal Mulai
            </label>
            <input type="date" name="start_date" value="<?= date('Y-m-d') ?>" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-100">
          </div>

          <div>
            <label class="block text-xs font-bold text-rose-800 uppercase tracking-wider mb-1">
              Ketik kata "RESET" untuk konfirmasi:
            </label>
            <input type="text" id="reset-confirmation-input" name="confirmation" autocomplete="off" placeholder="Ketik RESET di sini" class="w-full px-3.5 py-2.5 rounded-xl border border-rose-300 text-sm font-bold tracking-widest text-rose-700 focus:border-rose-600 focus:outline-none focus:ring-2 focus:ring-rose-200">
          </div>

          <button type="submit" id="btn-confirm-reset" disabled class="w-full py-3 rounded-xl bg-rose-600 text-white font-bold text-sm shadow-md opacity-50 cursor-not-allowed hover:bg-rose-700 transition-all">
            Ya, Reset dan Buka Periode Baru
          </button>
        </form>
      </div>
    </div>
  </div>

</div>
