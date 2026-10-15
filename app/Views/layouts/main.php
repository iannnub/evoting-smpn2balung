<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($title) ? e($title) . ' - ' : '' ?><?= e(config('app.name')) ?></title>
  
  <meta name="description" content="Situs resmi pemilihan Ketua dan Wakil Ketua OSIS SMP Negeri 2 Balung periode 2026/2027. Salurkan hak suara Anda secara jujur, langsung, dan rahasia.">
  
  <!-- Open Graph / Facebook / WhatsApp -->
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="E-Voting OSIS SMPN 2 Balung">
  <meta property="og:title" content="<?= isset($title) ? e($title) . ' - ' : '' ?><?= e(config('app.name')) ?>">
  <meta property="og:description" content="Situs resmi pemilihan Ketua dan Wakil Ketua OSIS SMP Negeri 2 Balung periode 2026/2027. Salurkan hak suara Anda secara jujur, langsung, dan rahasia.">
  <meta property="og:image" content="<?= asset('img/og-image.png') ?>?v=20261015">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">
  <meta property="og:image:alt" content="E-Voting OSIS SMP Negeri 2 Balung">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= isset($title) ? e($title) . ' - ' : '' ?><?= e(config('app.name')) ?>">
  <meta name="twitter:description" content="Situs resmi pemilihan Ketua dan Wakil Ketua OSIS SMP Negeri 2 Balung periode 2026/2027.">
  <meta name="twitter:image" content="<?= asset('img/og-image.png') ?>?v=20261015">

  <link rel="icon" type="image/x-icon" href="/favicon.ico">
  <link rel="icon" type="image/png" href="<?= asset('img/logo.png') ?>">
  <link rel="apple-touch-icon" href="<?= asset('img/apple-touch-icon.png') ?>">

  <!-- Google Fonts: Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50: '#fdf2f8',
              100: '#fce7f3',
              200: '#fbcfe8',
              300: '#f9a8d4',
              400: '#f472b6',
              500: '#ec4899',
              600: '#db2777',
              700: '#be185d',
            }
          }
        }
      }
    }
  </script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="min-h-full flex flex-col bg-slate-50 text-slate-800 antialiased">

  <?php require BASE_PATH . '/app/Views/components/alert.php'; ?>
  <?php require BASE_PATH . '/app/Views/components/header.php'; ?>

  <main class="flex-1">
    <?= $content ?>
  </main>

  <?php require BASE_PATH . '/app/Views/components/footer.php'; ?>

  <script src="<?= asset('js/main.js') ?>"></script>
</body>
</html>
