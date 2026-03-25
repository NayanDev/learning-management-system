<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Evaluasi Peserta - {{ $event->workshop->name ?? 'N/A' }}</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F7F8;
            padding: 2rem;
        }
        .result-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background-color: #fff;
            margin-bottom: 1.5rem;
        }
        .table-evaluation {
            margin-bottom: 0;
        }
        .table-evaluation th {
            background-color: #f8f9fa;
            font-weight: 600;
            text-align: center;
            vertical-align: middle;
            padding: 0.75rem;
        }
        .table-evaluation td {
            vertical-align: middle;
            padding: 0.75rem;
        }
        .badge-category {
            font-size: 1rem;
            padding: 0.5rem 1rem;
            font-weight: 600;
        }
        .badge-A { background-color: #198754; color: white; }
        .badge-B { background-color: #0dcaf0; color: white; }
        .badge-C { background-color: #ffc107; color: black; }
        .badge-D { background-color: #dc3545; color: white; }
        
        .legend-item {
            display: inline-flex;
            align-items: center;
            margin-right: 1rem;
            margin-bottom: 0.5rem;
        }
        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            margin-right: 0.5rem;
        }
        
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <div class="container-fluid" style="max-width: 1400px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
            <div>
                <h3 class="fw-bold mb-1">Hasil Evaluasi Peserta Pelatihan</h3>
                <p class="text-muted mb-0">
                    Pelatihan: <strong>{{ $event->workshop->name ?? 'N/A' }}</strong><br>
                    Periode: {{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($event->end_date)->format('d M Y') }}
                </p>
            </div>
            <div>
                <a href="{{ route('evaluation.month') }}?token={{ request('token') }}" class="btn btn-secondary me-2">
                    <i class="ti ti-arrow-left me-1"></i> Kembali ke Form
                </a>
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="ti ti-printer me-1"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Legenda Kategori -->
        <div class="card result-card no-print">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Keterangan Kategori Nilai:</h6>
                <div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #198754;"></div>
                        <span><strong>A (Sangat Baik)</strong> = 16-20</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #0dcaf0;"></div>
                        <span><strong>B (Baik)</strong> = 11-15</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #ffc107;"></div>
                        <span><strong>C (Cukup)</strong> = 6-10</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background-color: #dc3545;"></div>
                        <span><strong>D (Kurang)</strong> = 0-5</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Hasil Evaluasi Per Peserta -->
        @foreach($evaluationData as $participantId => $data)
            @php
                $participant = $data['participant'];
                $evaluations = $data['evaluations'];
            @endphp
            
            @if($evaluations->isNotEmpty())
            <div class="card result-card">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1 fw-bold">{{ $participant->name }}</h5>
                            <small class="text-muted">NIK: {{ $participant->nik ?? 'N/A' }} | Divisi: {{ $participant->divisi ?? 'N/A' }}</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-evaluation">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">No</th>
                                    <th style="width: 50%;">Aspek Penilaian</th>
                                    <th style="width: 15%;">Nilai</th>
                                    <th style="width: 15%;">Kategori</th>
                                    <th style="width: 15%;">P.I</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $no = 1;
                                    $totalNilai = 0;
                                    $jumlahAspek = 0;
                                @endphp
                                
                                @foreach($evaluations as $aspekName => $evalList)
                                    @php
                                        $eval = $evalList->first();
                                        $totalNilai += $eval->value;
                                        $jumlahAspek++;
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $no++ }}</td>
                                        <td>{{ $aspekName }}</td>
                                        <td class="text-center fw-bold">{{ $eval->value }}</td>
                                        <td class="text-center">
                                            <span class="badge badge-category badge-{{ $eval->category }}">
                                                {{ $eval->category }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-category badge-{{ $eval->category }}">
                                                {{ $eval->category }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                                
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-end">TOTAL / RATA-RATA</td>
                                    <td class="text-center">{{ $totalNilai }}</td>
                                    <td class="text-center">
                                        @php
                                            $rataRata = $jumlahAspek > 0 ? round($totalNilai / $jumlahAspek, 1) : 0;
                                            $kategoriRataRata = \App\Models\EvaluationMonth::calculateCategory($rataRata);
                                        @endphp
                                        {{ $rataRata }}
                                    </td>
                                    <td class="text-center">
                                        <span class="badge badge-category badge-{{ $kategoriRataRata }}">
                                            {{ $kategoriRataRata }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        @if($evaluationData->isEmpty() || $evaluationData->filter(fn($d) => $d['evaluations']->isNotEmpty())->isEmpty())
        <div class="card result-card">
            <div class="card-body text-center py-5">
                <i class="ti ti-alert-circle" style="font-size: 3rem; color: #ffc107;"></i>
                <h5 class="mt-3">Belum Ada Data Evaluasi</h5>
                <p class="text-muted">Silakan isi form evaluasi terlebih dahulu.</p>
                <a href="{{ route('evaluation.month') }}?token={{ request('token') }}" class="btn btn-primary">
                    <i class="ti ti-forms me-1"></i> Isi Form Evaluasi
                </a>
            </div>
        </div>
        @endif

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
