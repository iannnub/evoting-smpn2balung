<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>500 - Kesalahan Server</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-full flex items-center justify-center bg-slate-50 p-6 text-center font-sans">
  <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="text-6xl font-black text-rose-500 mb-2">500</div>
    <h1 class="text-xl font-bold text-gray-800 mb-2">Terjadi Kesalahan Server</h1>
    <p class="text-sm text-gray-500 mb-6">Sistem sedang mengalami kendala sementara. Tim admin telah mencatat pesan kesalahan ini.</p>
    <?php if (!empty($errorMessage)): ?>
      <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-xl text-left text-xs font-mono text-rose-800 break-all leading-relaxed">
        <strong>Detail Error:</strong><br><?= htmlspecialchars($errorMessage) ?>
        <?php if (!empty($errorFile)): ?>
          <div class="mt-2 text-gray-500 text-[11px]">Lokasi: <?= htmlspecialchars($errorFile) ?></div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
    <a href="<?= url() ?>" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-pink-600 text-white font-semibold text-sm hover:bg-pink-700 transition-colors">
      Kembali ke Beranda
    </a>
  </div>
</body>
</html>
