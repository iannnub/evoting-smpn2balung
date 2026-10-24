<div class="space-y-6">

  <!-- Top Action & Period Bar -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
    <div>
      <h2 class="text-xl font-bold text-gray-900">Daftar Pemilih Tetap (DPT)</h2>
      <p class="text-xs text-gray-500 mt-1">
        Periode aktif: <strong class="text-pink-600"><?= e($activePeriod['name'] ?? 'Belum ada periode aktif') ?></strong>
        (Total DPT: <?= number_format($meta['total'] ?? 0, 0, ',', '.') ?> siswa)
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-2.5">
      <a href="<?= url('admin/siswa/export?period_id=' . ($activePeriod['id'] ?? 0)) ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition-all">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
        Ekspor Excel (.xlsx)
      </a>

      <a href="<?= url('admin/import') ?>" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white text-xs font-bold shadow-xs transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
        Import Excel / CSV
      </a>

      <button type="button" onclick="document.getElementById('modal-add-student').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 text-xs font-semibold transition-colors">
        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Tambah Manual
      </button>

      <?php if (!empty($meta['total']) && $meta['total'] > 0): ?>
        <form action="<?= url('admin/siswa-hapus-semua') ?>" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SELURUH data DPT (<?= $meta['total'] ?> siswa) pada periode ini? Tindakan ini tidak dapat dibatalkan!');">
          <?= csrf_field() ?>
          <button type="submit" class="inline-flex items-center gap-1 px-3 py-2.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold transition-colors">
            Kosongkan DPT
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <!-- Category Quick Filter Tabs -->
  <div class="flex flex-wrap gap-2">
    <a href="<?= url('admin/siswa?' . http_build_query(array_merge($filters, ['category' => '', 'has_voted' => '', 'page' => 1]))) ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all <?= empty($filters['category']) && $filters['has_voted'] === '' ? 'bg-pink-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
      <span>Semua DPT</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] <?= empty($filters['category']) && $filters['has_voted'] === '' ? 'bg-pink-700 text-white' : 'bg-gray-100 text-gray-600' ?>">
        <?= number_format($counts['total_all'] ?? $meta['total'], 0, ',', '.') ?>
      </span>
    </a>

    <a href="<?= url('admin/siswa?' . http_build_query(array_merge($filters, ['category' => 'siswa', 'page' => 1]))) ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all <?= ($filters['category'] ?? '') === 'siswa' ? 'bg-pink-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
      <span>Data Siswa</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($filters['category'] ?? '') === 'siswa' ? 'bg-pink-700 text-white' : 'bg-pink-50 text-pink-700' ?>">
        <?= number_format($counts['total_siswa'] ?? 770, 0, ',', '.') ?>
      </span>
    </a>

    <a href="<?= url('admin/siswa?' . http_build_query(array_merge($filters, ['category' => 'guru', 'page' => 1]))) ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all <?= ($filters['category'] ?? '') === 'guru' ? 'bg-purple-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
      <span>Dewan Guru</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($filters['category'] ?? '') === 'guru' ? 'bg-purple-700 text-white' : 'bg-purple-50 text-purple-700' ?>">
        <?= number_format($counts['total_guru'] ?? 52, 0, ',', '.') ?>
      </span>
    </a>

    <a href="<?= url('admin/siswa?' . http_build_query(array_merge($filters, ['has_voted' => '1', 'page' => 1]))) ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all <?= ($filters['has_voted'] ?? '') === '1' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
      <span>Sudah Memilih</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($filters['has_voted'] ?? '') === '1' ? 'bg-emerald-700 text-white' : 'bg-emerald-50 text-emerald-700' ?>">
        <?= number_format($counts['total_voted'] ?? 0, 0, ',', '.') ?>
      </span>
    </a>

    <a href="<?= url('admin/siswa?' . http_build_query(array_merge($filters, ['has_voted' => '0', 'page' => 1]))) ?>" 
       class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl text-xs font-bold transition-all <?= ($filters['has_voted'] ?? '') === '0' ? 'bg-slate-700 text-white shadow-xs' : 'bg-white border border-gray-200 text-gray-700 hover:bg-gray-50' ?>">
      <span>Belum Memilih</span>
      <span class="px-2 py-0.5 rounded-full text-[10px] <?= ($filters['has_voted'] ?? '') === '0' ? 'bg-slate-800 text-white' : 'bg-slate-100 text-slate-700' ?>">
        <?= number_format($counts['total_unvoted'] ?? 0, 0, ',', '.') ?>
      </span>
    </a>
  </div>

  <!-- Filter & Search Card -->
  <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-xs">
    <form action="<?= url('admin/siswa') ?>" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3">
      <?php if (!empty($_GET['period_id'])): ?>
        <input type="hidden" name="period_id" value="<?= e($_GET['period_id']) ?>">
      <?php endif; ?>
      <?php if (!empty($filters['category'])): ?>
        <input type="hidden" name="category" value="<?= e($filters['category']) ?>">
      <?php endif; ?>

      <div class="md:col-span-2">
        <label for="search" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Nama / NISN</label>
        <div class="relative">
          <input type="text" id="search" name="search" value="<?= e($filters['search']) ?>" placeholder="Ketik nama siswa, guru, atau NISN..." class="w-full pl-9 pr-4 py-2 rounded-xl border border-gray-200 text-xs focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100">
          <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <div>
        <label for="filter-class" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Kelas</label>
        <select id="filter-class" name="class" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100 bg-white">
          <option value="">Semua Kelas</option>
          <?php foreach ($classes as $c): ?>
            <option value="<?= e($c) ?>" <?= $filters['class'] === $c ? 'selected' : '' ?>><?= e($c === 'GURU' ? 'DEWAN GURU' : $c) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label for="filter-status" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Vote</label>
        <select id="filter-status" name="has_voted" class="w-full px-3 py-2 rounded-xl border border-gray-200 text-xs focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100 bg-white">
          <option value="">Semua Status</option>
          <option value="0" <?= $filters['has_voted'] === '0' ? 'selected' : '' ?>>Belum Memilih</option>
          <option value="1" <?= $filters['has_voted'] === '1' ? 'selected' : '' ?>>Sudah Memilih</option>
        </select>
      </div>

      <div class="flex items-end gap-2">
        <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-bold text-xs transition-colors">
          Filter
        </button>
        <a href="<?= url('admin/siswa') ?>" class="py-2 px-3 rounded-xl border border-gray-200 hover:bg-gray-50 text-gray-600 font-semibold text-xs transition-colors text-center">
          Reset
        </a>
      </div>
    </form>
  </div>

  <!-- Bulk Action Form & Student Table -->
  <form id="bulk-delete-form" action="<?= url('admin/siswa-hapus-banyak') ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa yang dicentang?');">
    <?= csrf_field() ?>

    <div class="bg-white rounded-3xl border border-gray-100 shadow-xs overflow-hidden">
      <!-- Bulk Toolbar -->
      <div id="bulk-toolbar" class="hidden px-6 py-3 bg-pink-50 border-b border-pink-100 flex items-center justify-between">
        <span class="text-xs font-bold text-pink-900">
          <span id="selected-count">0</span> siswa dipilih
        </span>
        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition-colors">
          Hapus Terpilih
        </button>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs">
          <thead class="bg-slate-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-[11px]">
            <tr>
              <th class="py-3.5 px-4 w-10 text-center">
                <input type="checkbox" id="select-all" class="rounded text-pink-600 focus:ring-pink-500">
              </th>
              <th class="py-3.5 px-4 w-16 text-center">No</th>
              <th class="py-3.5 px-4">NISN</th>
              <th class="py-3.5 px-4">Nama Siswa</th>
              <th class="py-3.5 px-4">Kelas</th>
              <th class="py-3.5 px-4">Status Voting</th>
              <th class="py-3.5 px-4 text-center w-20">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php if (empty($students)): ?>
              <tr>
                <td colspan="7" class="py-12 text-center text-gray-400">
                  <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                  Belum ada data siswa / DPT pada periode ini atau tidak ada hasil yang sesuai filter.<br>
                  <a href="<?= url('admin/import') ?>" class="text-pink-600 font-bold hover:underline mt-1 inline-block">Klik di sini untuk import dari Excel/CSV</a>
                </td>
              </tr>
            <?php else: ?>
              <?php $num = $meta['from']; ?>
              <?php foreach ($students as $s): ?>
                <tr class="hover:bg-slate-50/70 transition-colors">
                  <td class="py-3 px-4 text-center">
                    <input type="checkbox" name="student_ids[]" value="<?= $s['id'] ?>" class="student-checkbox rounded text-pink-600 focus:ring-pink-500">
                  </td>
                  <td class="py-3 px-4 text-center font-mono text-gray-400"><?= $num++ ?></td>
                  <td class="py-3 px-4 font-mono font-medium text-gray-700">
                    <?= !empty($s['nis']) ? e($s['nis']) : '<span class="text-gray-300 italic">-</span>' ?>
                  </td>
                  <td class="py-3 px-4 font-bold text-gray-900"><?= e($s['full_name']) ?></td>
                  <td class="py-3 px-4">
                    <?php if ($s['class'] === 'GURU'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                        <span>👨‍🏫</span> Dewan Guru / Staf
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-100">
                        Kelas <?= e($s['class']) ?>
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4">
                    <?php if (!empty($s['has_voted'])): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Sudah Memilih
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                        Belum
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4 text-center">
                    <form action="<?= url('admin/siswa/' . $s['id'] . '/hapus') ?>" method="POST" onsubmit="return confirm('Hapus siswa <?= htmlspecialchars(addslashes($s['full_name'])) ?> dari DPT?');" class="inline">
                      <?= csrf_field() ?>
                      <button type="submit" class="text-gray-400 hover:text-rose-600 p-1 transition-colors" title="Hapus Siswa">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination Footer -->
      <?php if (!empty($meta) && $meta['total'] > 0): ?>
        <div class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-gray-500">
          <div>
            Menampilkan data <strong><?= $meta['from'] ?></strong> - <strong><?= $meta['to'] ?></strong> dari total <strong><?= number_format($meta['total'], 0, ',', '.') ?></strong> siswa
          </div>

          <?php if ($meta['last_page'] > 1): ?>
            <div class="flex items-center gap-1">
              <?php
                // Helper query string builder preserving current filters
                $query = $_GET;
              ?>

              <!-- Previous button -->
              <?php if ($meta['current_page'] > 1): ?>
                <?php $query['page'] = $meta['current_page'] - 1; ?>
                <a href="<?= url('admin/siswa?' . http_build_query($query)) ?>" class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold transition-colors">
                  Sebelumnya
                </a>
              <?php endif; ?>

              <!-- Page numbers -->
              <?php
                $startPage = max(1, $meta['current_page'] - 2);
                $endPage = min($meta['last_page'], $meta['current_page'] + 2);
              ?>

              <?php if ($startPage > 1): ?>
                <?php $query['page'] = 1; ?>
                <a href="<?= url('admin/siswa?' . http_build_query($query)) ?>" class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold transition-colors">1</a>
                <?php if ($startPage > 2): ?><span class="px-1 text-gray-400">...</span><?php endif; ?>
              <?php endif; ?>

              <?php for ($p = $startPage; $p <= $endPage; $p++): ?>
                <?php $query['page'] = $p; ?>
                <?php if ($p === $meta['current_page']): ?>
                  <span class="px-3 py-1.5 rounded-lg bg-pink-600 text-white font-bold"><?= $p ?></span>
                <?php else: ?>
                  <a href="<?= url('admin/siswa?' . http_build_query($query)) ?>" class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold transition-colors"><?= $p ?></a>
                <?php endif; ?>
              <?php endfor; ?>

              <?php if ($endPage < $meta['last_page']): ?>
                <?php if ($endPage < $meta['last_page'] - 1): ?><span class="px-1 text-gray-400">...</span><?php endif; ?>
                <?php $query['page'] = $meta['last_page']; ?>
                <a href="<?= url('admin/siswa?' . http_build_query($query)) ?>" class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold transition-colors"><?= $meta['last_page'] ?></a>
              <?php endif; ?>

              <!-- Next button -->
              <?php if ($meta['current_page'] < $meta['last_page']): ?>
                <?php $query['page'] = $meta['current_page'] + 1; ?>
                <a href="<?= url('admin/siswa?' . http_build_query($query)) ?>" class="px-2.5 py-1.5 rounded-lg border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 font-semibold transition-colors">
                  Berikutnya
                </a>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </form>

</div>

<!-- Modal Tambah Siswa Manual -->
<div id="modal-add-student" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
  <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-gray-100">
    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-bold text-gray-900">Tambah Siswa ke DPT</h3>
      <button type="button" onclick="document.getElementById('modal-add-student').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
    </div>

    <form action="<?= url('admin/siswa') ?>" method="POST" class="space-y-4">
      <?= csrf_field() ?>

      <div>
        <label for="nis" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">NISN (10 Digit)</label>
        <input type="text" id="nis" name="nis" maxlength="10" placeholder="Contoh: 0133085278" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100">
        <span class="text-[11px] text-gray-400 mt-1 block">Boleh dikosongkan jika siswa belum memiliki NISN</span>
      </div>

      <div>
        <label for="full_name" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Nama Lengkap *</label>
        <input type="text" id="full_name" name="full_name" required placeholder="Contoh: AHMAD FAUZAN" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100 uppercase">
      </div>

      <div>
        <label for="class" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">Kelas *</label>
        <input type="text" id="class" name="class" required placeholder="Contoh: 7A, 8B, 9H" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-100 uppercase">
      </div>

      <div class="pt-4 flex items-center justify-end gap-2.5 border-t border-gray-100">
        <button type="button" onclick="document.getElementById('modal-add-student').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50">
          Batal
        </button>
        <button type="submit" class="px-5 py-2 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-xs transition-colors">
          Simpan Siswa
        </button>
      </div>
    </form>
  </div>
</div>

<script>
// Checkbox select all & bulk action bar
const selectAll = document.getElementById('select-all');
const studentCheckboxes = document.querySelectorAll('.student-checkbox');
const bulkToolbar = document.getElementById('bulk-toolbar');
const selectedCount = document.getElementById('selected-count');

function updateBulkState() {
  const checked = document.querySelectorAll('.student-checkbox:checked');
  const count = checked.length;
  if (selectedCount) selectedCount.textContent = count;
  if (bulkToolbar) {
    if (count > 0) {
      bulkToolbar.classList.remove('hidden');
    } else {
      bulkToolbar.classList.add('hidden');
    }
  }
}

if (selectAll) {
  selectAll.addEventListener('change', function() {
    studentCheckboxes.forEach(cb => cb.checked = selectAll.checked);
    updateBulkState();
  });
}

studentCheckboxes.forEach(cb => {
  cb.addEventListener('change', updateBulkState);
});
</script>
