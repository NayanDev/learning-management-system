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

        .page-break { page-break-before: always; }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <tr>
                <td>Logo PT Sampharindo</td>
                <td>
                    <h3>EVALUASI PELATIHAN</h3>
                    <span>Internal / <del>Eksternal</del></span>
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
                            <td valign="top" width="50%">
                                1. Nama : Nayantaka<br>
                                2. Divisi / Bagian : Umum & SDM<br>
                                3. Jenis Pelatihan : Time Management<br>
                                4. Penyelenggara : HRD<br>
                            </td>
                            <td valign="top">
                                5. Pembicara : Cahyanti Fitri Hafidha<br>
                                6. Tempat : R. Coparcetin PT Sampharindo Perdana<br>
                                7. Hari / Tanggal Pelaksanaan : Kamis, 13 Maret 2025<br>
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
                <td>URAIAN</td>
                <td>PENILAIAN</td>
                <td width="25%">PRE TEST</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Fasilitas</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                A <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                B <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                C <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                D <br>
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="6"></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Kondisi Ruangan</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Akomodasi</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Materi Pelatihan</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>5</td>
                <td>Pembicara</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>6</td>
                <td>Lain - lain .....</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    Peserta Pelatihan,<br><br><br><br><br>
                    (.............................)
                </td>
                <td colspan="2" valign="top">
                    Catatan :
                </td>
            </tr>
            <tr>
                <td colspan="4">III. Evaluasi Peserta Pelatihan*) Diisi Oleh Penyelenggara Pelatihan / Pembicara.</td>
            </tr>
            <tr>
                <td width="5%">No.</td>
                <td>URAIAN</td>
                <td>PENILAIAN</td>
                <td width="25%">KESIMPULAN PENILAIAN</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Penguasaan Teori</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                A <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                B <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                C <br>
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                D <br>
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
                <td rowspan="4"></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Penguasaan Praktek</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>3</td>
                <td>Kedisiplinan & Prilaku</td>
                <td>
                    <table>
                        <tr>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                            <td class="penilaian">
                                <input type="checkbox">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>4</td>
                <td>Evaluasi Implementasi</td>
                <td> Form. Lampiran 1 </td>
            </tr>
            <tr>
                <td colspan="2">
                    Semarang, .................... 2025<br>
                    Penyelenggara Pelatihan,<br><br><br><br><br>
                    (.............................) <br><br><br>
                    Pembicara, / Pimpinan<br><br><br><br><br>
                    (.............................)
                </td>
                <td colspan="2" valign="top">
                    Catatan : <br><br><br><br><br><br><br><br><br><br><br><br><br><br>

                    Catatan : berilah tanda " √ " pada kotak penilaian
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
                            <td width="15%">Nama</td>
                            <td width="5%" align="center">:</td>
                            <td>Nayantaka</td>
                        </tr>
                        <tr>
                            <td>Divisi / Bagian</td>
                            <td align="center">:</td>
                            <td>QA</td>
                        </tr>
                        <tr>
                            <td>Periode Penilaian</td>
                            <td align="center">:</td>
                            <td>3 Bulan / Tahun 2025</td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td width="5%" rowspan="3">No.</td>
                <td rowspan="3">ASPEK EVALUASI PENILAIAN</td>
                <td colspan="8">SKOR PENILAIAN</td>
            </tr>
            <tr>
                <td colspan="2">A</td>
                <td colspan="2">B</td>
                <td colspan="2">C</td>
                <td colspan="2">D</td>
            </tr>
            <tr>
                <td colspan="2">16 - 20</td>
                <td colspan="2">11 - 15</td>
                <td colspan="2">6 - 10</td>
                <td colspan="2">0 - 5</td>
            </tr>
            <tr>
                <td colspan="2">I. Aspek Teknis Pekerjaan</td>
                <td width="5%">P.I</td>
                <td width="5%">P.II</td>
                <td width="5%">P.I</td>
                <td width="5%">P.II</td>
                <td width="5%">P.I</td>
                <td width="5%">P.II</td>
                <td width="5%">P.I</td>
                <td width="5%">P.II</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Efektivitas & Efesiensi Kerja</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>10</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Ketepatan Waktu Dalam Menyelesaikan Tugas</td>
                <td></td>
                <td></td>
                <td>11</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Kemampuan Mencapai Target / Standar Perusahaan</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>10</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">II. Aspek Non Teknis</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Tertib Administrasi</td>
                <td></td>
                <td></td>
                <td>11</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Inisiatif</td>
                <td></td>
                <td></td>
                <td>11</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Kerjasama / Koordinasi Antar Bagian</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>6</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">III. Aspek Kepribadian</td>
            </tr>
            <tr>
                <td>1</td>
                <td>Perilaku</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td>6</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Kedisiplinan</td>
                <td></td>
                <td></td>
                <td>11</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Tanggung Jawab & Loyalitas</td>
                <td></td>
                <td></td>
                <td>12</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Ketaatan Terhadap Instruksi Kerja</td>
                <td></td>
                <td></td>
                <td>12</td>
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
                <td>1</td>
                <td>Koordinasi Bawahan</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>2</td>
                <td>Kontrol / Pengendalian Bawahan</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>3</td>
                <td>Evaluasi dan Pembinaan Bawahan</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>4</td>
                <td>Delegasi Tanggung Jawab dan Wewenang</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td>5</td>
                <td>Kecepatan & Ketepatan Pengambilan Keputusan</td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="10">
                    <table width="100%">
                        <tr>
                            <td colspan="3">KLASIFIKASI NILAI</td>
                        </tr>
                        <tr>
                            <td>Nilai Mutu</td>
                            <td>Bobot</td>
                            <td>Keterangan</td>
                        </tr>
                        <tr>
                            <td>A</td>
                            <td>240 - 800</td>
                            <td>Sangat Baik</td>
                        </tr>
                        <tr>
                            <td>B</td>
                            <td>165 - 239</td>
                            <td>Baik</td>
                        </tr>
                        <tr>
                            <td>C</td>
                            <td>75 - 164</td>
                            <td>Cukup</td>
                        </tr>
                        <tr>
                            <td>D</td>
                            <td>0 - 75</td>
                            <td>Buruk</td>
                        </tr>
                    </table>

                    <br><br>

                    <table width="100%">
                        <tr>
                            <td colspan="2">Total Nilai</td>
                        </tr>
                        <tr>
                            <td valign="top">
                                P.I <br>
                                150 <br><br>
                            </td>
                            <td valign="top">
                                P.II <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">Nilai Akhir</td>
                        </tr>
                        <tr>
                            <td colspan="2"> &nbsp; </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="10">
                    POST TEST: <br><br><br><br><br><br><br><br><br><br>
                </td>
            </tr>
            <tr>
                <td colspan="10">
                    <table>
                        <tr>
                            <td width="33%">
                                Penilai I <br><br><br><br><br> <br><br><br><br><br>
                            </td>
                            <td width="33%">
                                Penilai II <br><br><br><br><br> <br><br><br><br><br>
                            </td>
                            <td width="33%">
                                Mengetahui, <br><br><br><br><br> <br><br><br><br><br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p style="text-align: right ;">F.DUP.29.R.01.T.170222</p>
    </div>
</body>

</html>