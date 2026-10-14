<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Candidate;
use App\Models\Period;
use App\Models\Vote;
use App\Models\VoteResult;

class DashboardController extends Controller
{
    private Period $periodModel;
    private Candidate $candidateModel;
    private Vote $voteModel;
    private VoteResult $resultModel;

    public function __construct()
    {
        $this->periodModel = new Period();
        $this->candidateModel = new Candidate();
        $this->voteModel = new Vote();
        $this->resultModel = new VoteResult();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $candidates = [];
        $stats = ['total_students' => 0, 'total_voted' => 0, 'percentage' => 0];
        $results = [];

        if ($activePeriod) {
            $candidates = $this->candidateModel->getByPeriod($activePeriod['id']);
            $stats = $this->voteModel->getParticipation($activePeriod['id']);
            $results = $this->resultModel->getByPeriod($activePeriod['id']);
        }

        $this->view('admin.dashboard', [
            'title' => 'Dashboard',
            'pageTitle' => 'Dashboard Utama',
            'period' => $activePeriod,
            'candidates' => $candidates,
            'stats' => $stats,
            'results' => $results,
        ], 'admin');
    }
}
