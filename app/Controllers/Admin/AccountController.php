<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Admin;

class AccountController extends Controller
{
    private Admin $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    public function show(): void
    {
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->find($adminId);

        $this->view('admin.account', [
            'title' => 'Pengaturan Akun',
            'pageTitle' => 'Pengaturan Akun Admin',
            'admin' => $admin,
        ], 'admin');
    }

    public function update(): void
    {
        $adminId = Session::get('admin_id');
        $admin = $this->adminModel->find($adminId);

        $fullName = clean_name((string) ($_POST['full_name'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));
        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Format email tidak valid.');
            $this->back();
        }

        // If email changed, check uniqueness
        if ($email !== $admin['email']) {
            $existing = $this->adminModel->findByEmail($email);
            if ($existing && $existing['id'] != $adminId) {
                flash('error', 'Email ini sudah digunakan oleh akun lain.');
                $this->back();
            }
        }

        $this->adminModel->updateProfile($adminId, $email, $fullName);
        Session::set('admin_email', $email);
        Session::set('admin_name', $fullName);

        // Password change logic
        if (!empty($newPassword)) {
            if (empty($currentPassword)) {
                flash('error', 'Masukkan password saat ini untuk mengganti password baru.');
                $this->back();
            }

            if (!password_verify($currentPassword, $admin['password_hash'])) {
                flash('error', 'Password saat ini salah.');
                $this->back();
            }

            if ($newPassword !== $confirmPassword) {
                flash('error', 'Konfirmasi password baru tidak cocok.');
                $this->back();
            }

            if (!is_strong_password($newPassword)) {
                flash('error', 'Password baru minimal 8 karakter dan harus mengandung kombinasi huruf serta angka.');
                $this->back();
            }

            $newHash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 10]);
            $this->adminModel->updatePassword($adminId, $newHash);
            flash('success', 'Profil dan password akun admin berhasil diperbarui.');
            $this->redirect('/admin/akun');
        }

        flash('success', 'Profil akun admin berhasil diperbarui.');
        $this->redirect('/admin/akun');
    }
}
