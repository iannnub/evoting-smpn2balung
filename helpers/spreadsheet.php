<?php
/**
 * Pembaca & penulis spreadsheet tanpa library eksternal.
 * - XLSX dibaca dengan ZipArchive + SimpleXML (tersedia di PHP standar / InfinityFree).
 * - CSV mendukung pemisah koma, titik koma (Excel lokal Indonesia), dan tab.
 */

/**
 * Baca file spreadsheet menjadi array baris (setiap baris = array string, index kolom mulai 0).
 *
 * @throws RuntimeException jika file tidak bisa dibaca
 */
function read_spreadsheet(string $path, string $extension): array
{
    return match (strtolower($extension)) {
        'xlsx' => read_xlsx($path),
        'csv', 'txt' => read_csv($path),
        default => throw new RuntimeException('Format file tidak didukung. Gunakan .xlsx atau .csv'),
    };
}

function read_xlsx(string $path): array
{
    if (!class_exists('ZipArchive')) {
        throw new RuntimeException('Ekstensi PHP zip tidak aktif, gunakan file .csv');
    }
    $zip = new ZipArchive();
    if ($zip->open($path) !== true) {
        throw new RuntimeException('File .xlsx rusak atau bukan file Excel yang valid');
    }

    try {
        // Shared strings
        $strings = [];
        $ssXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($ssXml !== false) {
            $ss = simplexml_load_string($ssXml, 'SimpleXMLElement', LIBXML_NONET);
            if ($ss !== false) {
                foreach ($ss->si as $si) {
                    if (isset($si->t)) {
                        $strings[] = (string) $si->t;
                    } else {
                        $text = '';
                        foreach ($si->r as $run) {
                            $text .= (string) $run->t;
                        }
                        $strings[] = $text;
                    }
                }
            }
        }

        $sheetPath = xlsx_first_sheet_path($zip);
        $sheetXml = $zip->getFromName($sheetPath);
        if ($sheetXml === false) {
            throw new RuntimeException('Sheet pertama tidak ditemukan di file Excel');
        }
        $sheet = simplexml_load_string($sheetXml, 'SimpleXMLElement', LIBXML_NONET | LIBXML_COMPACT);
        if ($sheet === false || !isset($sheet->sheetData)) {
            throw new RuntimeException('Isi sheet tidak dapat dibaca');
        }

        $rows = [];
        foreach ($sheet->sheetData->row as $row) {
            $rowIndex = (int) $row['r'];
            $cells = [];
            $auto = 0;
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = $ref !== '' ? xlsx_column_index(preg_replace('/\d+/', '', $ref)) : $auto;
                $auto = $col + 1;
                $type = (string) $c['t'];
                $value = (string) $c->v;
                if ($type === 's') {
                    $value = $strings[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = (string) $c->is->t;
                } elseif ($type === '' || $type === 'n') {
                    $value = xlsx_number_to_string($value);
                }
                $cells[$col] = $value;
            }
            if ($cells) {
                $max = max(array_keys($cells));
                $line = [];
                for ($i = 0; $i <= $max; $i++) {
                    $line[$i] = trim((string) ($cells[$i] ?? ''));
                }
                $rows[$rowIndex > 0 ? $rowIndex : count($rows) + 1] = $line;
            }
        }
        return $rows;
    } finally {
        $zip->close();
    }
}

/** Ambil path sheet pertama berdasarkan workbook.xml + relasinya. */
function xlsx_first_sheet_path(ZipArchive $zip): string
{
    $fallback = 'xl/worksheets/sheet1.xml';
    $wb = $zip->getFromName('xl/workbook.xml');
    $rels = $zip->getFromName('xl/_rels/workbook.xml.rels');
    if ($wb === false || $rels === false) {
        return $fallback;
    }
    $wbXml = simplexml_load_string($wb, 'SimpleXMLElement', LIBXML_NONET);
    $relXml = simplexml_load_string($rels, 'SimpleXMLElement', LIBXML_NONET);
    if (!$wbXml || !$relXml || !isset($wbXml->sheets->sheet[0])) {
        return $fallback;
    }
    $attrs = $wbXml->sheets->sheet[0]->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships');
    $rid = (string) ($attrs['id'] ?? '');
    foreach ($relXml->Relationship as $rel) {
        if ((string) $rel['Id'] === $rid) {
            $target = ltrim((string) $rel['Target'], '/');
            return str_starts_with($target, 'xl/') ? $target : 'xl/' . $target;
        }
    }
    return $fallback;
}

function xlsx_column_index(string $letters): int
{
    $index = 0;
    foreach (str_split(strtoupper($letters)) as $ch) {
        $index = $index * 26 + (ord($ch) - 64);
    }
    return max(0, $index - 1);
}

