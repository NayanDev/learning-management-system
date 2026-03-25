<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluation Pelatihan</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-size: 12px;
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }

        .checklist {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 18px;
        }

        .container {
            /* border: 1px solid black; */
        }

        .text-start {
            text-align: left;
        }

        .letterhead {
            position: relative;
            margin-bottom: 10px;
            overflow: visible;
            padding-bottom: 10px;
            /* border: 1px solid black; */
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
            margin-bottom: 15px;
            font-size: 14px;
            /* border: 1px solid salmon; */
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 4px;
            /* text-align: center; */
        }

        .no-border {
            border: none !important;
        }

        .new-border {
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-top: 0px;
            border-bottom: 0px;
        }

        .text-center {
            text-align: center;
        }

        .th {
            border: 1px solid black;
            font-size: 7px;
            margin: 0;
            padding: 0;
            text-align: center;
            vertical-align: middle;
            height: 80px;
            width: 25px;
            position: relative;
            /* Tambahkan ini */
        }

        .rotate-text {
            position: absolute;
            /* Posisi absolute agar bisa full */
            top: 50%;
            /* Posisi vertical center */
            left: 50%;
            /* Posisi horizontal center */
            transform: translate(-50%, -50%) rotate(-90deg);
            /* Gabung translate dan rotate */
            width: 80px;
            /* Sesuaikan dengan height th */
            display: flex;
            /* Gunakan flex untuk centering content */
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .highlight {
            background-color: gray;
            /* Warna kuning */
        }

        .penilaian {
            margin:0;
            padding:0;
            text-align:center;
            border:none;
        }

        .bordered {
            border: 1px solid black;
        }

        .border-lr {
            border-top: none;
            border-left: 1px solid black;
            border-right: 1px solid black;
            border-bottom: none;
        }

        .page-break { page-break-before: always; }
    </style>
</head>

<body>
@foreach($participants as $index => $participant)
@php
    $participantEvaluations = $evaluationsData[$participant->id]['evaluations'] ?? collect();
    
    // Evaluasi dari peserta (type = peserta)
    $evalFasilitas = $participantEvaluations->get('Fasilitas');
    $evalKondisiRuangan = $participantEvaluations->get('Kondisi Ruangan');
    $evalAkomodasi = $participantEvaluations->get('Akomodasi');
    $evalMateri = $participantEvaluations->get('Materi Pelatihan');
    $evalPembicara = $participantEvaluations->get('Pembicara');
    $evalLainLain = $participantEvaluations->get('Lain-lain...');
    
    // Evaluasi dari penyelenggara (type = penyelenggara)
    $evalPenguasaanTeori = $participantEvaluations->get('Penguasaan Teori');
    $evalPenguasaanPraktek = $participantEvaluations->get('Penguasaan Praktek');
    $evalKedisiplinan = $participantEvaluations->get('Kedisiplinan & Prilaku');
    
    // Evaluasi Monthly dari EvaluationMonth
    $monthlyEvaluations = \App\Models\EvaluationMonth::where('event_id', $event->id)
        ->where('participant_id', $participant->id)
        ->get()
        ->keyBy('name');
    
    // Helper function untuk mendapatkan nilai sesuai kategori
    $getValue = function($evalName, $category) use ($monthlyEvaluations) {
        $eval = $monthlyEvaluations->get($evalName);
        if ($eval && $eval->category == $category) {
            return $eval->value;
        }
        return '';
    };
    
    // Query ResultQuestion untuk participant ini
    $currentResultQuestion = \App\Models\ResultQuestion::where('participant_id', $participant->id)->first();
@endphp
    <div class="container">
        <table>
            <tr class="bordered">
                <td class="no-border"><img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" alt="PT Sampharindo" width="40px"></td>
                <td class="no-border">
                    <h3 style="text-align:right">EVALUASI PELATIHAN</h3>
                    <p style="text-align:right">Internal / <del>Eksternal</del></p>
                </td>
            </tr>
        </table>

        <br>

        <table>
            <tr>
                <td>I. Peserta Pelatihan</td>
            </tr>
            <tr>
                <td>

                    <table>
                        <tr>
                            <td valign="top" width="50%" class="no-border">
                                <table style="margin:0; padding:0;">
                                    <tr>
                                        <td style="width: 3%" class="no-border text-center">1.</td>
                                        <td style="width: 30%" class="no-border">Nama</td>
                                        <td style="width: 3%" class="no-border">:</td>
                                        <td class="no-border">{{ $participant->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-border text-center">2.</td>
                                        <td class="no-border">Divisi / Bagian</td>
                                        <td class="no-border">:</td>
                                        <td class="no-border">{{ $participant->divisi ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-border text-center">3.</td>
                                        <td class="no-border">Jenis Pelatihan</td>
                                        <td class="no-border">:</td>
                                        <td class="no-border">{{ $event->workshop->name ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-border text-center">4.</td>
                                        <td class="no-border">Penyelenggara</td>
                                        <td class="no-border">:</td>
                                        <td class="no-border">{{ $event->organizer ?? '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                            <td valign="top" class="no-border">
                                <table style="margin:0; padding:0;">
                                    <tr>
                                        <td style="width: 3%" class="no-border text-center">5.</td>
                                        <td style="width: 30%" class="no-border">Pembicara</td>
                                        <td style="width: 3%" class="no-border">:</td>
                                        <td class="no-border">{{ $event->instructor ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-border text-center">6.</td>
                                        <td class="no-border">Tempat</td>
                                        <td class="no-border">:</td>
                                        <td class="no-border">{{ $event->location ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="no-border text-center">7.</td>
                                        <td class="no-border">Hari / Tanggal Pelaksanaan</td>
                                        <td class="no-border">:</td>
                                        <td class="no-border">{{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->translatedFormat('l, d F Y') : '-' }}</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <br>

        <table>
            <tr>
                <td colspan="4">II. Evaluasi Penyelenggara Pelatihan*) Diisi Oleh Peserta Pelatihan.</td>
            </tr>
            <tr>
                <td width="5%">No.</td>
                <td class="text-center">URAIAN</td>
                <td class="text-center">PENILAIAN</td>
                <td class="text-center" width="25%">PRE TEST</td>
            </tr>
            <tr>
                <td class="text-center border-lr">1</td>
                <td class="border-lr">Fasilitas</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                A <br>
                                @if($evalFasilitas && $evalFasilitas->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                B <br>
                                @if($evalFasilitas && $evalFasilitas->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                C <br>
                                @if($evalFasilitas && $evalFasilitas->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                D <br>
                                @if($evalFasilitas && $evalFasilitas->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="6"><p style="font-size: 40px;text-align:center;">{{ intval($currentResultQuestion->pretest_score) ?? '-' }}</p></td>
            </tr>
            <tr>
                <td class="text-center border-lr">2</td>
                <td class="border-lr">Kondisi Ruangan</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                @if($evalKondisiRuangan && $evalKondisiRuangan->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalKondisiRuangan && $evalKondisiRuangan->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalKondisiRuangan && $evalKondisiRuangan->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalKondisiRuangan && $evalKondisiRuangan->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center border-lr">3</td>
                <td  class="border-lr">Akomodasi</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                @if($evalAkomodasi && $evalAkomodasi->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalAkomodasi && $evalAkomodasi->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalAkomodasi && $evalAkomodasi->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalAkomodasi && $evalAkomodasi->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center border-lr">4</td>
                <td class="border-lr">Materi Pelatihan</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                @if($evalMateri && $evalMateri->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalMateri && $evalMateri->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalMateri && $evalMateri->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalMateri && $evalMateri->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center border-lr">5</td>
                <td class="border-lr">Pembicara</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                @if($evalPembicara && $evalPembicara->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalPembicara && $evalPembicara->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalPembicara && $evalPembicara->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalPembicara && $evalPembicara->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="text-center border-lr">6</td>
                <td class="border-lr">Lain - lain .....</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian">
                                @if($evalLainLain && $evalLainLain->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalLainLain && $evalLainLain->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalLainLain && $evalLainLain->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian">
                                @if($evalLainLain && $evalLainLain->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="position:relative;">
                        @php
                            $user = \App\Models\User::where('nik', $participant->nik)->first();
                        @endphp
                        Peserta Pelatihan,<br>
                        @if($participant->nik)
                            <img style="position:absolute" src="{{ asset('storage/signature/' . $user->signature) }}" alt="Tanda Tangan Peserta" height="70px"><br><br><br><br>
                        @else
                            <br><br><br>
                        @endif
                        ({{ $participant->name ?? '.............................' }})
                    </div>
                </td>
                <td colspan="2" valign="top">
                    Catatan :
                </td>
            </tr>
            <tr>
                <td colspan="4">III. Evaluasi Peserta Pelatihan*) Diisi Oleh Penyelenggara Pelatihan / Pembicara.</td>
            </tr>
            <tr>
                <td c width="5%">No.</td>
                <td>URAIAN</td>
                <td>PENILAIAN</td>
                <td width="25%">KESIMPULAN PENILAIAN</td>
            </tr>
            <tr>
                <td class="border-lr text-center">1</td>
                <td class="border-lr">Penguasaan Teori</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian no-border">
                                A <br>
                                @if($evalPenguasaanTeori && $evalPenguasaanTeori->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                B <br>
                                @if($evalPenguasaanTeori && $evalPenguasaanTeori->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                C <br>
                                @if($evalPenguasaanTeori && $evalPenguasaanTeori->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                D <br>
                                @if($evalPenguasaanTeori && $evalPenguasaanTeori->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="4"></td>
            </tr>
            <tr>
                <td class="border-lr text-center">2</td>
                <td class="border-lr">Penguasaan Praktek</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian no-border">
                                @if($evalPenguasaanPraktek && $evalPenguasaanPraktek->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalPenguasaanPraktek && $evalPenguasaanPraktek->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalPenguasaanPraktek && $evalPenguasaanPraktek->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalPenguasaanPraktek && $evalPenguasaanPraktek->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="border-lr text-center">3</td>
                <td class="border-lr">Kedisiplinan & Prilaku</td>
                <td class="border-lr">
                    <table>
                        <tr>
                            <td class="penilaian no-border">
                                @if($evalKedisiplinan && $evalKedisiplinan->score == 'A') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalKedisiplinan && $evalKedisiplinan->score == 'B') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalKedisiplinan && $evalKedisiplinan->score == 'C') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                            <td class="penilaian no-border">
                                @if($evalKedisiplinan && $evalKedisiplinan->score == 'D') <input type="checkbox" checked> @else <input type="checkbox"> @endif
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="border-lr text-center">4</td>
                <td class="border-lr">Evaluasi Implementasi</td>
                <td class="border-lr"> Form. Lampiran 1 </td>
            </tr>
            <tr>
                <td colspan="2" style="position:relative;">
                    Semarang,  {{ \Carbon\Carbon::parse($event->end_date)->translatedFormat('d F Y') }}<br>
                    Penyelenggara Pelatihan,<br><br>
                    <img src="{{ asset('storage/signature/' . $penyelenggara->signature) }}" style="position:absolute;top:30px" alt="signature" height="70px">
                    <br><br>
                    {{ $penyelenggara->name }} <br><br><br>
                    Pembicara, / Pimpinan<br><br>
                    <img src="{{ asset('storage/signature/' . $trainer->user->signature) }}" style="position:absolute;top:130px" alt="signature" height="70px">
                    <br><br>
                    {{ $trainer->user->name }}
                </td>
                <td colspan="2" valign="top">
                    Catatan : <br><br><br><br><br><br><br><br><br><br><br>

                    Catatan : berilah tanda "<span class="checklist">&#10003;</span>" pada kotak penilaian
                </td>
            </tr>
        </table>
        <p style="text-align: right;">F.DUP.29.R.01.T.170222</p>
    </div>

    <div class="page-break"></div>

    <div class="container">
        <p style="text-align: right;">Form. Lampiran 1</p>
        <table>
            <tr>
                <td colspan="10">
                    <table>
                        <tr>
                            <td class="no-border" width="15%">Nama</td>
                            <td class="no-border" width="5%" align="center">:</td>
                            <td class="no-border">{{ $participant->name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Divisi / Bagian</td>
                            <td class="no-border" align="center">:</td>
                            <td class="no-border">{{ $participant->divisi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="no-border">Periode Penilaian</td>
                            <td class="no-border" align="center">:</td>
                            <td class="no-border">3 Bulan / Tahun {{ date('Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="5%" rowspan="3">No.</td>
                <td rowspan="3">ASPEK EVALUASI PENILAIAN</td>
                <td class="text-center" colspan="8">SKOR PENILAIAN</td>
            </tr>
            <tr>
                <td class="text-center" colspan="2">A</td>
                <td class="text-center" colspan="2">B</td>
                <td class="text-center" colspan="2">C</td>
                <td class="text-center" colspan="2">D</td>
            </tr>
            <tr>
                <td class="text-center" colspan="2">16 - 20</td>
                <td class="text-center" colspan="2">11 - 15</td>
                <td class="text-center" colspan="2">6 - 10</td>
                <td class="text-center" colspan="2">0 - 5</td>
            </tr>
            <tr>
                <td colspan="2">I. Aspek Teknis Pekerjaan</td>
                <td class="text-center" width="5%">P.I</td>
                <td class="text-center" width="5%">P.II</td>
                <td class="text-center" width="5%">P.I</td>
                <td class="text-center" width="5%">P.II</td>
                <td class="text-center" width="5%">P.I</td>
                <td class="text-center" width="5%">P.II</td>
                <td class="text-center" width="5%">P.I</td>
                <td class="text-center" width="5%">P.II</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Efektivitas & Efesiensi Kerja</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Ketepatan Waktu Dalam Menyelesaikan Tugas</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Kemampuan Mencapai Target / Standar Perusahaan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">II. Aspek Non Teknis</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Tertib Administrasi</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Inisiatif</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Kerjasama / Koordinasi Antar Bagian</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">III. Aspek Kepribadian</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Perilaku</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Kedisiplinan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Tanggung Jawab & Loyalitas</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Ketaatan Terhadap Instruksi Kerja</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">IV. Aspek Kepemimpinan</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td>Koordinasi Bawahan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">2</td>
                <td>Kontrol / Pengendalian Bawahan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">3</td>
                <td>Evaluasi dan Pembinaan Bawahan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">4</td>
                <td>Delegasi Tanggung Jawab dan Wewenang</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td class="text-center">5</td>
                <td>Kecepatan & Ketepatan Pengambilan Keputusan</td>
                <td>20</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                {{-- Klasifikasi Nilai & Total nilai --}}
                <td colspan="10">
                    <table width="100%">
                        <tr>
                            <td width="60%" valign="top" class="no-border">
                                <table width="100%">
                                    <tr>
                                        <td colspan="3" class="text-center">KLASIFIKASI NILAI</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">Nilai Mutu</td>
                                        <td class="text-center">Bobot</td>
                                        <td class="text-center">Keterangan</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">A</td>
                                        <td class="text-center">240 - 800</td>
                                        <td class="text-center">Sangat Baik</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">B</td>
                                        <td class="text-center">165 - 239</td>
                                        <td class="text-center">Baik</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">C</td>
                                        <td class="text-center">75 - 164</td>
                                        <td class="text-center">Cukup</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">D</td>
                                        <td class="text-center">0 - 75</td>
                                        <td class="text-center">Buruk</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="40%" valign="top" class="no-border">
                                <table width="100%">
                                    <tr>
                                        <td colspan="2" class="text-center">Total Nilai</td>
                                    </tr>
                                    <tr>
                                        <td valign="top" class="text-center">
                                            P.I <br>
                                            &nbsp; <br><br>
                                        </td>
                                        <td valign="top" class="text-center">
                                            P.II <br>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-center">Nilai Akhir</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"> &nbsp; </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="10" style="position: relative">
                    POST TEST: <br><br><br>
                    <span style="font-size:40px;position:absolute;top:20px;">{{ intval($currentResultQuestion->posttest_score1) ?? '-' }}</span>
                    <br><br><br> 
                </td>
            </tr>
            <tr>
                <td colspan="10" style="padding: 0; margin:0">
                    <table style="padding: 0px; margin:0px">
                        <tr>
                            <td width="33%" class="text-center no-border">
                                Penilai I <br><br><br><br><br>
                            </td>
                            <td width="33%" class="text-center new-border">
                                Penilai II <br><br><br><br><br>
                            </td>
                            <td width="33%" class="text-center no-border">
                                Mengetahui, <br><br><br><br><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p style="text-align: right ;">F.DUP.29.R.01.T.170222</p>
    </div>

@if(!$loop->last)
    <div class="page-break"></div>
@endif
@endforeach
</body>

</html>

