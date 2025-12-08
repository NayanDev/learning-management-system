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

        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Nama</th>
                    <th  width="20%">Divisi / Bagian</th>
                    <th>Tanda Tangan</th>
                    <th width="20%">Keterangan</th>
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
                                @if(($participant->event->end_date < now()) && ($participant->attendance?->date_ready === null))
                                    {{ $loop->iteration }}. <span style="display: inline-block;position: absolute;left:52%;"> Tidak Hadir</span>
                                @else
                                    {{ $loop->iteration }}.<img src="{{ asset('storage/signature/'.($participant->attendance?->signready->signature ?? 'default.svg')) }}" style="position: absolute;top:-10;left:70;" alt=" " height="60"></span>
                                @endif
                            </td>    
                            <td></td>    
                        </tr>
                    @else
                        <tr>
                            <td>{{ $loop->iteration }}</td>    
                            <td class="text-start">{{ $participant->name }}</td>    
                            <td>{{ $participant->divisi }}</td>    
                            <td style="text-align: left;position: relative;">
                                @if(($participant->event->end_date < now()) && ($participant->attendance?->date_ready === null))
                                    {{ $loop->iteration }}.Tidak Hadir
                                @else
                                    {{ $loop->iteration }}.<img src="{{ asset('storage/signature/'.($participant->attendance?->signready->signature ?? 'default.svg')) }}" style="position: absolute;top:-10;left:-10;" alt=" " height="60">
                                @endif
                            </td>    
                            <td></td>    
                        </tr>  
                    @endif
                @endforeach               
            </tbody>
        </table>
        <p>
            Untuk mengikuti "{{ $workshop }}" pada hari {{ $day }}, {{ $date }}, Pukul {{ $clock }} WIB - Selesai, bertempat di {{ $location }} <br>
            Demikian Surat Perintah Pelatihan ini dibuat agar dilaksanakan dengan penuh tanggung jawab. Atas perhatiannya kami ucapkan terima kasih.
        </p>
    <br>
    <table class="no-border" style="width:100%;">
        <tr>
            <td class="no-border text-center"style="width:25%;">
                {{ $event->created_date ? \Carbon\Carbon::parse($event->created_date)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
                <br>
                Dibuat Oleh,
                <br><br>
                @if($event->status === 'approve')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( $event->approver->name . "\n" . 'Manager ' . $event->approver->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                    </div>
                </div>
                <br>
                <u><strong>{{ $event->approver->name ?? '-' }}</strong></u>
                <br>
                <span>Manager {{ optional($event->approver)->divisi ?? '-' }}</span>
                @elseif($event->status === 'submit')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML( $event->approver->name . "\n" . 'Manager ' . $event->approver->divisi . "\n" . '(ini adalah dokumen resmi dan sah)', 'QRCODE', 1, 1 ) !!}
                    </div>
                </div>
                <br>
                <u><strong>{{ $event->approver->name ?? '-' }}</strong></u>
                <br>
                <span>Manager {{ optional($event->approver)->divisi ?? '-' }}</span>
                @else
                <div style="height: 50px"></div>
                <u><strong>{{ $event->approver->name ?? '-' }}</strong></u>
                <br>
                <span>Manager {{ optional($event->approver)->divisi ?? '-' }}</span>
                @endif
            </td>
            <td class="no-border" style="width:25%;"></td>
            <td class="no-border" style="width:25%;"></td>
            <td class="no-border text-center"style="width:25%;">
                <br>
                Mengetahui,
                <br><br>
                @if($event->status === 'approve')
                <div style="display: flex; justify-content: center;">
                    <div style="display: inline-block;">
                        {!! DNS2D::getBarcodeHTML("MAKMURI YUSIN\nDirektur Umum & SDM\n(ini adalah dokumen resmi dan sah)", 'QRCODE', 1, 1) !!}
                    </div>
                </div>
                <br>
                <u><strong>MAKMURI YUSIN</strong></u>
                <br>
                <span>Direktur Umum & SDM</span>
                @else
                <div style="height: 50px"></div>
                <em>Data belum tersedia</em>
                @endif
            </td>
        </tr>
    </table>
    <p style="text-align: right">F.DUP.05.R.00.T.090217</p>
</body>
</html>