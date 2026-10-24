<div class="max-w-2xl mx-auto space-y-6">

  <div class="flex items-center justify-between">
    <a href="<?= url('admin/siswa') ?>" class="text-xs text-pink-600 hover:underline flex items-center gap-1 font-semibold">
      Kembali ke Daftar Siswa
    </a>
    <span class="text-xs text-gray-400">Periode: <?= e($period['name'] ?? '-') ?></span>
  </div>

  <!-- Instruction Card -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-xs space-y-6">
    <div class="border-b border-gray-100 pb-5">
      <h2 class="text-xl font-bold text-gray-900">Import Data Pemilih (DPT)</h2>
      <p class="text-xs text-gray-500 mt-1">
        Upload file spreadsheet DPT dari sekolah (.xlsx atau .csv) untuk memasukkan daftar siswa ke periode ini secara massal.
      </p>
    </div>

    <!-- Format Guide -->
    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
      <div class="flex items-center gap-2 text-xs font-bold text-slate-800">
        <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        Ketentuan Kolom Spreadsheet:
      </div>
      <ul class="text-xs text-slate-600 space-y-1.5 list-disc list-inside">
        <li>Baris header wajib memuat kolom: <code class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold">NO</code>, <code class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold">NISN</code>, <code class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold">NAMA</code>, <code class="px-1.5 py-0.5 rounded bg-slate-200 text-slate-800 font-mono font-bold">KELAS</code> (urutan kolom bebas).</li>
        <li>Kolom <strong>KELAS</strong> mendukung format sekolah seperti <code class="px-1 py-0.5 rounded bg-slate-200 font-mono font-semibold">7 A</code> s/d <code class="px-1 py-0.5 rounded bg-slate-200 font-mono font-semibold">9 H</code> (akan otomatis dinormalisasi).</li>
        <li>Angka <strong>NISN</strong> yang hilang angka nol di depannya otomatis dilengkapi menjadi 10 digit.</li>
      </ul>

      <div class="pt-2 flex flex-wrap items-center gap-2">
        <span class="text-xs text-gray-500">Unduh contoh template:</span>
        <a href="<?= url('admin/import/template?format=xlsx') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 text-xs font-bold transition-colors">
          <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
          Template .XLSX
        </a>
        <a href="<?= url('admin/import/template?format=csv') ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-100 text-slate-700 text-xs font-bold transition-colors">
          Template .CSV
        </a>
      </div>
    </div>

    <!-- Upload Form -->
    <form action="<?= url('admin/import/preview') ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
      <?= csrf_field() ?>

      <div>
        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
          Pilih File Spreadsheet *
        </label>
        <div class="relative border-2 border-dashed border-pink-200 hover:border-pink-400 bg-pink-50/20 rounded-3xl p-8 text-center transition-colors cursor-pointer" onclick="document.getElementById('file-input').click()">
          <input type="file" id="file-input" name="file" accept=".xlsx,.csv,.txt" required class="hidden" onchange="updateFileName(this)">
          
          <div class="flex flex-col items-center justify-center space-y-2">
            <div class="w-14 h-14 rounded-2xl bg-pink-100 text-pink-600 flex items-center justify-center shadow-xs">
              <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
            <div class="text-sm font-bold text-gray-800" id="file-label">
              Klik atau seret file spreadsheet ke sini
            </div>
            <div class="text-xs text-gray-400">
              Mendukung Microsoft Excel (.xlsx) atau file teks (.csv). Maksimal 10MB.
            </div>
          </div>
        </div>
      </div>

      <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
        <a href="<?= url('admin/siswa') ?>" class="px-5 py-2.5 rounded-xl border border-gray-200 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
          Batal
        </a>
        <button type="submit" id="submit-btn" class="px-6 py-2.5 rounded-xl bg-pink-600 hover:bg-pink-700 text-white font-bold text-sm shadow-sm transition-colors flex items-center gap-2">
          <span>Unggah & Pratinjau Data</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </button>
      </div>

    </form>
  </div>

</div>

<script>
function updateFileName(input) {
  if (input.files && input.files[0]) {
    const file = input.files[0];
    const label = document.getElementById('file-label');
    label.innerHTML = `<span class="text-pink-600 font-bold">📄 ${file.name}</span> <span class="text-xs text-gray-500 font-normal">(${(file.size / 1024).toFixed(1)} KB)</span>`;
  }
}
</script>
