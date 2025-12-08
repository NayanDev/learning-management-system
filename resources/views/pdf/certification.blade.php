<?php 
use Carbon\Carbon;

$imageTemplate = asset('images/certification/' . $template->template);
$sertificateNumber = $certification->number_certification;
$participantName = $certification->participant->name;
$typeOfParticipation = $certification->category;
$date = $certification->participant->event->end_date;
$formattedDate = Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY');
$eventDate = "Semarang, " . $formattedDate;
$note = $template->note ?? '';

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Certification</title>
    <style>
        *{
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }
        body {
            background-image: url('{{ $imageTemplate }}'); /* Ganti dengan path gambar yang diinginkan */
            background-size: cover; /* Agar gambar menutupi seluruh area */
            background-position: center center; /* Memastikan gambar selalu terpusat */
            background-repeat: no-repeat; /* Menghindari pengulangan gambar */
        }
        .sertifikat {
            /* border: 1px solid black; */
            text-align: center;
            font-size: 100px;
            margin: 0;
            padding-top: 65px;
            padding-bottom: 0;
        }
        .no_sertifikat {
            /* border: 1px solid black; */
            text-align: center;
            font-size: 23px;
            padding: 0;
            margin: 0;
        }
        .diberikan {
            /* border: 2px solid black; */
            padding-top: 20px;
            text-align: center;
            font-size: 24px;
        }
        .nama {
            /* border: 2px solid black; */
            text-align: center;
            padding-top: 20px;
            font-size: 60px;
        }
        .ket-partisipasi {
            /* border: 2px solid black; */
            text-align: center;
            font-size: 24px;
        }
        .peserta {
            /* border: 2px solid black; */
            text-align: center;
            font-size: 55px;
        }
        .note {
            text-align: center;
            font-size: 20px;
            width: 900px;
            margin: 20px auto;
            /* border: 1px solid black; */
            height: 50px;
        }
        .tanggal {
            /* border: 2px solid black; */
            text-align: center;
            font-size: 25px;
            margin-top:40px;
        }
    </style>
</head>
<body>
    <h1 class="sertifikat">&nbsp;</h1>
    <h2 class="no_sertifikat">No. {{ $sertificateNumber }}</h2>
    <h4 class="diberikan">&nbsp;</h4>
    <h1 class="nama">{{ $participantName }}</h1>
    <h4 class="ket-partisipasi">&nbsp;</h4>
    <br>
    <h2 class="peserta">{{ $typeOfParticipation }}</h2>
    <div class="note">
        {{ $note }}
    </div>
    <h5 class="tanggal">{{ $eventDate }}</h5>
</body>
</html>