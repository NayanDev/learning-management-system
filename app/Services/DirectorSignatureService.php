<?php

namespace App\Services;

use App\Models\DirectorSignature;
use Exception;

class DirectorSignatureService
{
    public function create($training, string $signatureData)
    {
        $type = class_basename($training);

        // Ambil data lama
        $existing = DirectorSignature::where([
            'approval_type' => $type,
            'approval_id' => $training->id,
        ])->first();

        $oldPath = $existing?->signature;

        // simpan file baru
        $signaturePath = $this->storeDirectorSignatureImage(
            $signatureData,
            $training->id
        );

        // update / create DB
        $result = DirectorSignature::updateOrCreate(
            [
                'approval_type' => $type,
                'approval_id' => $training->id,
            ],
            [
                'name' => 'MAKMURI YUSIN',
                'divisi' => '-',
                'position' => 'DIREKTUR',
                'signature' => $signaturePath,
                'date' => now(),
            ]
        );

        // hapus file lama (kalau ada)
        if ($oldPath && $oldPath !== $signaturePath) {
            $oldFile = public_path($oldPath);

            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }

        return $result;
    }

    protected function storeDirectorSignatureImage(string $signatureData, int $trainingId): string
    {
        // validasi SVG
        if (!str_contains($signatureData, '<svg')) {
            throw new Exception('Format tanda tangan tidak valid.');
        }

        $directory = public_path('director_signatures');

        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileName = 'training_' . $trainingId . '_director_' . time() . '.svg';
        $filePath = $directory . DIRECTORY_SEPARATOR . $fileName;

        file_put_contents($filePath, $signatureData);

        return 'director_signatures/' . $fileName;
    }
}