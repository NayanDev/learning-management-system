<?php

namespace App\Services;

use App\Models\Approval;
use Illuminate\Support\Facades\Auth;

class SignatureService
{
    public function create($training)
    {
        $map = [
        'TrainingAnalyst' => 'Analisa Kebutuhan Latihan',
        'TrainingNeed'    => 'Rencana Usulan Pelatihan',
        'Training'        => 'Training Schedule',
        'Event'           => 'Surat Perintah Pelatihan',
        'Certification'   => 'Sertifikat',
        'ReportTraining'  => 'Laporan Pelatihan',
        'Matrix'          => 'Matrix',
    ];

    $name = $map[class_basename($training)] ?? '-';

        return Approval::create([
            'name'           => $name,
            'approval_type'  => class_basename($training),
            'approval_id'    => $training->id,
            'user_id'        => Auth::user()->id,
            'date'           => now()
        ]);
    }
}