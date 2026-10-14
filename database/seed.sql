USE osis_voting;

-- Default Admin SMPN 2 BALUNG
-- Password default: admin123
INSERT INTO admins (email, password_hash, full_name)
VALUES (
  'admin@smpn2balung.sch.id',
  '$2y$10$GTWzq2N/AVLqljXp3UUdsuTKgodyiDEzsg99rwXgsTQcggwNLS78G',
  'Administrator SMPN 2 BALUNG'
)
ON DUPLICATE KEY UPDATE full_name = VALUES(full_name);

-- Default Active Period jika belum ada
INSERT INTO periods (id, name, start_date, end_date, is_active, is_results_visible, is_voting_open, require_nisn_verification)
SELECT 1, 'Pemilihan Ketua OSIS 2026/2027', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 14 DAY), 1, 1, 1, 0
WHERE NOT EXISTS (SELECT 1 FROM periods WHERE is_active = 1);
