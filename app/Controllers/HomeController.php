<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Candidate;
use App\Models\Period;

class HomeController extends Controller
{
    private Period $periodModel;
    private Candidate $candidateModel;

    public function __construct()
    {
        $this->periodModel = new Period();
        $this->candidateModel = new Candidate();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $candidates = $activePeriod ? $this->candidateModel->getByPeriod($activePeriod['id']) : [];

        $this->view('home', [
            'title' => 'Pemilihan Ketua OSIS',
            'period' => $activePeriod,
            'candidates' => $candidates,
        ]);
    }

    public function candidate(string $id): void
    {
        $candidate = $this->candidateModel->findWithVotes($id);

        if (!$candidate) {
            http_response_code(404);
            $this->view('errors.404', ['title' => 'Kandidat Tidak Ditemukan'], 'none');
            return;
        }

        $activePeriod = $this->periodModel->getActive();

        $this->view('candidate-detail', [
            'title' => 'Kandidat: ' . $candidate['full_name'],
            'candidate' => $candidate,
            'activePeriod' => $activePeriod,
        ]);
    }

    public function apiCandidates(): never
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json([
                'success' => false,
                'message' => 'Tidak ada periode pemilihan yang sedang aktif.',
            ], 404);
        }

        $candidates = $this->candidateModel->getByPeriod($activePeriod['id']);
        $data = array_map(function ($c) {
            return [
                'id' => (int) $c['id'],
                'full_name' => $c['full_name'],
                'class' => $c['class'],
                'photo_url' => candidate_photo($c['photo_filename']),
                'vision' => $c['vision'],
                'mission' => $c['mission'],
                'order_number' => (int) $c['order_number'],
            ];
        }, $candidates);

        $this->json([
            'success' => true,
            'data' => $data,
            'message' => 'Data kandidat berhasil diambil.',
        ]);
    }

    public function apiCandidateDetail(string $id): never
    {
        $candidate = $this->candidateModel->findWithVotes($id);
        if (!$candidate) {
            $this->json([
                'success' => false,
                'message' => 'Kandidat tidak ditemukan.',
            ], 404);
        }

        $this->json([
            'success' => true,
            'data' => [
                'id' => (int) $candidate['id'],
                'period_id' => (int) $candidate['period_id'],
                'period_name' => $candidate['period_name'] ?? null,
                'full_name' => $candidate['full_name'],
                'class' => $candidate['class'],
                'photo_url' => candidate_photo($candidate['photo_filename']),
                'vision' => $candidate['vision'],
                'mission' => $candidate['mission'],
                'missions' => mission_items($candidate['mission']),
                'order_number' => (int) $candidate['order_number'],
                'total_votes' => (int) $candidate['total_votes'],
            ],
        ]);
    }

    public function apiActivePeriod(): never
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json([
                'success' => false,
                'data' => null,
                'message' => 'Belum ada periode pemilihan aktif.',
            ]);
        }

        $this->json([
            'success' => true,
            'data' => [
                'id' => (int) $activePeriod['id'],
                'name' => $activePeriod['name'],
                'start_date' => $activePeriod['start_date'],
                'end_date' => $activePeriod['end_date'],
                'is_active' => (bool) $activePeriod['is_active'],
                'is_results_visible' => (bool) $activePeriod['is_results_visible'],
                'is_voting_open' => (bool) $activePeriod['is_voting_open'],
                'require_nisn_verification' => (bool) $activePeriod['require_nisn_verification'],
            ],
        ]);
    }
}