function xlsx_column_letter(int $index): string
{
    $letters = '';
    $index++;
    while ($index > 0) {
        $mod = ($index - 1) % 26;
        $letters = chr(65 + $mod) . $letters;
        $index = intdiv($index - $mod, 26);
    }
    return $letters;
}

/** Angka dari Excel bisa berbentuk "3.133085278E9" atau "12.0"; ubah ke string bulat bila memungkinkan. */
function xlsx_number_to_string(string $value): string
{
    if ($value === '' || !is_numeric($value)) {
        return $value;
    }
    $float = (float) $value;
    if (floor($float) == $float && abs($float) < 1e15) {
        return sprintf('%.0f', $float);
    }
    return $value;
}

function read_csv(string $path): array
{
    $content = file_get_contents($path);
    if ($content === false) {
        throw new RuntimeException('File CSV tidak dapat dibaca');
    }
    // Hapus BOM, konversi encoding Windows-1252 jika bukan UTF-8
    $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
    if (!mb_check_encoding($content, 'UTF-8')) {
        $content = mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
    }
    $lines = preg_split('/\R/', $content);
    // Baris "sep=;" dari Excel
    if (isset($lines[0]) && preg_match('/^sep=(.)$/i', trim($lines[0]), $m)) {
        $delimiter = $m[1];
        array_shift($lines);
    } else {
        $sample = implode("\n", array_slice($lines, 0, 5));
        $counts = [',' => substr_count($sample, ','), ';' => substr_count($sample, ';'), "\t" => substr_count($sample, "\t")];
        arsort($counts);
        $delimiter = (string) array_key_first($counts);
    }

    $rows = [];
    $i = 0;
    foreach ($lines as $line) {
        $i++;
        if (trim($line) === '') {
            continue;
        }
        $rows[$i] = array_map('trim', str_getcsv($line, $delimiter, '"', '\\'));
    }
    return $rows;
}

/**
 * Tulis file XLSX sederhana (multi-sheet). Nilai string disimpan sebagai teks
 * sehingga NISN dengan nol di depan tidak hilang.
 *
 * @param array<string, array<int, array<int, scalar|null>>> $sheets ['Nama Sheet' => [[...], [...]]]
 */
function write_xlsx(array $sheets, string $path): void
{
    $zip = new ZipArchive();
    if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new RuntimeException('Gagal membuat file Excel');
    }
    $esc = fn ($v) => htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8');

    $sheetNames = array_keys($sheets);
    $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
        . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
        . '<Default Extension="xml" ContentType="application/xml"/>'
        . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
        . '<Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
    $wbSheets = '';
    $wbRels = '';
    foreach ($sheetNames as $i => $name) {
        $n = $i + 1;
        $contentTypes .= '<Override PartName="/xl/worksheets/sheet' . $n . '.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>';
        $safeName = mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]/', ' ', $name), 0, 31);
        $wbSheets .= '<sheet name="' . $esc($safeName) . '" sheetId="' . $n . '" r:id="rId' . $n . '"/>';
        $wbRels .= '<Relationship Id="rId' . $n . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet' . $n . '.xml"/>';
    }
    $stylesId = count($sheetNames) + 1;
    $wbRels .= '<Relationship Id="rId' . $stylesId . '" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>';
    $contentTypes .= '</Types>';

    $zip->addFromString('[Content_Types].xml', $contentTypes);
    $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">'
        . '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>'
        . '</Relationships>');
    $zip->addFromString('xl/workbook.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">'
        . '<sheets>' . $wbSheets . '</sheets></workbook>');
    $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' . $wbRels . '</Relationships>');
    // Style 0 = normal, style 1 = bold (header)
    $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
        . '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'
        . '<fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts>'
        . '<fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills>'
        . '<borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders>'
        . '<cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs>'
        . '<cellXfs count="2"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="0" borderId="0" xfId="0" applyFont="1"/></cellXfs>'
        . '</styleSheet>');

    foreach (array_values($sheets) as $i => $rows) {
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'
            . '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        $r = 0;
        foreach ($rows as $row) {
            $r++;
            $xml .= '<row r="' . $r . '">';
            foreach (array_values($row) as $c => $value) {
                $ref = xlsx_column_letter($c) . $r;
                $style = $r === 1 ? ' s="1"' : '';
                if (is_int($value) || is_float($value)) {
                    $xml .= '<c r="' . $ref . '"' . $style . '><v>' . $value . '</v></c>';
                } elseif ($value !== null && $value !== '') {
                    $xml .= '<c r="' . $ref . '"' . $style . ' t="inlineStr"><is><t xml:space="preserve">' . $esc($value) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
        $xml .= '</sheetData></worksheet>';
        $zip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', $xml);
    }
    $zip->close();
}
