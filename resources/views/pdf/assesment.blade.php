@php
    $type = request('type');
    $selectedAnswers = [];
    foreach($answerParticipants as $answerParticipant) {
        $selectedAnswers[] = $answerParticipant->answer->name;
    }
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Assesment Report</title>
    <link rel="icon" href="{{ asset('easyadmin/idev/img/favicon.png') }}" type="image/png">
    <style>
        body {
            font-size: 12px;
            margin: 0;
            padding: 0;
            font-family: 'Tahoma', Geneva, sans-serif;
        }

        .text-start {
            text-align: left;
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
            /* border: 1px solid #000; */
            padding: 4px;
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

        .score {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            border: 1px solid black;
            padding: 15px 15px;
        }

        .options {
            display: inline-block; 
            width:48%; 
            padding-bottom:10px;
            position:relative;
            /* border: 1px solid black; */
            /* height: 30px; */
        }

        .option-cross {
            position: absolute;
            font-size: 20px;
            top: -4px;
            left: -2px;
        }

    </style>
</head>
<body>
    <div style="border: 1px solid black;">
        <table>
            <tr>
                <td width="10%">
                    <img src="{{ asset('easyadmin/idev/img/kop-dokumen.png') }}" width="50" alt="PT Sampharindo">
                </td>
                <td>
                    <h2>
                        {{ $type === 'pre_test' ? 'PRE-TEST' : 'POST-TEST' }}<br>
                        QUESTIONS
                </td>
                <td>
                    <table>
                        <tr>
                            <td width="20%">Tanggal</td>
                            <td width="5%">:</td>
                            <td>
                                {{ \Carbon\Carbon::parse($person->created_at)->format('d-m-Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Nama</td>
                            <td>:</td>
                            <td>{{ $person->name }}</td>
                        </tr>
                        <tr>
                            <td>Divisi</td>
                            <td>:</td>
                            <td>{{ $person->divisi }}</td>
                        </tr>
                    </table>
                </td>
                <td width="10%">
                    <div class="score">
                        @if($score->pretest_score && $type === 'pre_test')
                            {{ number_format($score->pretest_score, 0) }}
                        @elseif($score->posttest1_score && $type === 'post_test')
                            {{ number_format($score->posttest1_score, 0) }}
                        @else
                            {{ number_format($score->posttest2_score, 0) }}
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <br>
    <p>Jawablah Pertanyaan berikut ini!</p>
    <div>
        @foreach($questions as $question)

                @if(isset($question->image))
                    <img src="{{ asset('images/soal/' . $question->image) }}" alt="Question Image" style="max-height: 100px;"> <br>
                @endif

            <p style="position: relative;">
                
                    @foreach ($question->answerParticipants as $answerParticipant)
                        @if($answerParticipant->point === 0)
                            <span class="option-cross">X</span> 
                        @endif
                    @endforeach
                {{ $loop->iteration }}. {{ $question->name }}
            </p>
            @foreach($question->answers as $index => $answer)
                <span class="options">
                    @foreach($answerParticipants as $answerParticipant)
                        @if($answer->name === $answerParticipant->answer->name && $answerParticipant->question_id === $question->id)
                            <span class="option-cross">X</span> 
                        @endif
                    @endforeach
                {{ chr(65 + $index) }}. {{ $answer->name }}
                </span>
            @endforeach
        @endforeach
        
    </div>

</body>
</html>