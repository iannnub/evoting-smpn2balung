<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Candidate extends Model
{
    protected string $table = 'candidates';

    public function getByPeriod(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id) AS total_votes
            FROM {$this->table} c
            WHERE c.period_id = ?
            ORDER BY c.order_number ASC, c.id ASC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }

    public function getActive(): array
    {
        $stmt = $this->db->query("
            SELECT c.*,
                   (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id) AS total_votes
            FROM {$this->table} c
            JOIN periods p ON c.period_id = p.id
            WHERE p.is_active = 1
            ORDER BY c.order_number ASC, c.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function findWithVotes(int|string $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT c.*,
                   p.name AS period_name,
                   p.is_active AS period_is_active,
                   (SELECT COUNT(*) FROM votes v WHERE v.candidate_id = c.id) AS total_votes
            FROM {$this->table} c
            JOIN periods p ON c.period_id = p.id
            WHERE c.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countVotes(int|string $id): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM votes WHERE candidate_id = ?");
        $stmt->execute([$id]);
        return (int) $stmt->fetchColumn();
    }

    public function isCandidate(string $fullName, string $class, int|string $periodId): bool
    {
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE period_id = ? 
              AND UPPER(full_name) = ? 
              AND UPPER(class) = ? 
            LIMIT 1
        ");
        $stmt->execute([$periodId, strtoupper(trim($fullName)), strtoupper(trim($class))]);
        return (bool) $stmt->fetchColumn();
    }
}
