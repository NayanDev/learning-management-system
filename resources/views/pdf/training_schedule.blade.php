<?php
// Calculate Rowspan
$totalRows = 0;
foreach ($trainings as $training) {
    if (isset($training['training']['workshop']) && count($training['training']['workshop']) > 0) {
        $totalRows = max($totalRows, count($training['training']['workshop']));
    }
}
if ($totalRows == 0) {
    $totalRows = 1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Training Schedule</title>
    <link rel="icon" href="{{ asset('easyadmin/idev/img/favicon.png') }}" type="image/png">
    <style>
        @page {
            size: A4 landscape;
            margin: 8px 10px;
        }

        body {
            font-size: 6px;
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }

        .text-start {
            text-align: left;
        }

        .no-print {
            display: block;
        }

        .letterhead {
            position: relative;
            margin-bottom: 10px;
            overflow: visible;
            padding-bottom: 10px;
        }

        .letterhead img {
            position: absolute;
            width: 40px;
            padding-top: 5px;
            padding-left: 10px;
        }

        .letterhead h3 {
            margin-bottom: 0;
            text-align: right;
            padding: 0;
        }

        .letterhead p {
            text-align: right;
            margin-top: 0;
            margin-bottom: 20px;
            padding: 0;
        }

        .info-section {
            font-size: 8px;
            font-weight: bold;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            page-break-inside: auto;
        }

        thead {
            display: table-header-group;
        }

        tbody {
            display: table-row-group;
        }

        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }

        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }

        .no-border {
            border: none !important;
        }

        .th {
            border: 1px solid black;
            font-size: 6px;
            margin: 0;
            padding: 0;
            text-align: center;
            vertical-align: middle;
            height: 80px;
            width: 25px;
            position: relative;
        }

        .rotate-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-90deg);
            width: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .highlight {
            background-color: #9e9e9e;
        }

        @media print {
            body,
            table,
            th,
            td,
            .highlight {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .highlight {
                background-color: #9e9e9e !important;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
        <h3 style="border: 1px solid black;padding:25px 10px 25px 25px;">JADWAL PELATIHAN KARYAWAN</h3>
    </div>

    <div class="info-section">
        <span>Tahun : {{ $year ?? date('Y') }}</span>
    </div>

    <div class="no-print" style="text-align:right; margin: 6px 0 8px;">
        <button type="button" onclick="window.print()" style="padding:6px 10px; border:1px solid #000; background:#fff; cursor:pointer;">
            Cetak / Simpan PDF
        </button>
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">No</th>
                <th rowspan="2">Divisi /<br>Bagian / Unit</th>
                <th rowspan="2">Judul Pelatihan</th>
                <th rowspan="2">Peserta</th>
                <th colspan="4">Jan</th>
                <th colspan="4">Feb</th>
                <th colspan="4">Mar</th>
                <th colspan="4">Apr</th>
                <th colspan="4">May</th>
                <th colspan="4">Jun</th>
                <th colspan="4">Jul</th>
                <th colspan="4">Aug</th>
                <th colspan="4">Sept</th>
                <th colspan="4">Oct</th>
                <th colspan="4">Nov</th>
                <th colspan="4">Dec</th>
            </tr>
            <tr>
                <?php
                    for ($i = 0; $i < 12; $i++) {
                        for ($j = 1; $j <= 4; $j++) {
                            echo "<th>" . $j . "</th>";
                        }
                    }
                ?>
            </tr>
        </thead>
        <tbody>
        @if(empty($trainings))
            <tr>
                <td colspan="52" style="text-align: center; padding: 20px;">No data available</td>
            </tr>
        @else
            @foreach($trainings as $training)
                @php
                    $workshops = $training['training']['workshop'] ?? [];
                    $rowspan = count($workshops);

                    if ($rowspan == 0) {
                        $rowspan = 1;
                        $workshops = [[
                            'judul' => '',
                            'personil' => '',
                            'schedule' => [
                                'jan' => [], 'feb' => [], 'mar' => [], 'apr' => [],
                                'may' => [], 'jun' => [], 'jul' => [], 'aug' => [],
                                'sep' => [], 'oct' => [], 'nov' => [], 'dec' => []
                            ]
                        ]];
                    }
                @endphp

                @foreach($workshops as $key => $detail)
                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ $rowspan }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $training['divisi'] }}</td>
                        @endif
                        @php $displayTitle = $detail['judul'] ?? $key; @endphp
                        <td class="text-start">{{ $displayTitle }}</td>
                        <td>{{ $detail['personil'] ?? '' }}</td>
                        @foreach(['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'] as $month)
                            @for($week = 1; $week <= 4; $week++)
                                <td class="{{ in_array($week, $detail['schedule'][$month] ?? []) ? 'highlight' : '' }}"></td>
                            @endfor
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        @endif
        </tbody>
    </table>

    <br><br>
    <table class="no-border" style="width:100%;">
        <tr>
            <td class="no-border text-center" style="width:20%;">
                Semarang, {{ $created->created_date ? \Carbon\Carbon::parse($created->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                <br>
                Dibuat Oleh,
                <br><br>
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML(
                            route('signature.verified', [
                                'model' => class_basename($created),
                                'id'    => $created->id,
                                'user'  => $created->approver->id ?? 'not-available',
                            ]),
                            'QRCODE',
                            2,
                            2
                        ) !!}
                    </div>
                </div>
                <br>
                <u><strong>SARJONO</strong></u>
                <br>
                <span>Manager Umum & SDM</span>
            </td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border text-center" style="width:20%;">
                Mengetahui,
                <br><br>
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML(
                            route('director.signature.verified', [
                                'model' => class_basename($created),
                                'id'    => $created->id
                            ]),
                            'QRCODE',
                            2,
                            2
                        ) !!}
                    </div>
                </div>
                <br>
                <u><strong>MAKMURI YUSIN</strong></u>
                <br>
                <span>Direktur Umum & SDM</span>
            </td>
        </tr>
    </table>
    <p style="text-align: right">F.DUP.10.R.00.T.01.07.17</p>
    <script>
        // Open the browser print dialog so the user can save as PDF without DomPDF.
        window.onload = function () {
            window.print();
        };
    </script>
</body>
</html>