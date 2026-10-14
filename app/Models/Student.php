<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Student extends Model
{
    protected string $table = 'students';

    public function search(string $query, int|string $periodId, int $limit = 15, bool $excludeCandidates = false): array
    {
        $term = '%' . trim($query) . '%';
        $excludeSql = $excludeCandidates ? "AND c.id IS NULL" : "";

        $sql = "
            SELECT s.id, s.nis, s.full_name, s.class, s.grade,
                   (CASE WHEN v.id IS NOT NULL THEN 1 ELSE 0 END) AS has_voted,
                   v.voted_at,
                   (CASE WHEN c.id IS NOT NULL THEN 1 ELSE 0 END) AS is_candidate
            FROM {$this->table} s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            LEFT JOIN candidates c ON UPPER(s.full_name) = UPPER(c.full_name) 
                                  AND UPPER(s.class) = UPPER(c.class) 
                                  AND s.period_id = c.period_id
            WHERE s.period_id = ?
              AND (s.full_name LIKE ? OR s.nis LIKE ?)
              {$excludeSql}
            ORDER BY s.class ASC, s.full_name ASC
            LIMIT {$limit}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodId, $term, $term]);
        return $stmt->fetchAll();
    }

    public function filterPaginated(int|string $periodId, array $filters = [], int $page = 1, int $perPage = 50): array
    {
        $conditions = ['s.period_id = ?'];
        $bindings = [$periodId];

        if (!empty($filters['search'])) {
            $conditions[] = '(s.full_name LIKE ? OR s.nis LIKE ?)';
            $search = '%' . trim($filters['search']) . '%';
            $bindings[] = $search;
            $bindings[] = $search;
        }

        if (!empty($filters['class'])) {
            $conditions[] = 's.class = ?';
            $bindings[] = strtoupper(trim($filters['class']));
        }

        if (!empty($filters['grade'])) {
            $conditions[] = 's.grade = ?';
            $bindings[] = (int) $filters['grade'];
        }

        if (!empty($filters['category'])) {
            if ($filters['category'] === 'guru') {
                $conditions[] = "s.class = 'GURU'";
            } elseif ($filters['category'] === 'siswa') {
                $conditions[] = "s.class != 'GURU'";
            }
        }

        if (isset($filters['has_voted']) && $filters['has_voted'] !== '') {
            if ($filters['has_voted'] == '1') {
                $conditions[] = 'v.id IS NOT NULL';
            } elseif ($filters['has_voted'] == '0') {
                $conditions[] = 'v.id IS NULL';
            }
        }

        $whereSql = implode(' AND ', $conditions);

        // Count total
        $countSql = "
            SELECT COUNT(*) 
            FROM {$this->table} s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            WHERE {$whereSql}
        ";
        $stmtCount = $this->db->prepare($countSql);
        $stmtCount->execute($bindings);
        $total = (int) $stmtCount->fetchColumn();

        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $lastPage));
        $offset = ($page - 1) * $perPage;

        // Fetch records
        $sql = "
            SELECT s.*,
                   (CASE WHEN v.id IS NOT NULL THEN 1 ELSE 0 END) AS has_voted,
                   v.id AS vote_id,
                   v.voted_at,
                   c.full_name AS candidate_chosen,
                   c.class AS candidate_class,
                   (CASE WHEN cand.id IS NOT NULL THEN 1 ELSE 0 END) AS is_candidate
            FROM {$this->table} s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            LEFT JOIN candidates c ON v.candidate_id = c.id
            LEFT JOIN candidates cand ON UPPER(s.full_name) = UPPER(cand.full_name) 
                                      AND UPPER(s.class) = UPPER(cand.class) 
                                      AND s.period_id = cand.period_id
            WHERE {$whereSql}
            ORDER BY s.class ASC, s.full_name ASC
            LIMIT {$perPage} OFFSET {$offset}
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        $records = $stmt->fetchAll();

        return [
            'data' => $records,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
            ],
        ];
    }

    public function getCountsSummary(int|string $periodId): array
    {
        $sql = "
            SELECT 
                COUNT(*) AS total_all,
                SUM(CASE WHEN s.class != 'GURU' THEN 1 ELSE 0 END) AS total_siswa,
                SUM(CASE WHEN s.class = 'GURU' THEN 1 ELSE 0 END) AS total_guru,
                SUM(CASE WHEN v.id IS NOT NULL THEN 1 ELSE 0 END) AS total_voted,
                SUM(CASE WHEN v.id IS NULL THEN 1 ELSE 0 END) AS total_unvoted
            FROM {$this->table} s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            WHERE s.period_id = ?
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$periodId]);
        $row = $stmt->fetch();
        return [
            'total_all' => (int) ($row['total_all'] ?? 0),
            'total_siswa' => (int) ($row['total_siswa'] ?? 0),
            'total_guru' => (int) ($row['total_guru'] ?? 0),
            'total_voted' => (int) ($row['total_voted'] ?? 0),
            'total_unvoted' => (int) ($row['total_unvoted'] ?? 0),
        ];
    }

    public function countByPeriod(int|string $periodId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE period_id = ?");
        $stmt->execute([$periodId]);
        return (int) $stmt->fetchColumn();
    }

    public function getDistinctClasses(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT class 
            FROM {$this->table} 
            WHERE period_id = ? 
            ORDER BY class ASC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function importBulk(array $students, int|string $periodId, bool $skipDuplicateNis = false): array
    {
        $this->db->beginTransaction();
        $inserted = 0;
        $skipped = 0;

        try {
            // Existing NIS and teacher names in this period if skipping duplicates
            $existingNis = [];
            $existingTeachers = [];
            if ($skipDuplicateNis) {
                $stmtExisting = $this->db->prepare("SELECT nis FROM {$this->table} WHERE period_id = ? AND nis IS NOT NULL AND nis != ''");
                $stmtExisting->execute([$periodId]);
                $existingNis = array_fill_keys($stmtExisting->fetchAll(PDO::FETCH_COLUMN), true);

                $stmtTeachers = $this->db->prepare("SELECT full_name FROM {$this->table} WHERE period_id = ? AND class = 'GURU'");
                $stmtTeachers->execute([$periodId]);
                $existingTeachers = array_fill_keys(array_map('strtoupper', $stmtTeachers->fetchAll(PDO::FETCH_COLUMN)), true);
            }

            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (period_id, nis, full_name, class, grade)
                VALUES (?, ?, ?, ?, ?)
            ");

            foreach ($students as $s) {
                $nis = $s['nis'] ?: null;
                $nameUpper = strtoupper(trim($s['full_name']));

                if ($nis && $skipDuplicateNis && isset($existingNis[$nis])) {
                    $skipped++;
                    continue;
                }

                if ($s['class'] === 'GURU' && $skipDuplicateNis && isset($existingTeachers[$nameUpper])) {
                    $skipped++;
                    continue;
                }

                $stmt->execute([
                    $periodId,
                    $nis,
                    $s['full_name'],
                    $s['class'],
                    $s['grade'],
                ]);
                $inserted++;

                if ($nis) {
                    $existingNis[$nis] = true;
                }
                if ($s['class'] === 'GURU') {
                    $existingTeachers[$nameUpper] = true;
                }
            }

            $this->db->commit();
            return [
                'inserted' => $inserted,
                'skipped' => $skipped,
            ];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteAllInPeriod(int|string $periodId): int
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE period_id = ?");
        $stmt->execute([$periodId]);
        return $stmt->rowCount();
    }

    public function deleteMultiple(array $ids, int|string $periodId): int
    {
        if (empty($ids)) {
            return 0;
        }
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE period_id = ? AND id IN ({$placeholders})");
        $stmt->execute(array_merge([$periodId], array_values($ids)));
        return $stmt->rowCount();
    }

    public function exportAll(int|string $periodId, array $filters = []): array
    {
        $conditions = ['s.period_id = ?'];
        $bindings = [$periodId];

        if (!empty($filters['search'])) {
            $conditions[] = '(s.full_name LIKE ? OR s.nis LIKE ?)';
            $search = '%' . trim($filters['search']) . '%';
            $bindings[] = $search;
            $bindings[] = $search;
        }

        if (!empty($filters['class'])) {
            $conditions[] = 's.class = ?';
            $bindings[] = strtoupper(trim($filters['class']));
        }

        if (!empty($filters['category'])) {
            if ($filters['category'] === 'guru') {
                $conditions[] = "s.class = 'GURU'";
            } elseif ($filters['category'] === 'siswa') {
                $conditions[] = "s.class != 'GURU'";
            }
        }

        if (isset($filters['has_voted']) && $filters['has_voted'] !== '') {
            if ($filters['has_voted'] == '1') {
                $conditions[] = 'v.id IS NOT NULL';
            } elseif ($filters['has_voted'] == '0') {
                $conditions[] = 'v.id IS NULL';
            }
        }

        $whereSql = implode(' AND ', $conditions);

        $sql = "
            SELECT s.*,
                   (CASE WHEN v.id IS NOT NULL THEN 1 ELSE 0 END) AS has_voted,
                   v.voted_at,
                   c.full_name AS candidate_chosen,
                   c.class AS candidate_class
            FROM {$this->table} s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            LEFT JOIN candidates c ON v.candidate_id = c.id
            WHERE {$whereSql}
            ORDER BY 
                CASE WHEN s.class = 'GURU' THEN 1 ELSE 0 END ASC,
                s.class ASC, 
                s.full_name ASC
        ";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($bindings);
        return $stmt->fetchAll();
    }
}
