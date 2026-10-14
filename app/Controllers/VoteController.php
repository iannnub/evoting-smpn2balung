<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Candidate;
use App\Models\Period;
use App\Models\Student;
use App\Models\Vote;

class VoteController extends Controller
{
    private Period $periodModel;
    private Candidate $candidateModel;
    private Student $studentModel;
    private Vote $voteModel;

    public function __construct()
    {
        $this->periodModel = new Period();
        $this->candidateModel = new Candidate();
        $this->studentModel = new Student();
        $this->voteModel = new Vote();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $candidates = $activePeriod ? $this->candidateModel->getByPeriod($activePeriod['id']) : [];

        $this->view('vote', [
            'title' => 'Bilik Suara Digital',
            'period' => $activePeriod,
            'candidates' => $candidates,
        ]);
    }

    public function search(): never
    {
        $q = trim((string) ($_GET['q'] ?? ''));
        if (mb_strlen($q) < 3) {
            $this->json([
                'success' => false,
                'message' => 'Kata kunci pencarian minimal 3 karakter.',
            ], 400);
        }

        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json([
                'success' => false,
                'message' => 'Tidak ada periode pemilihan yang sedang aktif.',
            ], 404);
        }

        $excludeCandidates = !isset($_GET['exclude_candidates']) || $_GET['exclude_candidates'] === '1' || $_GET['exclude_candidates'] === 'true';
        $students = $this->studentModel->search($q, $activePeriod['id'], 15, $excludeCandidates);

        $this->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function check(string $id): never
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json([
                'success' => false,
                'message' => 'Tidak ada periode aktif.',
            ], 404);
        }

        $student = $this->studentModel->find($id);
        if (!$student || $student['period_id'] != $activePeriod['id']) {
            $this->json([
                'success' => false,
                'message' => 'Data siswa tidak ditemukan pada periode ini.',
            ], 404);
        }

        $hasVoted = $this->voteModel->hasVoted($id, $activePeriod['id']);
        $voteDetail = $hasVoted ? $this->voteModel->getVoteByStudent($id, $activePeriod['id']) : null;

        $this->json([
            'success' => true,
            'data' => [
                'has_voted' => $hasVoted,
                'voted_at' => $voteDetail['voted_at'] ?? null,
                'candidate_name' => $voteDetail['candidate_name'] ?? null,
            ],
        ]);
    }

    public function submit(): never
    {
        // 1. Rate limiting: 5 requests per IP per minute
        $ip = client_ip();
        if (!rate_limit("vote_{$ip}", 5, 60)) {
            $this->json([
                'success' => false,
                'message' => 'Terlalu banyak percobaan dalam waktu singkat. Mohon tunggu 1 menit.',
            ], 429);
        }

        // 2. Parse request payload
        $body = request_json();
        $studentId = (int) ($body['student_id'] ?? 0);
        $candidateId = (int) ($body['candidate_id'] ?? 0);

        if (!$studentId || !$candidateId) {
            $this->json([
                'success' => false,
                'message' => 'Pilihan siswa dan kandidat wajib diisi.',
            ], 422);
        }

        // 3. Verify active period
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json([
                'success' => false,
                'message' => 'Pemilihan tidak dapat dilakukan karena tidak ada periode aktif.',
            ], 400);
        }

        if (empty($activePeriod['is_voting_open'])) {
            $this->json([
                'success' => false,
                'message' => 'Bilik suara saat ini sedang ditutup oleh panitia pemilihan.',
            ], 403);
        }

        // 4. Verify student
        $student = $this->studentModel->find($studentId);
        if (!$student || $student['period_id'] != $activePeriod['id']) {
            $this->json([
                'success' => false,
                'message' => 'Data siswa tidak valid atau tidak terdaftar di periode ini.',
            ], 404);
        }

        // 4.1 Conflict of interest: candidates cannot vote
        if ($this->candidateModel->isCandidate($student['full_name'], $student['class'], $activePeriod['id'])) {
            $this->json([
                'success' => false,
                'message' => 'Kandidat OSIS tidak dapat memberikan suara.',
            ], 403);
        }

        // 4.1 NISN verification check if enabled for period
        if (!empty($activePeriod['require_nisn_verification']) && !empty($student['nis'])) {
            $submittedLast4 = trim((string) ($body['nisn_last4'] ?? ''));
            $expectedLast4 = substr(trim($student['nis']), -4);
            if ($submittedLast4 !== $expectedLast4) {
                $this->json([
                    'success' => false,
                    'message' => 'Verifikasi gagal: 4 digit terakhir NISN tidak sesuai.',
                ], 403);
            }
        }

        // 5. Verify candidate
        $candidate = $this->candidateModel->find($candidateId);
        if (!$candidate || $candidate['period_id'] != $activePeriod['id']) {
            $this->json([
                'success' => false,
                'message' => 'Kandidat yang dipilih tidak valid pada periode ini.',
            ], 404);
        }

        // 6. Check if student already voted
        if ($this->voteModel->hasVoted($studentId, $activePeriod['id'])) {
            $this->json([
                'success' => false,
                'message' => 'Maaf, siswa ' . $student['full_name'] . ' sudah memberikan suara sebelumnya.',
            ], 409);
        }

        // 7. Record vote inside transaction
        try {
            $voteId = $this->voteModel->recordVote($studentId, $candidateId, $activePeriod['id'], $ip);

            $this->json([
                'success' => true,
                'data' => [
                    'vote_id' => $voteId,
                    'student_name' => $student['full_name'],
                    'candidate_name' => $candidate['full_name'],
                    'voted_at' => date('Y-m-d H:i:s'),
                ],
                'message' => 'Terima kasih, ' . $student['full_name'] . '! Suara Anda berhasil tersimpan.',
            ], 201);
        } catch (\PDOException $e) {
            // MySQL error 1062 = Duplicate entry for unique key
            if ($e->getCode() == 23000 || str_contains($e->getMessage(), '1062')) {
                $this->json([
                    'success' => false,
                    'message' => 'Siswa ini sudah memberikan suara.',
                ], 409);
            }

            error_log('Vote error: ' . $e->getMessage());
            $this->json([
                'success' => false,
                'message' => 'Gagal menyimpan suara ke sistem. Silakan ulangi.',
            ], 500);
        }
    }
}
