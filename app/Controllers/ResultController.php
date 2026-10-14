<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Period;
use App\Models\Vote;
use App\Models\VoteResult;

class ResultController extends Controller
{
    private Period $periodModel;
    private VoteResult $resultModel;
    private Vote $voteModel;

    public function __construct()
    {
        $this->periodModel = new Period();
        $this->resultModel = new VoteResult();
        $this->voteModel = new Vote();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $results = [];
        $stats = ['total_students' => 0, 'total_voted' => 0, 'percentage' => 0];

        if ($activePeriod && !empty($activePeriod['is_results_visible'])) {
            $results = $this->resultModel->getByPeriod($activePeriod['id']);
            $stats = $this->voteModel->getParticipation($activePeriod['id']);
        }

        $this->view('results', [
            'title' => 'Hasil Pemilihan Suara',
            'period' => $activePeriod,
            'results' => $results,
            'stats' => $stats,
        ]);
    }

    public function data(): never
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            $this->json(['success' => false, 'message' => 'Tidak ada periode aktif.'], 404);
        }

        if (empty($activePeriod['is_results_visible'])) {
            $this->json([
                'success' => false,
                'message' => 'Hasil voting saat ini disembunyikan oleh panitia pemilihan.',
            ], 403);
        }

        $results = $this->resultModel->getByPeriod($activePeriod['id']);
        $stats = $this->voteModel->getParticipation($activePeriod['id']);
        $byClass = $this->resultModel->getParticipationByClass($activePeriod['id']);
        $byGrade = $this->resultModel->getParticipationByGrade($activePeriod['id']);

        $formattedResults = array_map(function ($r) {
            return [
                'id' => (int) $r['candidate_id'],
                'candidate_id' => (int) $r['candidate_id'],
                'order_number' => (int) ($r['order_number'] ?? 0),
                'candidate_name' => $r['candidate_name'],
                'candidate_class' => $r['candidate_class'],
                'photo_filename' => $r['photo_filename'] ?? null,
                'photo_url' => candidate_photo($r['photo_filename'] ?? null),
                'total_votes' => (int) $r['total_votes'],
                'vote_percentage' => (float) $r['vote_percentage'],
            ];
        }, $results);

        $this->json([
            'success' => true,
            'data' => [
                'period' => [
                    'id' => (int) $activePeriod['id'],
                    'name' => $activePeriod['name'],
                    'is_results_visible' => (bool) $activePeriod['is_results_visible'],
                ],
                'results' => $formattedResults,
                'statistics' => [
                    'total_students' => (int) $stats['total_students'],
                    'total_voted' => (int) $stats['total_voted'],
                    'percentage' => (float) $stats['percentage'],
                    'participation_rate' => (float) $stats['percentage'],
                ],
                'by_class' => $byClass,
                'by_grade' => $byGrade,
            ],
        ]);
    }
}
