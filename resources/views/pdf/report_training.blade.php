<?php
    $letterNumb = $event->letter_number ?? '-';
    $workshop = $event->workshop->name ?? '-';
    $divisi = ucwords(strtolower($event->divisi ?? '-'));
    $location = ucwords(strtolower($event->location ?? '-'));
    $day = \Carbon\Carbon::parse($event->start_date)->isoFormat('dddd');
    $date = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y') ?? '-';;
    $clock = \Carbon\Carbon::parse($event->start_date)->format('H:i') ?? '-';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pelatihan</title>
    <link rel="icon" href=" {{ config('idev.app_favicon', asset('easyadmin/idev/img/favicon.png')) }}">
    <link rel="stylesheet" href="{{ asset('custom/css/report.css') }}">
</head>
<body>
    <div class="letterhead">
            <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
            <h3  style="border: 1px solid black;padding:25px 10px 25px 25px;">LAPORAN PELATIHAN {{ strtoupper( $report->category_training ) }}</h3>
        </div>
        <div class="info-section">
            <table class="no-border" style="width:100%;">
                <tr>
                    <td class="text-start no-border" width="10%">Perihal</td>
                    <td width="3%" class="no-border">:</td>
                    <td class="text-start no-border">Laporan Pelatihan {{ ucfirst($report->category_training) }}</td>
                </tr>
                <tr>
                    <td class="text-start no-border"></td>
                    <td width="3%" class="no-border"></td>
                    <td class="text-start no-border"></td>
                </tr>
                <tr>
                    <td class="text-start no-border" colspan="3">
                        Kepada Yth. <br>
                        Bapak Pimpinan / Direksi <br>
                        <b>
                            PT. SAMPHARINDO PERDANA <br>
                            Semarang - Jawa Tengah
                        </b>
                    </td>
                </tr>
                <tr>
                    <td class="no-border"></td>
                    <td class="no-border"></td>
                    <td class="no-border"></td>
                </tr>
                <tr>
                    <td class="text-start no-border" colspan="3">
                        Dengan hormat, <br>
                        Yang bertanda tangan dibawah ini: <br>
                        Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; :  <b>SARJONO</b> <br>
                        <del>Divisi</del> / Bagian &nbsp; : UMUM & SDM <br>
                    </td>
                </tr>
                <tr>
                    <td class="no-border"></td>
                    <td class="no-border"></td>
                    <td class="no-border"></td>
                </tr>
                <tr>
                    <td class="text-start no-border" colspan="3">
                        {{ $report->description }}
                    </td>
                </tr>
            </table>

            <table class="no-border" style="width:100%;">
                <thead>
                    <tr>
                        <td width="3%"><b>NO.</b></td>
                        <td><b>MATERI</b></td>
                        <td><b>SASARAN</b></td>
                        <td><b>KETERANGAN</b></td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-start">1.</td>
                        <td class="text-start">{{ $report->materi ?? '-' }}</td>
                        <td class="text-start">{{ $report->targets ?? '-' }}</td>
                        <td class="text-start">{{ $report->notes ?? '-' }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="no-border" style="width:100%;">
                <tr>
                    <td class="text-start no-border" colspan="3">
                        Demikian laporan pelatihan {{ ucfirst($report->category_training) }} ini kami buat dengan sebenar-benarnya untuk dapat digunakan sebagaimana mestinya. Atas perhatian dan kerjasama Bapak Pimpinan / Direksi, kami ucapkan terima kasih.
                    </td>
                </tr>
                <tr>
                    <td class="text-start no-border" colspan="3">
                        <table width="100%">
                            <tr>
                                <td class="no-border" width="25%" style="position: relative;">
                                    Semarang, {{ \Carbon\Carbon::parse($report->report_date)->translatedFormat('d F Y') }} <br>
                                    Dilaporkan oleh, <br><br>
                                    @if($report->status === 'approve' || $report->status === 'close')
                                        <div style="display: flex; justify-content: center;">
                                            <div style="display: inline-block;">
                                                {!! DNS2D::getBarcodeHTML(
                                                    route('signature.verified', [
                                                        'model' => class_basename($report),
                                                        'id'    => $report->id,
                                                        'user'  => $report->managerUser->id ?? 'not-available',
                                                    ]),
                                                    'QRCODE',
                                                    2,
                                                    2
                                                ) !!}
                                            </div>
                                        </div>
                                    @endif
                                    <br>
                                    <b><u>{{ $report->managerUser->name }}</u></b> <br>
                                    Manager Umum & SDM
                                </td>
                                <td class="no-border" width="25%"></td>
                                <td class="no-border" width="25%"></td>
                                <td class="no-border" width="25%" style="position: relative;">
                                    <br>
                                    Diketahui oleh, <br><br>
                                    @if($report->status === 'close')
                                        <div style="display: flex; justify-content: center;">
                                            <div style="display: inline-block;">
                                                {!! DNS2D::getBarcodeHTML(
                                                    route('director.signature.verified', [
                                                        'model' => class_basename($report),
                                                        'id'    => $report->id
                                                    ]),
                                                    'QRCODE',
                                                    2,
                                                    2
                                                ) !!}
                                            </div>
                                        </div>
                                    @endif
                                    <br>
                                    <b><u>{{ $report->director_name }}</u></b> <br>
                                    Direktur Umum & SDM
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>

    <p style="text-align: right"><b>F.DUP.09.R.00.T.090217</b></p>
</body>
</html>