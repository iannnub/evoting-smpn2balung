<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Period;
use App\Models\Student;

class StudentController extends Controller
{
    private Student $studentModel;
    private Period $periodModel;

    public function __construct()
    {
        $this->studentModel = new Student();
        $this->periodModel = new Period();
    }

    public function index(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $periodId = $_GET['period_id'] ?? ($activePeriod['id'] ?? 0);

        $filters = [
            'search' => $_GET['search'] ?? '',
            'class' => $_GET['class'] ?? '',
            'grade' => $_GET['grade'] ?? '',
            'category' => $_GET['category'] ?? '',
            'has_voted' => $_GET['has_voted'] ?? '',
        ];
        $page = max(1, (int) ($_GET['page'] ?? 1));

        $result = $periodId ? $this->studentModel->filterPaginated($periodId, $filters, $page, 50) : ['data' => [], 'meta' => ['total' => 0, 'per_page' => 50, 'current_page' => 1, 'last_page' => 1, 'from' => 0, 'to' => 0]];
        $classes = $periodId ? $this->studentModel->getDistinctClasses($periodId) : [];
        $countsSummary = $periodId ? $this->studentModel->getCountsSummary($periodId) : [
            'total_all' => 0, 'total_siswa' => 0, 'total_guru' => 0, 'total_voted' => 0, 'total_unvoted' => 0
        ];
        $allPeriods = $this->periodModel->all('id DESC');

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => [
                    'students' => $result['data'],
                    'data' => $result['data'],
                    'meta' => $result['meta'],
                    'counts' => $countsSummary,
                    'classes' => $classes,
                    'active_period' => $activePeriod,
                    'all_periods' => $allPeriods,
                ],
                'students' => $result['data'],
                'meta' => $result['meta'],
                'counts' => $countsSummary,
                'classes' => $classes,
                'active_period' => $activePeriod,
                'all_periods' => $allPeriods,
            ]);
        }

        $this->view('admin.students.index', [
            'title' => 'Daftar Pemilih / DPT (Siswa & Guru)',
            'pageTitle' => 'Daftar Pemilih Tetap (DPT)',
            'students' => $result['data'],
            'meta' => $result['meta'],
            'counts' => $countsSummary,
            'filters' => $filters,
            'classes' => $classes,
            'activePeriod' => $activePeriod,
            'selectedPeriodId' => $periodId,
            'allPeriods' => $allPeriods,
        ], 'admin');
    }

    public function store(): void
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            flash('error', 'Tidak ada periode aktif.');
            $this->redirect('/admin/siswa');
        }

        $fullName = clean_name((string) ($_POST['full_name'] ?? ''));
        $nis = trim(preg_replace('/[^0-9]/', '', (string) ($_POST['nis'] ?? '')));
        $classRaw = trim((string) ($_POST['class'] ?? ''));

        $class = normalize_class($classRaw);

        if (empty($fullName)) {
            flash('error', 'Nama siswa wajib diisi.');
            $this->back();
        }

        if (!$class) {
            flash('error', 'Kelas tidak valid. Contoh: 7A, 8B, 9H.');
            $this->back();
        }

        $grade = grade_from_class($class);

        $this->studentModel->create([
            'period_id' => $activePeriod['id'],
            'full_name' => $fullName,
            'nis' => $nis ?: null,
            'class' => $class,
            'grade' => $grade,
        ]);

        flash('success', "Siswa '{$fullName}' berhasil ditambahkan ke DPT.");
        $this->redirect('/admin/siswa');
    }

    public function delete(string $id): void
    {
        $student = $this->studentModel->find($id);
        if ($student) {
            $this->studentModel->delete($id);
            flash('success', "Data siswa '{$student['full_name']}' berhasil dihapus.");
        }
        $this->redirect('/admin/siswa');
    }

    public function bulkDelete(): void
    {
        $activePeriod = $this->periodModel->getActive();
        $ids = $_POST['student_ids'] ?? [];

        if (empty($ids) || !is_array($ids)) {
            flash('warning', 'Pilih minimal satu siswa untuk dihapus.');
            $this->redirect('/admin/siswa');
        }

        $deleted = $this->studentModel->deleteMultiple($ids, $activePeriod['id']);
        flash('success', "{$deleted} data siswa terpilih berhasil dihapus.");
        $this->redirect('/admin/siswa');
    }

    public function deleteAll(): void
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            flash('error', 'Tidak ada periode aktif.');
            $this->redirect('/admin/siswa');
        }

        $deleted = $this->studentModel->deleteAllInPeriod($activePeriod['id']);
        flash('success', "Seluruh data DPT ({$deleted} siswa) pada periode ini berhasil dikosongkan.");
        $this->redirect('/admin/siswa');
    }

    public function importForm(): void
    {
        $activePeriod = $this->periodModel->getActive();

        $this->view('admin.students.import', [
            'title' => 'Import Siswa dari Excel/CSV',
            'pageTitle' => 'Import DPT Siswa',
            'period' => $activePeriod,
        ], 'admin');
    }

    public function importPreview(): void
    {
        $activePeriod = $this->periodModel->getActive();
        if (!$activePeriod) {
            flash('error', 'Tidak ada periode aktif untuk menampung data import.');
            $this->redirect('/admin/periode');
        }

        if (empty($_FILES['file']['name']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Silakan pilih file Excel (.xlsx) atau CSV terlebih dahulu.'], 400);
            }
            flash('error', 'Silakan pilih file Excel (.xlsx) atau CSV terlebih dahulu.');
            $this->redirect('/admin/import');
        }

        $tmpFile = $_FILES['file']['tmp_name'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, ['xlsx', 'csv', 'txt'], true)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Format file tidak didukung. Harap upload file .xlsx atau .csv.'], 400);
            }
            flash('error', 'Format file tidak didukung. Harap upload file .xlsx atau .csv.');
            $this->redirect('/admin/import');
        }

        try {
            $rows = read_spreadsheet($tmpFile, $ext);
        } catch (\Throwable $e) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Gagal membaca isi spreadsheet: ' . $e->getMessage()], 400);
            }
            flash('error', 'Gagal membaca isi spreadsheet: ' . $e->getMessage());
            $this->redirect('/admin/import');
        }

        if (empty($rows)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'File spreadsheet kosong atau tidak berisi data.'], 400);
            }
            flash('error', 'File spreadsheet kosong atau tidak berisi data.');
            $this->redirect('/admin/import');
        }

        // Find header row containing NAMA and KELAS (or NAMA_GURU for teacher files)
        $headerIndex = null;
        $colMap = [];
        $isTeacherImport = false;

        foreach ($rows as $rIdx => $row) {
            $rowUpper = array_map(fn($v) => strtoupper(trim((string)$v)), $row);

            // 1. Check student format: NAMA + KELAS
            $namaIdx = array_search('NAMA', $rowUpper);
            if ($namaIdx === false) $namaIdx = array_search('NAMA LENGKAP', $rowUpper);
            if ($namaIdx === false) $namaIdx = array_search('NAMA SISWA', $rowUpper);
            $kelasIdx = array_search('KELAS', $rowUpper);
            if ($kelasIdx === false) $kelasIdx = array_search('ROMBEL', $rowUpper);

            if ($namaIdx !== false && $kelasIdx !== false) {
                $headerIndex = $rIdx;
                $colMap['nama'] = $namaIdx;
                $colMap['kelas'] = $kelasIdx;
                $colMap['nisn'] = array_search('NISN', $rowUpper);
                if ($colMap['nisn'] === false) $colMap['nisn'] = array_search('NIS', $rowUpper);
                $colMap['no'] = array_search('NO', $rowUpper);
                $isTeacherImport = false;
                break;
            }

            // 2. Check teacher format: NAMA_GURU / NAMA GURU
            $guruIdx = array_search('NAMA_GURU', $rowUpper);
            if ($guruIdx === false) $guruIdx = array_search('NAMA GURU', $rowUpper);
            if ($guruIdx === false && in_array('GURU', $rowUpper, true) && $kelasIdx === false) {
                $guruIdx = array_search('GURU', $rowUpper);
            }

            if ($guruIdx !== false) {
                $headerIndex = $rIdx;
                $colMap['nama'] = $guruIdx;
                $colMap['kelas'] = false;
                $colMap['nisn'] = false;
                $colMap['no'] = array_search('NO', $rowUpper);
                $isTeacherImport = true;
                break;
            }
        }

        if ($headerIndex === null) {
            $errMsg = 'Header kolom tidak dikenali. Pastikan file siswa memuat kolom NO, NISN, NAMA, KELAS atau file guru memuat kolom NO, NAMA_GURU.';
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => $errMsg], 400);
            }
            flash('error', $errMsg);
            $this->redirect('/admin/import');
        }

        $parsedRows = [];
        $nisnCounts = [];
        $lastClass = null;
        $maxLimit = (int) config('app.import_max_rows', 1000);
        $warningsCount = 0;

        foreach ($rows as $rIdx => $row) {
            if ($rIdx <= $headerIndex) continue;

            $namaRaw = trim((string) ($row[$colMap['nama']] ?? ''));
            $kelasRaw = $colMap['kelas'] !== false ? trim((string) ($row[$colMap['kelas']] ?? '')) : '';
            $nisnRaw = $colMap['nisn'] !== false ? trim((string) ($row[$colMap['nisn']] ?? '')) : '';

            // Skip entirely empty row
            if ($namaRaw === '' && $kelasRaw === '' && $nisnRaw === '') {
                continue;
            }

            $fullName = clean_name($namaRaw);
            $warnings = [];

            if ($isTeacherImport) {
                $class = 'GURU';
                $grade = 4;
            } else {
                $class = normalize_class($kelasRaw);

                // If class is empty or invalid, try to inherit from preceding row if student name is present
                if (!$class && !empty($fullName) && !empty($lastClass)) {
                    $class = $lastClass;
                    $warnings[] = "Kelas kosong di file Excel, otomatis diisi {$class} mengikuti rombel baris sebelumnya";
                } elseif (!$class && !empty($fullName)) {
                    $warnings[] = "Kelas '{$kelasRaw}' tidak valid";
                }

                if ($class) {
                    $lastClass = $class;
                }

                $grade = $class ? grade_from_class($class) : 0;
            }

            // Clean NISN: remove noise like ';'
            $cleanNis = preg_replace('/[^0-9]/', '', $nisnRaw);
            // Left pad if numeric 8 or 9 digits
            if ($cleanNis !== '' && strlen($cleanNis) < 10) {
                $cleanNis = str_pad($cleanNis, 10, '0', STR_PAD_LEFT);
            }

            if (empty($fullName)) {
                $warnings[] = 'Nama kosong';
            }

            if (!$isTeacherImport) {
                if ($cleanNis === '') {
                    $warnings[] = 'NISN kosong';
                } else {
                    $nisnCounts[$cleanNis] = ($nisnCounts[$cleanNis] ?? 0) + 1;
                }
            }

            if (!empty($warnings)) {
                $warningsCount++;
            }

            $parsedRows[] = [
                'row_num' => count($parsedRows) + 1,
                'original_row' => $rIdx,
                'nis' => $cleanNis,
                'full_name' => $fullName,
                'class' => $class ?: $kelasRaw,
                'grade' => $grade,
                'is_valid' => !empty($fullName) && !empty($class),
                'warnings' => $warnings,
            ];

            if (count($parsedRows) >= $maxLimit) {
                break;
            }
        }

        // Mark duplicates in warning list
        foreach ($parsedRows as &$p) {
            if ($p['nis'] !== '' && ($nisnCounts[$p['nis']] ?? 0) > 1) {
                $p['warnings'][] = "NISN {$p['nis']} terulang " . $nisnCounts[$p['nis']] . "x";
            }
        }
        unset($p);

        // Save preview token to storage/imports/{token}.json
        $token = bin2hex(random_bytes(16));
        $saveDir = BASE_PATH . '/storage/imports';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0775, true);
        }

        file_put_contents($saveDir . '/' . $token . '.json', json_encode([
            'created_at' => time(),
            'period_id' => $activePeriod['id'],
            'filename' => $_FILES['file']['name'],
            'total' => count($parsedRows),
            'rows' => $parsedRows,
        ], JSON_UNESCAPED_UNICODE));

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'total' => count($parsedRows),
                    'warnings_count' => $warningsCount,
                    'sample_rows' => array_slice($parsedRows, 0, 5),
                ],
                'message' => "File Excel berhasil dibaca ({$warningsCount} baris dengan catatan).",
            ]);
        }

        $this->redirect('/admin/import/preview/' . $token);
    }

    public function showPreview(string $token): void
    {
        $filePath = BASE_PATH . '/storage/imports/' . basename($token) . '.json';
        if (!is_file($filePath)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Sesi pratinjau import tidak ditemukan atau telah kadaluarsa.'], 404);
            }
            flash('error', 'Sesi pratinjau import telah kadaluarsa atau tidak ditemukan.');
            $this->redirect('/admin/import');
        }

        $data = json_decode(file_get_contents($filePath), true);
        $activePeriod = $this->periodModel->getActive();

        if (is_api_request()) {
            $this->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'import_data' => $data,
                    'period' => $activePeriod,
                ],
            ]);
        }

        $this->view('admin.students.preview', [
            'title' => 'Pratinjau Import Data Siswa',
            'pageTitle' => 'Pratinjau Hasil Import DPT',
            'token' => $token,
            'importData' => $data,
            'period' => $activePeriod,
        ], 'admin');
    }

    public function importConfirm(): void
    {
        $input = is_api_request() && empty($_POST) ? (json_decode(file_get_contents('php://input'), true) ?? []) : $_POST;
        $token = basename((string) ($input['token'] ?? ''));
        $skipDuplicateNis = !empty($input['skip_duplicate_nis']);

        $filePath = BASE_PATH . '/storage/imports/' . $token . '.json';
        if (!is_file($filePath)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Sesi import telah kadaluarsa. Silakan upload ulang file Excel.'], 400);
            }
            flash('error', 'Sesi import telah kadaluarsa. Silakan upload ulang file Excel.');
            $this->redirect('/admin/import');
        }

        $sessionData = json_decode(file_get_contents($filePath), true);
        $periodId = $sessionData['period_id'];
        $rows = $sessionData['rows'] ?? [];

        // Filter only valid rows
        $validRows = array_filter($rows, fn($r) => !empty($r['is_valid']));

        if (empty($validRows)) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Tidak ada data valid yang dapat dimasukkan ke database.'], 400);
            }
            flash('error', 'Tidak ada data valid yang dapat dimasukkan ke database.');
            $this->redirect('/admin/import');
        }

        try {
            $result = $this->studentModel->importBulk($validRows, $periodId, $skipDuplicateNis);
            @unlink($filePath);

            $msg = "Import berhasil: {$result['inserted']} siswa berhasil dimasukkan ke DPT.";
            if ($result['skipped'] > 0) {
                $msg .= " ({$result['skipped']} siswa dilewati karena NISN ganda).";
            }

            if (is_api_request()) {
                $this->json([
                    'success' => true,
                    'data' => $result,
                    'message' => $msg,
                ]);
            }

            flash('success', $msg);
            $this->redirect('/admin/siswa');
        } catch (\Throwable $e) {
            if (is_api_request()) {
                $this->json(['success' => false, 'message' => 'Gagal menyimpan data import: ' . $e->getMessage()], 500);
            }
            flash('error', 'Gagal menyimpan data import: ' . $e->getMessage());
            $this->redirect('/admin/import');
        }
    }

    public function template(): never
    {
        $headers = ['NO', 'NISN', 'NAMA', 'KELAS'];
        $samples = [
            [1, '0133085278', 'ACH. FATIR ARDIANSYAH', '7A'],
            [2, '0138202388', 'ANANDA SEPTIAN', '7A'],
            [3, '0134176889', 'BAYU DIGJAYA SAPUTRA', '8G'],
            [4, '0118591342', 'HILDA ELFIRA DWI SEPTIANI', '9H'],
        ];

        $format = strtolower((string) ($_GET['format'] ?? 'xlsx'));

        if ($format === 'xlsx') {
            $tempFile = tempnam(sys_get_temp_dir(), 'tpl_') . '.xlsx';
            write_xlsx(['DPT Siswa' => array_merge([$headers], $samples)], $tempFile);

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="Template_DPT_OSIS_SMPN2_Balung.xlsx"');
            header('Content-Length: ' . filesize($tempFile));
            readfile($tempFile);
            @unlink($tempFile);
            exit;
        }

        // CSV fallback
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="Template_DPT_OSIS_SMPN2_Balung.csv"');
        $out = fopen('php://output', 'w');
        // UTF-8 BOM so Excel opens with proper encoding
        fwrite($out, "\xEF\xBB\xBF");
        fputcsv($out, $headers);
        foreach ($samples as $s) {
            fputcsv($out, $s);
        }
        fclose($out);
        exit;
    }

    public function export(): never
    {
        $activePeriod = $this->periodModel->getActive();
        $periodId = $_GET['period_id'] ?? ($activePeriod['id'] ?? 0);
        $period = $periodId ? $this->periodModel->find($periodId) : null;

        if (!$period) {
            flash('error', 'Pilih periode untuk export data pemilih.');
            $this->redirect('/admin/siswa');
        }

        $category = strtolower((string)($_GET['category'] ?? '')); // 'siswa', 'guru', or '' (all)
        $classFilter = $_GET['class'] ?? '';
        $search = $_GET['search'] ?? '';
        $hasVoted = $_GET['has_voted'] ?? '';

        $filters = [];
        if ($category) $filters['category'] = $category;
        if ($classFilter) $filters['class'] = $classFilter;
        if ($search) $filters['search'] = $search;
        if ($hasVoted !== '') $filters['has_voted'] = $hasVoted;

        $allRecords = $this->studentModel->exportAll($periodId, $filters);
        $allSiswa = array_values(array_filter($allRecords, fn($r) => $r['class'] !== 'GURU'));
        $allGuru = array_values(array_filter($allRecords, fn($r) => $r['class'] === 'GURU'));

        // Prepare Sheet 1: Semua DPT
        $sheetSemua = [
            ['NO', 'NISN', 'NAMA LENGKAP', 'KELAS / ROMBEL', 'TINGKAT', 'KATEGORI', 'STATUS HAK SUARA', 'WAKTU MEMILIH']
        ];
        foreach ($allRecords as $i => $r) {
            $sheetSemua[] = [
                $i + 1,
                !empty($r['nis']) ? (string)$r['nis'] : '-',
                $r['full_name'],
                $r['class'] === 'GURU' ? 'DEWAN GURU' : $r['class'],
                $r['class'] === 'GURU' ? 'Dewan Guru' : 'Kelas ' . ($r['grade'] ?: substr($r['class'], 0, 1)),
                $r['class'] === 'GURU' ? 'Guru / Staf' : 'Siswa',
                !empty($r['has_voted']) ? 'Sudah Memilih' : 'Belum Memilih',
                !empty($r['voted_at']) ? $r['voted_at'] : '-'
            ];
        }

        // Prepare Sheet 2: Khusus Siswa
        $sheetSiswa = [
            ['NO', 'NISN', 'NAMA SISWA', 'KELAS', 'TINGKAT', 'STATUS HAK SUARA']
        ];
        foreach ($allSiswa as $i => $s) {
            $sheetSiswa[] = [
                $i + 1,
                !empty($s['nis']) ? (string)$s['nis'] : '-',
                $s['full_name'],
                $s['class'],
                'Kelas ' . ($s['grade'] ?: substr($s['class'], 0, 1)),
                !empty($s['has_voted']) ? 'Sudah Memilih' : 'Belum Memilih'
            ];
        }

        // Prepare Sheet 3: Khusus Guru
        $sheetGuru = [
            ['NO', 'NAMA GURU / STAF', 'STATUS / JABATAN', 'STATUS HAK SUARA']
        ];
        foreach ($allGuru as $i => $g) {
            $sheetGuru[] = [
                $i + 1,
                $g['full_name'],
                'Dewan Guru / Tenaga Kependidikan',
                !empty($g['has_voted']) ? 'Sudah Memilih' : 'Belum Memilih'
            ];
        }

        // Prepare Sheet 4: Format Siap Impor (Untuk kemudahan edit lulusan / pensiunan / guru baru)
        $sheetFormatImpor = [
            ['NISN', 'NAMA_LENGKAP', 'KELAS']
        ];
        foreach ($allRecords as $r) {
            $sheetFormatImpor[] = [
                !empty($r['nis']) ? (string)$r['nis'] : '',
                $r['full_name'],
                $r['class']
            ];
        }

        $sheets = [];
        if ($category === 'siswa') {
            $sheets['Data Siswa (' . count($allSiswa) . ')'] = $sheetSiswa;
            $sheets['Format Siap Edit & Re-Impor'] = array_merge(
                [['NISN', 'NAMA_SISWA', 'KELAS']],
                array_map(fn($s) => [(string)$s['nis'], $s['full_name'], $s['class']], $allSiswa)
            );
            $prefix = 'DPT_Siswa';
        } elseif ($category === 'guru') {
            $sheets['Dewan Guru (' . count($allGuru) . ')'] = $sheetGuru;
            $sheets['Format Siap Edit & Re-Impor'] = array_merge(
                [['NISN', 'NAMA_GURU', 'KELAS']],
                array_map(fn($g) => ['', $g['full_name'], 'GURU'], $allGuru)
            );
            $prefix = 'DPT_Dewan_Guru';
        } else {
            $sheets['Semua DPT (' . count($allRecords) . ')'] = $sheetSemua;
            $sheets['Data Siswa (' . count($allSiswa) . ')'] = $sheetSiswa;
            $sheets['Dewan Guru (' . count($allGuru) . ')'] = $sheetGuru;
            $sheets['Format Siap Edit & Re-Impor'] = $sheetFormatImpor;
            $prefix = 'DPT_Lengkap_SMPN2Balung';
        }

        $tempFile = tempnam(sys_get_temp_dir(), 'dpt_') . '.xlsx';
        write_xlsx($sheets, $tempFile);

        $safePeriod = preg_replace('/[^a-zA-Z0-9_-]/', '_', $period['name']);
        $filename = "{$prefix}_{$safePeriod}_" . date('Ymd_His') . ".xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($tempFile));
        header('Cache-Control: max-age=0, no-cache, must-revalidate');
        header('Pragma: public');
        readfile($tempFile);
        @unlink($tempFile);
        exit;
    }
}
