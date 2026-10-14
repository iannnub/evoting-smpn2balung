<?php
/**
 * Upload & resize foto kandidat (GD).
 */

/**
 * Validasi dan simpan foto kandidat. Foto di-resize maks 800x800, disimpan sebagai JPEG kualitas 85.
 *
 * @return array{success: bool, filename?: string, error?: string}
 */
function upload_candidate_photo(array $file): array
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Ukuran foto melebihi batas server',
            UPLOAD_ERR_FORM_SIZE => 'Ukuran foto melebihi batas',
            UPLOAD_ERR_PARTIAL => 'Upload foto terputus, coba lagi',
            UPLOAD_ERR_NO_FILE => 'Foto belum dipilih',
        ];
        return ['success' => false, 'error' => $messages[$file['error'] ?? UPLOAD_ERR_NO_FILE] ?? 'Upload foto gagal'];
    }

    $maxSize = (int) config('app.upload_max_size');
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'Ukuran foto maksimal ' . round($maxSize / 1048576, 1) . ' MB'];
    }

    // Cek tipe asli dari isi file (magic bytes), bukan dari nama/ekstensi
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        return ['success' => false, 'error' => 'Foto harus berformat JPG, PNG, atau WEBP'];
    }
    $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
        return ['success' => false, 'error' => 'Ekstensi file foto tidak diizinkan'];
    }
    $info = @getimagesize($file['tmp_name']);
    if ($info === false) {
        return ['success' => false, 'error' => 'File bukan gambar yang valid'];
    }

    $dir = BASE_PATH . '/public/uploads/candidates';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(6)) . '.jpg';

    if (!resize_image($file['tmp_name'], $dir . '/' . $filename, 800, 800, $mime)) {
        return ['success' => false, 'error' => 'Gagal memproses foto'];
    }
    return ['success' => true, 'filename' => $filename];
}

function resize_image(string $source, string $destination, int $maxW, int $maxH, string $mime): bool
{
    $src = match ($mime) {
        'image/jpeg' => @imagecreatefromjpeg($source),
        'image/png' => @imagecreatefrompng($source),
        'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
        default => false,
    };
    if (!$src) {
        return false;
    }

    // Perbaiki orientasi foto HP (EXIF)
    if ($mime === 'image/jpeg' && function_exists('exif_read_data')) {
        $exif = @exif_read_data($source);
        $rotate = ['3' => 180, '6' => -90, '8' => 90][(string) ($exif['Orientation'] ?? '')] ?? 0;
        if ($rotate) {
            $rotated = imagerotate($src, $rotate, 0);
            if ($rotated) {
                imagedestroy($src);
                $src = $rotated;
            }
        }
    }

    $w = imagesx($src);
    $h = imagesy($src);
    $ratio = min(1, $maxW / $w, $maxH / $h);
    $nw = max(1, (int) round($w * $ratio));
    $nh = max(1, (int) round($h * $ratio));

    $dst = imagecreatetruecolor($nw, $nh);
    // Latar putih untuk PNG transparan
    imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
    imageinterlace($dst, true);
    $ok = imagejpeg($dst, $destination, 85);
    imagedestroy($src);
    imagedestroy($dst);
    return $ok;
}

function delete_candidate_photo(?string $filename): void
{
    if (!$filename || basename($filename) !== $filename) {
        return;
    }
    $path = BASE_PATH . '/public/uploads/candidates/' . $filename;
    if (is_file($path)) {
        @unlink($path);
    }
}
