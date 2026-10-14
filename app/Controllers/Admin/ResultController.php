<?php

namespace App\Controllers\Admin;

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
        $periodId = $_GET['period_id'] ?? ($activePeriod['id'] ?? 0);
        $allPeriods = $this->periodModel->all('id DESC');

        $period = $periodId ? $this->periodModel->find($periodId) : null;
        $rawResults = $periodId ? $this->resultModel->getByPeriod($periodId) : [];
        $results = array_map(function ($r) {
            $r['id'] = (int) $r['candidate_id'];
            $r['photo_url'] = candidate_photo($r['photo_filename'] ?? null);
            return $r;
        }, $rawResults);
        $stats = $periodId ? $this->voteModel->getParticipation($periodId) : ['total_students' => 0, 'total_voted' => 0, 'percentage' => 0];
        $byClass = $periodId ? $this->resultModel->getParticipationByClass($periodId) : [];
        $byGrade = $periodId ? $this->resultModel->getParticipationByGrade($periodId) : [];
        $votesDetail = $periodId ? array_slice($this->resultModel->getAllVotesDetail($periodId), 0, 100) : [];

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => [
                    'period' => $period,
                    'selected_period_id' => $periodId,
                    'all_periods' => $allPeriods,
                    'results' => $results,
                    'statistics' => $stats,
                    'by_class' => $byClass,
                    'by_grade' => $byGrade,
                    'recent_votes' => $votesDetail,
                ],
            ]);
        }

        $this->view('admin.results.index', [
            'title' => 'Hasil & Laporan Pemilihan',
            'pageTitle' => 'Rekapitulasi Hasil & Laporan',
            'period' => $period,
            'selectedPeriodId' => $periodId,
            'allPeriods' => $allPeriods,
            'results' => $results,
            'stats' => $stats,
            'byClass' => $byClass,
            'byGrade' => $byGrade,
            'votesDetail' => $votesDetail,
        ], 'admin');
    }

    public function export(): never
    {
        $activePeriod = $this->periodModel->getActive();
        $periodId = $_GET['period_id'] ?? ($activePeriod['id'] ?? 0);
        $period = $periodId ? $this->periodModel->find($periodId) : null;

        if (!$period) {
            flash('error', 'Pilih periode untuk export laporan.');
            $this->redirect('/admin/hasil');
        }

        $results = $this->resultModel->getByPeriod($periodId);
        $byClass = $this->resultModel->getParticipationByClass($periodId);
        $byGrade = $this->resultModel->getParticipationByGrade($periodId);
        $allVotes = $this->resultModel->getAllVotesDetail($periodId);

        // Prepare Sheet 1: Hasil Kandidat
        $sheetKandidat = [
            ['NO URUT', 'NAMA KANDIDAT', 'KELAS', 'TOTAL SUARA', 'PERSENTASE (%)']
        ];
        foreach ($results as $r) {
            $sheetKandidat[] = [
                $r['order_number'],
                $r['candidate_name'],
                $r['candidate_class'],
                (int) $r['total_votes'],
                $r['vote_percentage'] . '%'
            ];
        }

        // Prepare Sheet 2: Partisipasi Kelas
        $sheetKelas = [
            ['KELAS', 'TOTAL SISWA', 'SUDAH MEMILIH', 'PARTISIPASI (%)']
        ];
        foreach ($byClass as $c) {
            $sheetKelas[] = [
                $c['class'],
                (int) $c['total_students'],
                (int) $c['total_voted'],
                $c['percentage'] . '%'
            ];
        }

        // Prepare Sheet 3: Partisipasi Tingkat
        $sheetTingkat = [
            ['TINGKAT', 'TOTAL SISWA', 'SUDAH MEMILIH', 'PARTISIPASI (%)']
        ];
        foreach ($byGrade as $g) {
            $sheetTingkat[] = [
                grade_label((int)$g['grade']),
                (int) $g['total_students'],
                (int) $g['total_voted'],
                $g['percentage'] . '%'
            ];
        }

        // Prepare Sheet 4: Log Suara
        $sheetLog = [
            ['NO', 'NISN', 'NAMA SISWA', 'KELAS', 'KANDIDAT DIPILIH', 'KELAS KANDIDAT', 'WAKTU VOTE', 'IP ADDRESS']
        ];
        $i = 0;
        foreach ($allVotes as $v) {
            $i++;
            $sheetLog[] = [
                $i,
                $v['nis'] ?: '-',
                $v['student_name'],
                $v['student_class'],
                $v['candidate_name'],
                $v['candidate_class'],
                $v['voted_at'],
                $v['ip_address'] ?: '-'
            ];
        }

        $sheets = [
            'Hasil Kandidat' => $sheetKandidat,
            'Partisipasi Kelas' => $sheetKelas,
            'Partisipasi Tingkat' => $sheetTingkat,
            'Log Pemilih' => $sheetLog,
        ];

        $tempFile = tempnam(sys_get_temp_dir(), 'rep_') . '.xlsx';
        write_xlsx($sheets, $tempFile);

        $safePeriod = preg_replace('/[^a-zA-Z0-9_-]/', '_', $period['name']);
        $filename = "Laporan_{$safePeriod}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        readfile($tempFile);
        @unlink($tempFile);
        exit;
    }
}
