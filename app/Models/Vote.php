<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Vote extends Model
{
    protected string $table = 'votes';

    public function hasVoted(int|string $studentId, int|string $periodId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE student_id = ? AND period_id = ? LIMIT 1");
        $stmt->execute([$studentId, $periodId]);
        return (bool) $stmt->fetchColumn();
    }

    public function getVoteByStudent(int|string $studentId, int|string $periodId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT v.*, c.full_name AS candidate_name, c.class AS candidate_class
            FROM {$this->table} v
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.student_id = ? AND v.period_id = ?
            LIMIT 1
        ");
        $stmt->execute([$studentId, $periodId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function recordVote(int|string $studentId, int|string $candidateId, int|string $periodId, ?string $ip = null): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (student_id, candidate_id, period_id, ip_address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$studentId, $candidateId, $periodId, $ip]);
        return (int) $this->db->lastInsertId();
    }

    public function getParticipation(int|string $periodId): array
    {
        $stmtTotal = $this->db->prepare("SELECT COUNT(*) FROM students WHERE period_id = ?");
        $stmtTotal->execute([$periodId]);
        $totalStudents = (int) $stmtTotal->fetchColumn();

        $stmtVoted = $this->db->prepare("SELECT COUNT(*) FROM votes WHERE period_id = ?");
        $stmtVoted->execute([$periodId]);
        $totalVoted = (int) $stmtVoted->fetchColumn();

        return [
            'total_students' => $totalStudents,
            'total_voted' => $totalVoted,
            'percentage' => percent($totalVoted, $totalStudents),
        ];
    }

    public function resetVotes(int|string $periodId): int
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE period_id = ?");
        $stmt->execute([$periodId]);
        return $stmt->rowCount();
    }
}

