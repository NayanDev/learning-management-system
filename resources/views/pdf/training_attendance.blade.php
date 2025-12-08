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
    <title>Daftar Hadir Pelatihan</title>
    <link rel="icon" href=" {{ config('idev.app_favicon', asset('easyadmin/idev/img/favicon.png')) }}">
    <link rel="stylesheet" href="{{ asset('custom/css/report.css') }}">
</head>
<body>
    <div class="letterhead">
            <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo">
            <h3  style="border: 1px solid black;padding:25px 10px 25px 25px;">DAFTAR HADIR PELATIHAN</h3>
        </div>
        <div class="info-section">
            <table class="no-border" style="width:100%;">
                <tr>
                    <td class="text-start no-border" width="10%">Nomor</td>
                    <td width="3%" class="no-border">:</td>
                    <td class="text-start no-border">{{ $letterNumb }}</td>
                </tr>
                <tr>
                    <td class="text-start no-border">Hari / Tgl</td>
                    <td width="3%" class="no-border">:</td>
                    <td class="text-start no-border">{{ $day }}, {{ $date }} <span style="float: right">Jam : {{ $clock }} WIB - Selesai</span></td>
                </tr>
                <tr>
                    <td class="text-start no-border">Tempat</td>
                    <td width="3%" class="no-border">:</td>
                    <td class="text-start no-border">{{ $location }}</td>
                </tr>
                <tr>
                    <td class="text-start no-border" style="vertical-align: top">Pembicara</td>
                    <td width="3%" style="vertical-align: top" class="no-border">:</td>
                    <td class="text-start no-border no-border">
                        @foreach($trainer as $trainer)
                            {{ $trainer->user->name }}<br>
                        @endforeach
                    </td>
                </tr>
            </table>
        </div>
        <p>Pokok Bahasan: {{ $workshop }}</p>

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="30%">Nama</th>
                    <th  width="20%">Divisi / Bagian</th>
                    <th>Tanda Tangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($participants as $participant)
                    @if($loop->iteration % 2 === 0)
                        <tr>
                            <td>{{ $loop->iteration }}</td>    
                            <td class="text-start">{{ $participant->name }}</td>    
                            <td>{{ $participant->divisi }}</td>    
                            <td style="text-align: center;position: relative;">
                                @if(($participant->event->end_date < now()) && ($participant->attendance?->date_present === null))
                                    {{ $loop->iteration }}.<span style="display: inline-block;position: absolute;left:52%;"> Tidak Hadir</span>
                                @else
                                    {{ $loop->iteration }}.<img src="{{ asset('storage/signature/'.($participant->attendance?->signpresent->signature ?? 'default.svg')) }}" style="position: absolute;top:-10;left:120;" alt=" " height="60">
                                @endif
                            </td>    
                        </tr>
                    @else
                        <tr>
                            <td>{{ $loop->iteration }}</td>    
                            <td class="text-start">{{ $participant->name }}</td>    
                            <td>{{ $participant->divisi }}</td>    
                            <td style="text-align: left;position: relative;">
                                @if(($participant->event->end_date < now()) && ($participant->attendance?->date_present === null))
                                    {{ $loop->iteration }}.Tidak Hadir
                                @else
                                {{ $loop->iteration }}.<img src="{{ asset('storage/signature/'.($participant->attendance?->signpresent->signature ?? 'default.svg')) }}" style="position: absolute;top:-10;left:-10;" alt=" " height="60">
                                @endif
                            </td>    
                        </tr>
                    @endif
                @endforeach                          
            </tbody>
        </table>
    <br>
    <table class="no-border" style="width:100%;">
        <tr>
            @if($event->instructor === 'internal')
                @if($event?->trainers->count() === 1)
                    @foreach($event->trainers as $trainer)
                        <td class="no-border text-left" style="width:25%;">
                            @if($loop->iteration === 1)
                                Semarang, {{ $event->created_date ? \Carbon\Carbon::parse($event->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                            @endif
                            <br>
                            Pembicara
                            <br><br>
                            <div style="display: flex; justify-content: center;">
                                <div style="display: inline-block;">
                                    {!! DNS2D::getBarcodeHTML( $trainer?->user->name . "\n" . 'STAFF ' . $trainer->user->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                                </div>
                            </div>
                            <br>
                            <u><strong>{{ $trainer->user->name ?? '-' }}</strong></u>
                            <br>
                            <span>STAFF {{ $trainer->user->divisi ?? '-' }}</span>
                        </td>
                        <td class="no-border" style="width:75%;"></td>
                    @endforeach
                @elseif($event?->trainers->count() === 2)
                    @foreach($event->trainers as $trainer)
                        <td class="no-border text-center" style="width:50%;">
                            @if($loop->iteration === 1)
                                Semarang, {{ $event->created_date ? \Carbon\Carbon::parse($event->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                            @endif
                            <br>
                            Pembicara {{ $loop->iteration }}
                            <br><br>
                            <div style="display: flex; justify-content: center;">
                                <div style="display: inline-block;">
                                    {!! DNS2D::getBarcodeHTML( $trainer->user->name . "\n" . 'STAFF ' . $trainer->user->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                                </div>
                            </div>
                            <br>
                            <u><strong>{{ $trainer->user->name ?? '-' }}</strong></u>
                            <br>
                            <span>STAFF {{ $trainer->user->divisi ?? '-' }}</span>
                        </td>
                    @endforeach
                @else
                    @foreach($event->trainers as $trainer)
                        <td class="no-border text-center" style="width: 33.33%;">
                            @if($loop->iteration === 1)
                                Semarang, {{ $event->created_date ? \Carbon\Carbon::parse($event->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                            @endif
                            <br>
                            Pembicara {{ $loop->iteration }}
                            <br><br>
                            <div style="display: flex; justify-content: center;">
                                <div style="display: inline-block;">
                                    {!! DNS2D::getBarcodeHTML( $trainer->user->name . "\n" . 'STAFF ' . $trainer->user->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                                </div>
                            </div>
                            <br>
                            <u><strong>{{ $trainer->user->name ?? '-' }}</strong></u>
                            <br>
                            <span>STAFF {{ $trainer->user->divisi ?? '-' }}</span>
                        </td>
                    @endforeach
                @endif
            @else
                @foreach($event->trainers as $trainer)
                    <td class="no-border text-left" style="width:25%;">
                        <br>
                        Pembicara
                        <br>
                        <br>
                        <br>
                        <br>
                        <u><strong>{{ $trainer->external ?? '-' }}</strong></u>
                    </td>
                    <td class="no-border" style="width:75%;"></td>
                @endforeach
            @endif
        </tr>
    </table>
    <p style="text-align: right">F.DUP.06.R.00.T.090217</p>
</body>
</html>