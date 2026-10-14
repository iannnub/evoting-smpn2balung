<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 - Halaman Tidak Ditemukan</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-full flex items-center justify-center bg-slate-50 p-6 text-center font-sans">
  <div class="max-w-md w-full bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
    <div class="text-6xl font-black text-pink-500 mb-2">404</div>
    <h1 class="text-xl font-bold text-gray-800 mb-2">Halaman Tidak Ditemukan</h1>
    <p class="text-sm text-gray-500 mb-6">Halaman yang Anda cari mungkin telah dipindahkan atau alamat URL tidak valid.</p>
    <a href="<?= url() ?>" class="inline-flex items-center justify-center px-6 py-2.5 rounded-xl bg-pink-600 text-white font-semibold text-sm hover:bg-pink-700 transition-colors">
      Kembali ke Beranda
    </a>
  </div>
</body>
</html>
