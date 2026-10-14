<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= e(config('app.name')) ?></title>
  
  <link rel="icon" type="image/png" href="<?= asset('img/logo.png') ?>">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="min-h-full flex flex-col justify-center bg-gradient-to-br from-pink-50 via-slate-50 to-purple-50 antialiased p-4">

  <?php require BASE_PATH . '/app/Views/components/alert.php'; ?>

  <?= $content ?>

  <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
