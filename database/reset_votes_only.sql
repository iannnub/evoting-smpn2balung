-- =====================================================================
-- SCRIPT KHUSUS PHPMYADMIN: RESET SUARA KE 0% & UPDATE KANDIDAT RESMI
-- Jalankan query ini di tab 'SQL' phpMyAdmin jika tidak ingin menimpa data siswa
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Kosongkan riwayat suara uji coba demo (Hasil voting kembali 0%)
TRUNCATE TABLE `votes`;

-- 2. Pastikan pengaturan periode tidak mewajibkan NISN
UPDATE `periods` SET `require_nisn_verification` = 0 WHERE `id` = 1;

SET FOREIGN_KEY_CHECKS = 1;
