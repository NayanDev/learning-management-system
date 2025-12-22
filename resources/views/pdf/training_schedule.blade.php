<?php
// Calculate Rowspan
$totalRows = 0;
foreach ($trainings as $training) {
    if (isset($training['training']['workshop']) && count($training['training']['workshop']) > 0) {
        $totalRows = max($totalRows, count($training['training']['workshop']));
    }
}
// Set minimum rows if no data
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
        body {
            font-size: 6px;
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }

        .text-start {
            text-align: left;
        }

        .letterhead {
            position: relative;
            margin-bottom: 10px;
            overflow: visible;
            padding-bottom: 10px;
            /* border: 2px solid black; */
        }

        .letterhead img {
            position: absolute;
            width: 40px;
            padding-top: 13px;
            padding-left: 10px;
            /* border: 1px solid green; */
        }

        .letterhead h3 {
            /* margin-top: 20px; */
            margin-bottom: 0;
            text-align: right;
            /* padding: 10px; */
            /* border: 1px solid red; */
            padding: 0px;
        }
        
        .letterhead p {
            text-align: right;
            margin-top: 0;
            margin-bottom: 20px;
            padding: 0px;
            /* border:1px solid blue; */
        }

        .info-section {
            font-size: 8px;
            font-weight: bold
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
            border-collapse: collapse;
            page-break-inside: auto;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
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
        position: relative; /* Tambahkan ini */
    }

    .rotate-text {
        position: absolute;    /* Posisi absolute agar bisa full */
        top: 50%;             /* Posisi vertical center */
        left: 50%;           /* Posisi horizontal center */
        transform: translate(-50%, -50%) rotate(-90deg); /* Gabung translate dan rotate */
        width: 80px;        /* Sesuaikan dengan height th */
        display: flex;       /* Gunakan flex untuk centering content */
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .highlight {
        background-color: gray; /* Warna kuning */
    }

    </style>
</head>
<body>
    <div class="letterhead">
            <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
            <h3  style="border: 1px solid black;padding:25px 10px 25px 25px;">JADWAL PELATIHAN KARYAWAN</h3>
        </div>
        <div class="info-section">
            <span>Tahun : {{ $year ?? date('Y') }}</span>
        </div>
    <br>

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
                                echo "<th>" . $j . "</th>"; // Output angka 1 hingga 4
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
                    
                    // If no workshops, show division with empty row
                    if ($rowspan == 0) {
                        $rowspan = 1;
                        $workshops = ['' => ['personil' => '', 'schedule' => [
                            'jan' => [], 'feb' => [], 'mar' => [], 'apr' => [],
                            'may' => [], 'jun' => [], 'jul' => [], 'aug' => [],
                            'sep' => [], 'oct' => [], 'nov' => [], 'dec' => []
                        ]]];
                    }
                @endphp

                @foreach($workshops as $judul => $detail)
                    <tr>
                        @if($loop->first)
                            <td rowspan="{{ $rowspan }}">{{ $loop->parent->iteration }}</td>
                            <td rowspan="{{ $rowspan }}">{{ $training['divisi'] }}</td>
                        @endif
                        <td class="text-start">{{ $judul }}</td>
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
            <td class="no-border text-center"style="width:20%;">
                Semarang, {{ $created->created_date ? \Carbon\Carbon::parse($created->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                <br>
                Dibuat Oleh,
                <br><br>
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( 'SARJONO' . "\n" . 'Manager ' . 'Umum & SDM' . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
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
            <td class="no-border text-center"style="width:20%;">
                Mengetahui,
                <br><br>
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML("MAKMURI YUSIN\nDirektur Umum & SDM\n(ini adalah dokumen resmi dan sah)", 'QRCODE', 1, 1) !!}
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
</body>
</html>