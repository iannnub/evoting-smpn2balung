<?php
  $rows = $importData['rows'] ?? [];
  $totalRows = count($rows);
  $validCount = count(array_filter($rows, fn($r) => !empty($r['is_valid'])));
  $invalidCount = $totalRows - $validCount;
  $warningRowsCount = count(array_filter($rows, fn($r) => !empty($r['warnings'])));
?>

<div class="space-y-6">

  <!-- Header -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
    <div>
      <div class="flex items-center gap-2 mb-1">
        <a href="<?= url('admin/import') ?>" class="text-xs text-pink-600 hover:underline font-semibold">Ganti File</a>
        <span class="text-xs text-gray-400">•</span>
        <span class="text-xs text-gray-500 font-mono">File: <?= e($importData['filename'] ?? 'Spreadsheet') ?></span>
      </div>
      <h2 class="text-xl font-bold text-gray-900">Pratinjau Data Import DPT</h2>
      <p class="text-xs text-gray-500 mt-0.5">
        Target Periode: <strong class="text-pink-600"><?= e($period['name'] ?? '-') ?></strong>
      </p>
    </div>

    <!-- Quick Stats Badges -->
    <div class="flex flex-wrap items-center gap-2">
      <div class="px-3.5 py-2 rounded-2xl bg-slate-50 border border-slate-200 text-center">
        <span class="block text-xs text-gray-500">Total Baris</span>
        <span class="block text-base font-bold text-gray-800"><?= number_format($totalRows, 0, ',', '.') ?></span>
      </div>
      <div class="px-3.5 py-2 rounded-2xl bg-emerald-50 border border-emerald-200 text-center">
        <span class="block text-xs text-emerald-700">Valid</span>
        <span class="block text-base font-bold text-emerald-800"><?= number_format($validCount, 0, ',', '.') ?></span>
      </div>
      <?php if ($invalidCount > 0): ?>
        <div class="px-3.5 py-2 rounded-2xl bg-rose-50 border border-rose-200 text-center">
          <span class="block text-xs text-rose-700">Tidak Valid</span>
          <span class="block text-base font-bold text-rose-800"><?= number_format($invalidCount, 0, ',', '.') ?></span>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Confirmation Form Card -->
  <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-xs">
    <form action="<?= url('admin/import/simpan') ?>" method="POST" class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <?= csrf_field() ?>
      <input type="hidden" name="token" value="<?= e($token) ?>">

      <div class="space-y-1">
        <label class="flex items-start gap-2.5 cursor-pointer">
          <input type="checkbox" name="skip_duplicate_nis" value="1" checked class="mt-0.5 w-4 h-4 rounded text-pink-600 focus:ring-pink-500">
          <div>
            <span class="text-xs font-bold text-gray-800">Lewati baris jika NISN sudah terdaftar di periode ini</span>
            <span class="block text-[11px] text-gray-500">Mencegah duplikasi data jika ada siswa dengan NISN sama yang telah diinput sebelumnya.</span>
          </div>
        </label>
      </div>

      <div class="flex items-center gap-3 shrink-0">
        <a href="<?= url('admin/import') ?>" class="px-4 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-xs font-semibold hover:bg-gray-50 transition-colors">
          Batal
        </a>
        <button type="submit" <?= $validCount === 0 ? 'disabled' : '' ?> class="px-5 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-xs shadow-sm transition-colors disabled:opacity-50 flex items-center gap-2">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
          <span>Konfirmasi & Simpan (<?= number_format($validCount, 0, ',', '.') ?> Siswa)</span>
        </button>
      </div>
    </form>
  </div>

  <!-- Preview Table -->
  <div class="bg-white rounded-3xl border border-gray-100 shadow-xs overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
      <span class="text-xs font-bold text-gray-700">Tabel Pratinjau Baris Siswa</span>
      <span class="text-xs text-gray-400">Menampilkan seluruh baris dari file</span>
    </div>

    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-slate-50 border-b border-gray-100 text-gray-500 font-semibold uppercase tracking-wider text-[11px] sticky top-0 z-10 shadow-xs">
          <tr>
            <th class="py-3 px-4 w-12 text-center">No</th>
            <th class="py-3 px-4 w-28">NISN</th>
            <th class="py-3 px-4">Nama Siswa</th>
            <th class="py-3 px-4 w-24">Kelas</th>
            <th class="py-3 px-4 w-24 text-center">Status</th>
            <th class="py-3 px-4">Catatan / Peringatan</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          <?php foreach ($rows as $r): ?>
            <tr class="hover:bg-slate-50/70 transition-colors <?= !$r['is_valid'] ? 'bg-rose-50/40' : '' ?>">
              <td class="py-2.5 px-4 text-center font-mono text-gray-400"><?= $r['row_num'] ?></td>
              <td class="py-2.5 px-4 font-mono font-medium <?= empty($r['nis']) ? 'text-amber-600' : 'text-gray-800' ?>">
                <?= !empty($r['nis']) ? e($r['nis']) : '<span class="italic text-gray-400">-</span>' ?>
              </td>
              <td class="py-2.5 px-4 font-bold text-gray-900"><?= e($r['full_name'] ?: '(Kosong)') ?></td>
              <td class="py-2.5 px-4">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-pink-50 text-pink-700 border border-pink-100">
                  <?= e($r['class']) ?>
                </span>
              </td>
              <td class="py-2.5 px-4 text-center">
                <?php if ($r['is_valid']): ?>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Valid
                  </span>
                <?php else: ?>
                  <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                    Gagal
                  </span>
                <?php endif; ?>
              </td>
              <td class="py-2.5 px-4">
                <?php if (!empty($r['warnings'])): ?>
                  <div class="flex flex-wrap gap-1">
                    <?php foreach ($r['warnings'] as $w): ?>
                      <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-800 border border-amber-200">
                        ⚠ <?= e($w) ?>
                      </span>
                    <?php endforeach; ?>
                  </div>
                <?php else: ?>
                  <span class="text-gray-300 text-[11px]">-</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="px-6 py-4 bg-slate-50 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
      <span>Total <?= number_format($totalRows, 0, ',', '.') ?> data siswa siap diproses.</span>
      <a href="<?= url('admin/import') ?>" class="text-pink-600 font-bold hover:underline">Unggah Ulang File</a>
    </div>
  </div>

</div>
