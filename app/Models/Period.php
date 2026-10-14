<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Period extends Model
{
    protected string $table = 'periods';

    public function getActive(): ?array
    {
        $stmt = $this->db->query("SELECT * FROM {$this->table} WHERE is_active = 1 LIMIT 1");
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function activate(int|string $id): bool
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec("UPDATE {$this->table} SET is_active = 0");
            $stmt = $this->db->prepare("UPDATE {$this->table} SET is_active = 1 WHERE id = ?");
            $stmt->execute([$id]);
            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function toggleResults(int|string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_results_visible = IF(is_results_visible = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function toggleVoting(int|string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET is_voting_open = IF(is_voting_open = 1, 0, 1) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getAllWithStats(): array
    {
        $sql = "SELECT p.*,
                (SELECT COUNT(*) FROM candidates c WHERE c.period_id = p.id) AS total_candidates,
                (SELECT COUNT(*) FROM students s WHERE s.period_id = p.id) AS total_students,
                (SELECT COUNT(*) FROM votes v WHERE v.period_id = p.id) AS total_votes
                FROM {$this->table} p
                ORDER BY p.is_active DESC, p.id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function reset(string $newName, string $startDate, ?string $endDate = null): int
    {
        $this->db->beginTransaction();
        try {
            $this->db->exec("UPDATE {$this->table} SET is_active = 0");
            $stmt = $this->db->prepare("INSERT INTO {$this->table} (name, start_date, end_date, is_active, is_results_visible, is_voting_open) VALUES (?, ?, ?, 1, 1, 1)");
            $stmt->execute([$newName, $startDate, $endDate ?: null]);
            $newId = (int) $this->db->lastInsertId();
            $this->db->commit();
            return $newId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
