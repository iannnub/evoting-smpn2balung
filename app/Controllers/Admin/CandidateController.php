<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Candidate;
use App\Models\Period;

class CandidateController extends Controller
{
    private Candidate $candidateModel;
    private Period $periodModel;

    public function __construct()
    {
        $this->candidateModel = new Candidate();
        $this->periodModel = new Period();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $periodId = $_GET['period_id'] ?? ($activePeriod['id'] ?? 0);
        
        $candidates = $periodId ? $this->candidateModel->getByPeriod($periodId) : [];
        $allPeriods = $this->periodModel->all('id DESC');

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => [
                    'candidates' => $candidates,
                    'active_period' => $activePeriod,
                    'all_periods' => $allPeriods,
                ],
            ]);
        }

        $this->view('admin.candidates.index', [
            'title' => 'Kelola Kandidat',
            'pageTitle' => 'Daftar Kandidat OSIS',
            'candidates' => $candidates,
            'activePeriod' => $activePeriod,
            'selectedPeriodId' => $periodId,
            'allPeriods' => $allPeriods,
        ], 'admin');
    }

    public function create(): void
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            flash('warning', 'Harap aktifkan atau buat periode pemilihan terlebih dahulu sebelum menambah kandidat.');
            $this->redirect('/admin/periode');
        }

        $this->view('admin.candidates.form', [
            'title' => 'Tambah Kandidat',
            'pageTitle' => 'Tambah Kandidat Baru',
            'candidate' => null,
            'period' => $activePeriod,
            'isEdit' => false,
        ], 'admin');
    }

    public function store(): void
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            flash('error', 'Tidak ada periode aktif.');
            $this->redirect('/admin/periode');
        }

        $fullName = clean_name((string) ($_POST['full_name'] ?? ''));
        $classRaw = trim((string) ($_POST['class'] ?? ''));
        $orderNumber = (int) ($_POST['order_number'] ?? 1);
        $vision = trim((string) ($_POST['vision'] ?? ''));
        $mission = trim((string) ($_POST['mission'] ?? ''));

        with_input($_POST);

        $class = normalize_class($classRaw);

        if (is_api_request()) {
            if (empty($fullName)) {
                $this->json(['success' => false, 'message' => 'Nama lengkap kandidat wajib diisi.'], 400);
            }
            if (!$class) {
                $this->json(['success' => false, 'message' => 'Format kelas tidak valid (contoh: 7A, 8B, 9H).'], 400);
            }
            if (!empty($_FILES['photo']['name'])) {
                $upload = upload_candidate_photo($_FILES['photo']);
                if (!$upload['success']) {
                    $this->json(['success' => false, 'message' => $upload['error']], 400);
                }
                $photoFilename = $upload['filename'];
            }

            $candId = $this->candidateModel->create([
                'period_id' => $activePeriod['id'],
                'full_name' => $fullName,
                'class' => $class,
                'order_number' => $orderNumber,
                'photo_filename' => $photoFilename,
                'vision' => $vision,
                'mission' => $mission,
            ]);

            $this->json(['success' => true, 'data' => ['id' => $candId], 'message' => "Kandidat '{$fullName}' berhasil ditambahkan."]);
        }

        if (empty($fullName)) {
            flash('error', 'Nama lengkap kandidat wajib diisi.');
            $this->back();
        }

        if (!$class) {
            flash('error', 'Format kelas tidak valid (contoh: 7A, 8B, 9H).');
            $this->back();
        }

        // Upload photo
        $photoFilename = null;
        if (!empty($_FILES['photo']['name'])) {
            $upload = upload_candidate_photo($_FILES['photo']);
            if (!$upload['success']) {
                flash('error', $upload['error']);
                $this->back();
            }
            $photoFilename = $upload['filename'];
        }

        $this->candidateModel->create([
            'period_id' => $activePeriod['id'],
            'full_name' => $fullName,
            'class' => $class,
            'order_number' => $orderNumber,
            'photo_filename' => $photoFilename,
            'vision' => $vision,
            'mission' => $mission,
        ]);

        flash('success', "Kandidat '{$fullName}' berhasil ditambahkan.");
        $this->redirect('/admin/kandidat');
    }

    public function edit(string $id): void
    {
        $candidate = $this->candidateModel->find($id);
        if (!$candidate) {
            flash('error', 'Kandidat tidak ditemukan.');
            $this->redirect('/admin/kandidat');
        }

        $activePeriod = $this->periodModel->find($candidate['period_id']);

        $this->view('admin.candidates.form', [
            'title' => 'Edit Kandidat',
            'pageTitle' => 'Edit Data Kandidat: ' . $candidate['full_name'],
            'candidate' => $candidate,
            'period' => $activePeriod,
            'isEdit' => true,
        ], 'admin');
    }

    public function update(string $id): void
    {
        $candidate = $this->candidateModel->find($id);
        if (!$candidate) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.'], 404);
            }
            flash('error', 'Kandidat tidak ditemukan.');
            $this->redirect('/admin/kandidat');
        }

        $input = is_api_request() && empty($_POST) ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;

        $fullName = clean_name((string) ($input['full_name'] ?? ''));
        $classRaw = trim((string) ($input['class'] ?? ''));
        $orderNumber = (int) ($input['order_number'] ?? $candidate['order_number']);
        $vision = trim((string) ($input['vision'] ?? ''));
        $mission = trim((string) ($input['mission'] ?? ''));

        with_input($input);

        $class = normalize_class($classRaw);

        if (is_api_request()) {
            if (empty($fullName)) {
                $this->json(['success' => false, 'message' => 'Nama lengkap kandidat wajib diisi.'], 400);
            }
            if (!$class) {
                $this->json(['success' => false, 'message' => 'Format kelas tidak valid (contoh: 7A, 8B, 9H).'], 400);
            }
            $photoFilename = $candidate['photo_filename'];
            if (!empty($_FILES['photo']['name'])) {
                $upload = upload_candidate_photo($_FILES['photo']);
                if (!$upload['success']) {
                    $this->json(['success' => false, 'message' => $upload['error']], 400);
                }
                delete_candidate_photo($candidate['photo_filename']);
                $photoFilename = $upload['filename'];
            }

            $this->candidateModel->update($id, [
                'full_name' => $fullName,
                'class' => $class,
                'order_number' => $orderNumber,
                'photo_filename' => $photoFilename,
                'vision' => $vision,
                'mission' => $mission,
            ]);

            $this->json(['success' => true, 'message' => "Kandidat '{$fullName}' berhasil diperbarui."]);
        }

        if (empty($fullName)) {
            flash('error', 'Nama lengkap kandidat wajib diisi.');
            $this->back();
        }

        if (!$class) {
            flash('error', 'Format kelas tidak valid (contoh: 7A, 8B, 9H).');
            $this->back();
        }

        $photoFilename = $candidate['photo_filename'];

        // If new photo uploaded
        if (!empty($_FILES['photo']['name'])) {
            $upload = upload_candidate_photo($_FILES['photo']);
            if (!$upload['success']) {
                flash('error', $upload['error']);
                $this->back();
            }
            // Delete old photo
            delete_candidate_photo($candidate['photo_filename']);
            $photoFilename = $upload['filename'];
        }

        $this->candidateModel->update($id, [
            'full_name' => $fullName,
            'class' => $class,
            'order_number' => $orderNumber,
            'photo_filename' => $photoFilename,
            'vision' => $vision,
            'mission' => $mission,
        ]);

        flash('success', "Kandidat '{$fullName}' berhasil diperbarui.");
        $this->redirect('/admin/kandidat');
    }

    public function delete(string $id): void
    {
        $candidate = $this->candidateModel->find($id);
        if (!$candidate) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Kandidat tidak ditemukan.'], 404);
            }
            flash('error', 'Kandidat tidak ditemukan.');
            $this->redirect('/admin/kandidat');
        }

        // Delete photo from disk
        delete_candidate_photo($candidate['photo_filename']);

        // Delete candidate record (votes cascade deleted)
        $this->candidateModel->delete($id);

        if (is_api_request()) {
            $this->json(['success' => true, 'message' => "Kandidat '{$candidate['full_name']}' berhasil dihapus."]);
        }

        flash('success', "Kandidat '{$candidate['full_name']}' berhasil dihapus.");
        $this->redirect('/admin/kandidat');
    }
}
