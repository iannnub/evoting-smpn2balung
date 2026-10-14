<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? e($title) . ' - ' : '' ?>Admin <?= e(config('app.school')) ?></title>
  
  <link rel="icon" type="image/png" href="<?= asset('img/logo.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Chart.js via CDN -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="min-h-full antialiased text-slate-800 bg-slate-50">

  <?php require BASE_PATH . '/app/Views/components/alert.php'; ?>
  <?php require BASE_PATH . '/app/Views/components/admin-sidebar.php'; ?>

  <div class="lg:pl-64 flex flex-col min-h-screen">
    <?php require BASE_PATH . '/app/Views/components/admin-navbar.php'; ?>

    <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
      <?= $content ?>
    </main>
  </div>

  <script src="<?= asset('js/main.js') ?>"></script>
  <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
