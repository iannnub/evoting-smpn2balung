<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Admin extends Model
{
    protected string $table = 'admins';

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? LIMIT 1");
        $stmt->execute([trim($email)]);
        $res = $stmt->fetch();
        return $res ?: null;
    }

    public function updateLastLogin(int|string $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function updatePassword(int|string $id, string $newPasswordHash): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$newPasswordHash, $id]);
    }

    public function updateProfile(int|string $id, string $email, string $fullName): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET email = ?, full_name = ? WHERE id = ?");
        return $stmt->execute([$email, $fullName, $id]);
    }
}
