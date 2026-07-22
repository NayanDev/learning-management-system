<?php
    $letterNumb = $event->letter_number ?? '-';
    $workshop = $event->workshop->name ?? '-';
    $divisi = $event->divisi ?? '-';
    $location = ucwords(strtolower($event->location ?? '-'));
    $day = \Carbon\Carbon::parse($event->start_date)->isoFormat('dddd');
    $date = \Carbon\Carbon::parse($event->start_date)->translatedFormat('d F Y') ?? '-';;
    $clock = \Carbon\Carbon::parse($event->start_date)->format('H:i') ?? '-';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Surat Perintah Pelatihan</title>
    <link rel="icon" href=" {{ config('idev.app_favicon', asset('easyadmin/idev/img/favicon.png')) }}">
    <link rel="stylesheet" href="{{ asset('custom/css/report.css') }}">
</head>
<body>
    <div class="letterhead">
            <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
            <h3  style="border: 1px solid black;padding:25px 10px 25px 25px;">SURAT PERINTAH PELATIHAN</h3>
        </div>
        <div class="info-section">
            <table class="no-border" style="width:40%;">
                <tr>
                    <td class="text-start no-border">No.</td>
                    <td width="5%" class="no-border">:</td>
                    <td class="text-start no-border">{{ $letterNumb }}</td>
                </tr>
                <tr>
                    <td class="text-start no-border">Perihal</td>
                    <td width="5%" class="no-border">:</td>
                    <td class="text-start no-border">Perintah Pelatihan</td>
                </tr>
            </table>
        </div>
    <p>Berdasarkan Usulan Divisi "{{ $divisi }}", maka kami menugaskan nama tersebut dibawah ini:</p>

        <table style="width:100%; border: 1px solid black;">
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th>Nama</th>
                    <th  width="25%">Divisi / Bagian</th>
                    <th width="25%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participants as $participant)
                    <tr>
                        <td class="border-lr">{{ $loop->iteration }}</td>
                        <td class="border-lr text-start">{{ $participant->name }}</td>
                        <td class="border-lr">{{ $participant->divisi }}</td>

                        @if($loop->first)
                            <td rowspan="{{ $participants->count() }}" class="text-start" valign="top">
                                Mohon agar dilaksanakan sesuai dengan jadwal yang telah ditetapkan.
                            </td>
                        @endif
                    </tr>
                @endforeach             
            </tbody>
        </table>
        <p>
            Untuk mengikuti "{{ $workshop }}" pada hari {{ $day }}, {{ $date }}, Pukul {{ $clock }} WIB - Selesai, bertempat di {{ $location }}. <br><br>
            Demikian Surat Perintah Pelatihan ini dibuat agar dilaksanakan dengan penuh tanggung jawab. Atas perhatiannya kami ucapkan terima kasih.
        </p>
    <br>
    <table class="no-border" style="width:100%;">
        <tr>
            <td class="no-border text-center"style="width:25%;position: relative;">
                Semarang, {{ $event->created_date ? \Carbon\Carbon::parse($event->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                <br>
                Disetujui,
                <br><br>
                @if($event->approve_by !== null)
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML(
                            route('signature.verified', [
                                'model' => class_basename($event),
                                'id'    => $event->id,
                                'user'  => $event->approver->id ?? 'not-available',
                            ]),
                            'QRCODE',
                            2,
                            2
                        ) !!}
                    </div>
                </div>
                @endif
                <br>
                <u><strong>{{ $event->approver->name ?? 'SARJONO' }}</strong></u>
                <br>
                <span>Manager Umum & SDM</span>
            </td>
            <td class="no-border" style="width:25%;"></td>
            <td class="no-border" style="width:25%;"></td>
            <td class="no-border text-center"style="width:25%;">
            </td>
        </tr>
    </table>
    <p style="text-align: right">F.DUP.05.R.00.T.090217</p>
</body>
</html>