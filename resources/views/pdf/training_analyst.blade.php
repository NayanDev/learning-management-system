@php
function addLineBreaks($text) 
{
    $words = explode(' ', $text);
    $result = [];
    
    foreach ($words as $word) {
        if (strlen($word) > 9) {
            $result[] = $word . '<br>';
        } else {
            $result[] = $word;
        }
    }
    
    return implode(' ', $result);
}

$qualification = json_decode($training_analyst->qualification, true);
$general = json_decode($training_analyst->general, true);
$technic = json_decode($training_analyst->technic, true);
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Training Need Analyst</title>
    <link rel="icon" href=" {{ config('idev.app_favicon', asset('easyadmin/idev/img/favicon.png')) }}">
    <style>
        body {
            font-size: 10px;
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }

        .checklist {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
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
            padding-top: 5px;
            padding-bottom: 5px;
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
            margin-bottom: 15px;
            font-size: 12px;
        }
        
        table {
            border-collapse: collapse;
            width: 100%;
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
            margin: 0;
            padding: 0;
            text-align: center;
            vertical-align: middle;
            height: 120px;
            width: 30px;
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

                /* Style untuk elemen yang hanya akan tampil di layar */
        .hanya-tampil-di-layar {
        padding: 10px;
        background-color: #A6BE47;
        border: 1px solid #ccd;
        margin-bottom: 20px;
        }

        /* Aturan untuk menyembunyikan elemen saat dicetak/dibuat PDF */
        @media print {
        .hanya-tampil-di-layar {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
        <h3>ANALISA KEBUTUHAN LATIHAN</h3>
        <p><em>Training Needs Analysis</em></p>
    </div>
    <div class="info-section">
        <span style="float:left;">Divisi / Bagian / Unit Kerja :  {{ $training_analyst->user->divisi }}</span>
        <span style="float:right;">Periode {{ $training_analyst->training->year }}</span>
    </div>
    <br>
    <table>
        <thead>
            <tr>
                <th rowspan="3" style="width: 15px">No</th>
                <th rowspan="3">Jabatan</th>
                <th rowspan="3" style="width: 30px;">Jumlah Personil</th>
                <th colspan="<?= count($qualification) + count($general) + count($technic) ?>">Jenis Pelatihan</th>
            </tr>
            <tr>
                <th colspan="<?= count($qualification) ?>" class="text-center">Qualification</th>
                <th colspan="<?= count($general) ?>" class="text-center">Pelatihan Umum</th>
                <th colspan="<?= count($technic) ?>" class="text-center">Pelatihan Khusus & Tambahan</th>
            </tr>
            <tr>
               
                    @foreach($qualification as $key )
                        <th class="th"><span class="rotate-text"><?= addLineBreaks($key); ?></span></th>
                    @endforeach
                    @foreach($general as $key )
                        <th class="th"><span class="rotate-text"><?= addLineBreaks($key); ?></span></th>
                    @endforeach
                    @foreach($technic as $key )
                        <th class="th"><span class="rotate-text"><?= addLineBreaks($key); ?></span></th>
                    @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($training_analyst_data as $item)
                @php
                    $qualification = json_decode($item->qualification, true);
                    $general = json_decode($item->general, true);
                    $technical = json_decode($item->technic, true);
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="text-start">{{ $item->position }}</td>
                    <td>{{ $item->personil }}</td>

                    {{-- Qualifications --}}
                    @foreach($qualification as $qual)
                        <td class="text-center checklist">{!! $qual === "true" ? '&#10003;' : '' !!}</td>
                    @endforeach
                                                                        
                    {{-- Qualifications --}}
                    @foreach($general as $gen)
                        <td class="text-center checklist">{!! $gen === "true" ? '&#10003;' : '' !!}</td>
                    @endforeach

                    {{-- Qualifications --}}
                    @foreach($technical as $tech)
                        <td class="text-center checklist">{!! $tech === "true" ? '&#10003;' : '' !!}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <p><i>Catatan: berilah tanda <span class="checklist">"&#10003;"</span> pada kolom yang sesuai</i></p>
    <br>

    <table class="no-border" style="width:100%;">
        <tr>
            <td class="no-border text-center"style="width:20%;">
                Dibuat Oleh,<br><br>
                @if($created->status === 'approve')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( $created->user->name . "\n" . 'Staff ' . $created->user->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                    </div>
                </div>
                <br>
                <strong><u>{{ $created->user->name ?? '-' }}</u></strong>
                <br>
                <span>Staff {{ ucwords(strtolower($created->user->divisi)) }}</span>
                @elseif($created->status === 'submit')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( $created->user->name . "\n" . 'Staff ' . $created->user->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                    </div>
                </div>
                <br>
                <strong><u>{{ $created->user->name ?? '-' }}</u></strong>
                <br>
                <span>Staff {{ ucwords(strtolower($created->user->divisi)) }}</span>
                @else
                <div style="height: 50px"></div>
                <strong><u>{{ $created->user->name ?? '-' }}</u></strong>
                <br>
                <span>Staff {{ ucwords(strtolower($created->user->divisi)) }}</span>
                @endif
            </td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border" style="width:20%;"></td>
            <td class="no-border text-center"style="width:20%;">
                Disetujui Oleh,<br><br>
                @if($created->status === 'approve')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( $created->approver->name . "\n" . 'Manager ' . $created->approver->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                    </div>
                </div>
                <br>
                <strong><u>{{ $created->approver->name ?? '-' }}</u></strong>
                <br>
                <span>Manager {{ ucwords(strtolower($created->approver->divisi)) }}</span>
                @else
                <div style="height: 50px"></div>
                <em>Data belum tersedia</em>
                @endif
            </td>
        </tr>
    </table>
    <p style="text-align: right">F.DUP.34.R.00.T.01.07.10</p>
</body>
</html>