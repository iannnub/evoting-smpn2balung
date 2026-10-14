<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Admin;

class AuthController extends Controller
{
    private Admin $adminModel;

    public function __construct()
    {
        $this->adminModel = new Admin();
    }

    public function showLogin(): void
    {
        if (Session::has('admin_id')) {
            $this->redirect('/admin/dashboard');
        }

        $this->view('admin.login', [
            'title' => 'Login Admin',
        ], 'blank');
    }

    public function login(): void
    {
        $body = request_json();
        $email = trim((string) ($body['email'] ?? $_POST['email'] ?? ''));
        $password = (string) ($body['password'] ?? $_POST['password'] ?? '');

        if (is_api_request()) {
            if (empty($email) || empty($password)) {
                $this->json(['success' => false, 'message' => 'Email dan kata sandi wajib diisi.'], 422);
            }
            $admin = $this->adminModel->findByEmail($email);
            if (!$admin || !password_verify($password, $admin['password_hash'])) {
                $this->json(['success' => false, 'message' => 'Email atau kata sandi salah.'], 401);
            }
            Session::regenerate();
            Session::set('admin_id', (int) $admin['id']);
            Session::set('admin_email', $admin['email']);
            Session::set('admin_name', $admin['full_name'] ?? 'Administrator');
            $this->adminModel->updateLastLogin($admin['id']);

            $this->json([
                'success' => true,
                'data' => [
                    'id' => (int) $admin['id'],
                    'email' => $admin['email'],
                    'full_name' => $admin['full_name'],
                ],
                'message' => 'Login berhasil.',
            ]);
        }

        with_input($_POST);

        if (empty($email) || empty($password)) {
            flash('error', 'Email dan password wajib diisi.');
            $this->back();
        }

        $admin = $this->adminModel->findByEmail($email);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            flash('error', 'Email atau password salah.');
            $this->back();
        }

        // Login success: regenerate session id to prevent session fixation
        Session::regenerate();
        Session::set('admin_id', (int) $admin['id']);
        Session::set('admin_email', $admin['email']);
        Session::set('admin_name', $admin['full_name'] ?? 'Administrator');

        $this->adminModel->updateLastLogin($admin['id']);

        flash('success', 'Selamat datang kembali, ' . ($admin['full_name'] ?? 'Admin') . '!');
        $this->redirect('/admin/dashboard');
    }

    public function me(): never
    {
        $adminId = Session::get('admin_id');
        if (!$adminId) {
            $this->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $admin = $this->adminModel->find($adminId);
        $this->json([
            'success' => true,
            'data' => [
                'id' => (int) $admin['id'],
                'email' => $admin['email'],
                'full_name' => $admin['full_name'],
            ],
        ]);
    }

    public function logout(): void
    {
        Session::destroy();
        session_start();

        if (is_api_request()) {
            $this->json(['success' => true, 'message' => 'Logout berhasil.']);
        }

        flash('info', 'Anda telah berhasil keluar.');
        $this->redirect('/admin/login');
    }
}
