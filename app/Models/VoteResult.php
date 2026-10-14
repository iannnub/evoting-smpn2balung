<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class VoteResult extends Model
{
    protected string $table = 'candidates';

    public function getByPeriod(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.id AS candidate_id,
                c.full_name AS candidate_name,
                c.class AS candidate_class,
                c.photo_filename AS photo_filename,
                c.order_number AS order_number,
                c.period_id AS period_id,
                COUNT(v.id) AS total_votes,
                ROUND(IFNULL(COUNT(v.id) * 100.0 / NULLIF((SELECT COUNT(*) FROM votes WHERE period_id = c.period_id), 0), 0), 1) AS vote_percentage
            FROM candidates c
            LEFT JOIN votes v ON c.id = v.candidate_id
            WHERE c.period_id = ?
            GROUP BY c.id, c.full_name, c.class, c.photo_filename, c.order_number, c.period_id
            ORDER BY total_votes DESC, c.order_number ASC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }

    public function getParticipationByClass(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.class,
                   COUNT(DISTINCT s.id) AS total_students,
                   COUNT(DISTINCT v.id) AS total_voted,
                   ROUND(COUNT(DISTINCT v.id) * 100.0 / NULLIF(COUNT(DISTINCT s.id), 0), 1) AS percentage
            FROM students s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            WHERE s.period_id = ?
            GROUP BY s.class
            ORDER BY s.class ASC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }

    public function getParticipationByGrade(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.grade,
                   COUNT(DISTINCT s.id) AS total_students,
                   COUNT(DISTINCT v.id) AS total_voted,
                   ROUND(COUNT(DISTINCT v.id) * 100.0 / NULLIF(COUNT(DISTINCT s.id), 0), 1) AS percentage
            FROM students s
            LEFT JOIN votes v ON s.id = v.student_id AND v.period_id = s.period_id
            WHERE s.period_id = ?
            GROUP BY s.grade
            ORDER BY s.grade ASC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }

    public function getAllVotesDetail(int|string $periodId): array
    {
        $stmt = $this->db->prepare("
            SELECT s.nis, s.full_name AS student_name, s.class AS student_class,
                   c.full_name AS candidate_name, c.class AS candidate_class,
                   v.voted_at, v.ip_address
            FROM votes v
            JOIN students s ON v.student_id = s.id
            JOIN candidates c ON v.candidate_id = c.id
            WHERE v.period_id = ?
            ORDER BY v.voted_at DESC
        ");
        $stmt->execute([$periodId]);
        return $stmt->fetchAll();
    }
}
