<?php
$flashes = take_flashes();
if (!empty($flashes)):
?>
  <div class="fixed top-5 right-5 z-50 flex flex-col gap-2 max-w-md w-full px-4">
    <?php foreach ($flashes as $f): 
      $type = $f['type'] ?? 'info';
      $bg = match ($type) {
          'success' => 'bg-emerald-600 text-white',
          'error' => 'bg-rose-600 text-white',
          'warning' => 'bg-amber-600 text-white',
          default => 'bg-slate-800 text-white',
      };
      $icon = match ($type) {
          'success' => '✓',
          'error' => '✕',
          'warning' => '⚠',
          default => 'ℹ',
      };
    ?>
      <div class="alert-dismissible shadow-lg rounded-xl p-4 flex items-start justify-between gap-3 text-sm font-medium <?= $bg ?>">
        <div class="flex items-center gap-2">
          <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-xs font-bold"><?= $icon ?></span>
          <span><?= e($f['message']) ?></span>
        </div>
        <button type="button" onclick="this.parentElement.remove()" class="text-white/80 hover:text-white text-lg leading-none">&times;</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
