<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Analisis Kesehatan - HEALTIVA</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f3f4f6;
            margin: 0;
            padding: 18px;
            color: #1f2937;
        }
        .sheet {
            max-width: 980px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #d1d5db;
            padding: 20px 24px 28px;
        }
        .top-title {
            text-align: center;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #111827;
        }
        .head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo {
            height: 34px;
            width: auto;
        }
        .brand {
            color: #c0262d;
            font-weight: 800;
            font-size: 24px;
            letter-spacing: 0.4px;
            line-height: 1;
        }
        .subtitle {
            font-size: 12px;
            color: #4b5563;
            margin-top: 2px;
        }
        .head-right {
            text-align: right;
            font-size: 12px;
            color: #374151;
            line-height: 1.5;
        }
        .line-red {
            border-top: 3px solid #b91c1c;
            margin: 10px 0 14px;
        }
        .identity {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 14px;
        }
        .identity td {
            padding: 6px 4px;
            vertical-align: top;
        }
        .id-label {
            width: 22%;
            color: #374151;
            font-weight: 700;
        }
        .id-sep {
            width: 2%;
            color: #6b7280;
        }
        .id-val {
            width: 26%;
            color: #111827;
            font-weight: 600;
        }
        .divider {
            border-top: 1px solid #d1d5db;
            margin-bottom: 14px;
        }
        .report {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #111827;
            font-size: 13px;
            line-height: 1.6;
        }
        .report td, .report th {
            border: 1px solid #111827;
            padding: 10px 12px;
            vertical-align: top;
        }
        .sec {
            background: #f8fafc;
            font-weight: 800;
            color: #111827;
        }
        .foot {
            margin-top: 18px;
            border-top: 1px dashed #d1d5db;
            padding-top: 10px;
            text-align: center;
            font-size: 11px;
            color: #4b5563;
        }
        .actions {
            margin-top: 14px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .btn {
            border: 0;
            border-radius: 8px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-print {
            background: #b91c1c;
            color: #fff;
        }
        .btn-close {
            background: #e5e7eb;
            color: #374151;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .sheet {
                border: 0;
                max-width: 100%;
                padding: 0;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
@php
    $gender = match($patient->gender ?? null) {
        'male' => 'Laki-laki',
        'female' => 'Perempuan',
        default => '-',
    };

    $age = $patient->age ? $patient->age . ' tahun' : '-';
    $printedAt = optional($analysis->created_at)->format('j M Y H:i') ?? '-';

    $raw = (string) ($analysis->result ?? '-');
    $normalized = str_replace(['BMI (', '**BMI (', 'BMI:'], ['IMT (', '**IMT (', 'IMT:'], $raw);

    $lines = preg_split('/\r\n|\r|\n/', $normalized);

    $rows = [];
    $currentSection = null;
    $currentText = [];

    $flush = function () use (&$rows, &$currentSection, &$currentText) {
        if ($currentSection !== null) {
            $rows[] = [
                'section' => $currentSection,
                'content' => trim(implode("\n", $currentText)),
            ];
        }
        $currentSection = null;
        $currentText = [];
    };

    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            $currentText[] = '';
            continue;
        }

        $clean = preg_replace('/^\*+|\*+$/', '', $line);

        if (preg_match('/^(\d+\.)\s*(Ringkasan|Analisis|Rekomendasi|Kesimpulan)/i', $clean)) {
            $flush();
            $currentSection = $clean;
            continue;
        }

        if ($currentSection === null) {
            $currentSection = 'Ringkasan';
        }
        $currentText[] = $clean;
    }

    $flush();
@endphp

    <div class="sheet">
        <div class="top-title">Laporan Analisis Kesehatan - HEALTIVA</div>

        <div class="head">
            <div>
                <div class="logo-wrap">
                    <img src="/images/logo.png" alt="HEALTIVA" class="logo">
                    <div>
                        <div class="brand">HEALTIVA</div>
                        <div class="subtitle">HEALTH & TECHNOLOGY</div>
                    </div>
                </div>
            </div>
            <div class="head-right">
                <div><strong>Laporan Analisis Kesehatan</strong></div>
                <div>{{ $printedAt }}</div>
            </div>
        </div>

        <div class="line-red"></div>

        <table class="identity">
            <tr>
                <td class="id-label">Nama</td><td class="id-sep">:</td><td class="id-val">{{ $patient->name ?? '-' }}</td>
                <td class="id-label">Usia</td><td class="id-sep">:</td><td class="id-val">{{ $age }}</td>
            </tr>
            <tr>
                <td class="id-label">Tanggal Cetak</td><td class="id-sep">:</td><td class="id-val">{{ $printedAt }}</td>
                <td class="id-label">Jenis Kelamin</td><td class="id-sep">:</td><td class="id-val">{{ $gender }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="report">
            @foreach($rows as $row)
                <tr>
                    <td class="sec" colspan="2">{{ $row['section'] }}</td>
                </tr>
                <tr>
                    <td colspan="2">{!! nl2br(e($row['content'] !== '' ? $row['content'] : '-')) !!}</td>
                </tr>
            @endforeach
        </table>

        <div class="foot">
            Laporan ini dihasilkan oleh HEALTIVA AI. Bersifat informatif dan tidak menggantikan diagnosa medis resmi dari dokter spesialis.
        </div>

        <div class="actions">
            <button type="button" class="btn btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
            <button type="button" class="btn btn-close" onclick="window.close()">Tutup</button>
        </div>
    </div>
</body>
</html>
