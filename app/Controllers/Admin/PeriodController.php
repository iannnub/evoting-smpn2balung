<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Period;
use App\Models\Vote;

class PeriodController extends Controller
{
    private Period $periodModel;
    private Vote $voteModel;

    public function __construct()
    {
        $this->periodModel = new Period();
        $this->voteModel = new Vote();
    }

    public function index(): void
    {
        $periods = $this->periodModel->getAllWithStats();

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => $periods,
            ]);
        }

        $this->view('admin.periods.index', [
            'title' => 'Kelola Periode Pemilihan',
            'pageTitle' => 'Periode Pemilihan OSIS',
            'periods' => $periods,
        ], 'admin');
    }

    public function create(): void
    {
        $this->view('admin.periods.form', [
            'title' => 'Buat Periode Baru',
            'pageTitle' => 'Buat Periode Baru',
            'period' => null,
            'isEdit' => false,
        ], 'admin');
    }

    public function store(): void
    {
        $name = trim((string) ($_POST['name'] ?? ''));
        $startDate = (string) ($_POST['start_date'] ?? date('Y-m-d'));
        $endDate = !empty($_POST['end_date']) ? (string) $_POST['end_date'] : null;
        $isResultsVisible = !empty($_POST['is_results_visible']) ? 1 : 0;
        $isVotingOpen = !empty($_POST['is_voting_open']) ? 1 : 0;
        $requireNisn = !empty($_POST['require_nisn_verification']) ? 1 : 0;
        $setAsActive = !empty($_POST['is_active']) ? 1 : 0;

        with_input($_POST);

        if (empty($name)) {
            flash('error', 'Nama periode wajib diisi.');
            $this->back();
        }

        if ($setAsActive) {
            $this->periodModel->activate(0); // Deactivate all
        }

        $id = $this->periodModel->create([
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $setAsActive ? 1 : 0,
            'is_results_visible' => $isResultsVisible,
            'is_voting_open' => $isVotingOpen,
            'require_nisn_verification' => $requireNisn,
        ]);

        if ($setAsActive) {
            $this->periodModel->activate($id);
        }

        flash('success', "Periode '{$name}' berhasil dibuat.");
        $this->redirect('/admin/periode');
    }

    public function edit(string $id): void
    {
        $period = $this->periodModel->find($id);
        if (!$period) {
            flash('error', 'Periode tidak ditemukan.');
            $this->redirect('/admin/periode');
        }

        $this->view('admin.periods.form', [
            'title' => 'Edit Periode',
            'pageTitle' => 'Edit Periode: ' . $period['name'],
            'period' => $period,
            'isEdit' => true,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $period = $this->periodModel->find($id);
        if (!$period) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Periode tidak ditemukan.'], 404);
            }
            flash('error', 'Periode tidak ditemukan.');
            $this->redirect('/admin/periode');
        }

        $input = is_api_request() ? (json_decode(file_get_contents('php://input'), true) ?? $_POST) : $_POST;

        $name = trim((string) ($input['name'] ?? ''));
        $startDate = (string) ($input['start_date'] ?? $period['start_date']);
        $endDate = !empty($input['end_date']) ? (string) $input['end_date'] : null;
        $isResultsVisible = !empty($input['is_results_visible']) ? 1 : 0;
        $isVotingOpen = !empty($input['is_voting_open']) ? 1 : 0;
        $requireNisn = !empty($input['require_nisn_verification']) ? 1 : 0;

        with_input($_POST);

        if (empty($name)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Nama periode wajib diisi.'], 400);
            }
            flash('error', 'Nama periode wajib diisi.');
            $this->back();
        }

        $this->periodModel->update($id, [
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_results_visible' => $isResultsVisible,
            'is_voting_open' => $isVotingOpen,
            'require_nisn_verification' => $requireNisn,
        ]);

        if (is_api_request()) {
            $this->json(['success' => true, 'message' => "Periode '{$name}' berhasil diperbarui."]);
        }

        flash('success', "Periode '{$name}' berhasil diperbarui.");
        $this->redirect('/admin/periode');
    }

    public function activate(string $id): void
    {
        $period = $this->periodModel->find($id);
        if (!$period) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Periode tidak ditemukan.'], 404);
            }
            flash('error', 'Periode tidak ditemukan.');
            $this->redirect('/admin/periode');
        }

        $this->periodModel->activate($id);
        if (is_api_request()) {
            $this->json(['success' => true, 'message' => "Periode '{$period['name']}' sekarang menjadi periode aktif."]);
        }
        flash('success', "Periode '{$period['name']}' sekarang menjadi periode aktif.");
        $this->redirect('/admin/periode');
    }

    public function toggleResults(string $id): void
    {
        $period = $this->periodModel->find($id);
        if ($period) {
            $this->periodModel->toggleResults($id);
            $newStatus = empty($period['is_results_visible']) ? 'ditampilkan ke publik' : 'disembunyikan dari publik';
            if (is_api_request()) {
                $this->json(['success' => true, 'message' => "Hasil voting untuk periode '{$period['name']}' sekarang {$newStatus}."]);
            }
            flash('success', "Hasil voting untuk periode '{$period['name']}' sekarang {$newStatus}.");
        }
        $this->back();
    }

    public function toggleVoting(string $id): void
    {
        $period = $this->periodModel->find($id);
        if ($period) {
            $this->periodModel->toggleVoting($id);
            $newStatus = empty($period['is_voting_open']) ? 'DIBUKA' : 'DITUTUP SEMENTARA';
            if (is_api_request()) {
                $this->json(['success' => true, 'message' => "Status bilik suara untuk periode '{$period['name']}' sekarang {$newStatus}."]);
            }
            flash('success', "Status bilik suara untuk periode '{$period['name']}' sekarang {$newStatus}.");
        }
        $this->back();
    }

    public function reset(): void
    {
        $confirmation = trim((string) ($_POST['confirmation'] ?? ''));
        $newName = trim((string) ($_POST['new_period_name'] ?? ''));
        $startDate = (string) ($_POST['start_date'] ?? date('Y-m-d'));

        if ($confirmation !== 'RESET') {
            flash('error', 'Konfirmasi reset salah. Anda harus mengetik kata "RESET" dengan huruf kapital.');
            $this->redirect('/admin/periode');
        }

        if (empty($newName)) {
            flash('error', 'Nama periode baru wajib diisi.');
            $this->redirect('/admin/periode');
        }

        $newId = $this->periodModel->reset($newName, $startDate);

        flash('success', "Voting berhasil direset! Periode baru '{$newName}' telah dibuat dan aktif. Data periode lama tetap aman tersimpan sebagai arsip.");
        $this->redirect('/admin/kandidat');
    }

    public function delete(string $id): void
    {
        $period = $this->periodModel->find($id);
        if (!$period) {
            flash('error', 'Periode tidak ditemukan.');
            $this->redirect('/admin/periode');
        }

        if ($period['is_active']) {
            flash('error', 'Periode yang sedang aktif tidak dapat dihapus. Silakan aktifkan periode lain terlebih dahulu.');
            $this->redirect('/admin/periode');
        }

        $this->periodModel->delete($id);
        flash('success', "Periode '{$period['name']}' beserta data terkait berhasil dihapus.");
        $this->redirect('/admin/periode');
    }

    public function resetVotes(?string $id = null): void
    {
        $activePeriod = $this->periodModel->getActive();
        $targetId = !empty($id) ? (int)$id : ($activePeriod['id'] ?? 0);

        if (!$targetId) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Tidak ada periode pemilihan yang aktif.'], 404);
            }
            flash('error', 'Tidak ada periode pemilihan yang aktif.');
            $this->redirect('/admin/hasil');
        }

        $period = $this->periodModel->find($targetId);
        if (!$period) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Periode tidak ditemukan.'], 404);
            }
            flash('error', 'Periode tidak ditemukan.');
            $this->redirect('/admin/hasil');
        }

        $deletedCount = $this->voteModel->resetVotes($targetId);
        $message = "Perolehan suara berhasil direset ke 0! Sebanyak {$deletedCount} suara telah dikosongkan. Data DPT (Siswa & Guru) serta Paslon tetap utuh.";

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'period_id' => $targetId,
                    'period_name' => $period['name'],
                    'deleted_votes' => $deletedCount,
                ],
            ]);
        }

        flash('success', $message);
        $this->redirect('/admin/hasil');
    }
}

