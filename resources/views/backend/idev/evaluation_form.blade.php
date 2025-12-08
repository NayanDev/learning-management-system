<?php 
$fields = [
    "Fasilitas" => ["A", "B", "C", "D"],
    "Kondisi Ruangan" => ["A", "B", "C", "D"],
    "Akomodasi" => ["A", "B", "C", "D"],
    "Materi Pelatihan" => ["A", "B", "C", "D"],
    "Pembicara" => ["A", "B", "C", "D"],
    "Lain-lain..." => ["A", "B", "C", "D"],
];
$token = request('token');

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Evaluasi</title>
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
        .evaluation-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background-color: #fff;
            overflow: hidden;
        }
        .evaluation-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        .table-evaluation {
            margin-bottom: 0;
        }
        .table-evaluation th {
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            padding: 1rem;
        }
        .table-evaluation td {
            vertical-align: middle;
            padding: 1rem;
        }
        .radio-cell {
            text-align: center;
        }
        /* Custom Radio Button Styling */
        .custom-radio-input {
            width: 24px;
            height: 24px;
            cursor: pointer;
            accent-color: #0891B2; /* Warna Cyan/Teal */
        }
        .row-label {
            font-weight: 500;
            color: #212529;
        }
        /* Hover effect untuk baris */
        .table-hover tbody tr:hover {
            background-color: #f1f5f9;
        }
        .legend-box {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: #0369a1;
        }
    </style>
</head>
<body>

    <div class="container" style="max-width: 900px;">
        
        <div class="card evaluation-card">
            <div class="card-header">
                <h4 class="fw-bold mb-0">Evaluasi Pelatihan</h4>
                <p class="text-muted mb-0 mt-1">Silakan berikan penilaian Anda terhadap aspek-aspek berikut.</p>
            </div>
            
            <div class="card-body p-4">
                
                <!-- Keterangan Skala (Opsional, bisa dihapus jika tidak perlu) -->
                <div class="legend-box d-flex align-items-center">
                    <i class="ti ti-info-circle me-2 fs-4"></i>
                    <div>
                        <strong>Keterangan Skala:</strong> A = Sangat Baik, B = Baik, C = Cukup, D = Kurang
                    </div>
                </div>

                <form action="{{ route('submit.evaluation') }}" method="POST">
                    @csrf
                    <input type="text" name="token" value="{{ $token }}" hidden>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-evaluation">
                            <thead>
                                <tr>
                                    <th style="width: 40%; text-align: left;">Aspek Penilaian</th>
                                    <th style="width: 15%;">A</th>
                                    <th style="width: 15%;">B</th>
                                    <th style="width: 15%;">C</th>
                                    <th style="width: 15%;">D</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fields as $label => $values)
                                    <tr>
                                        <td class="row-label">{{ $label }}</td>
                                        @foreach($values as $value)
                                            <td class="radio-cell">
                                                <input class="form-check-input custom-radio-input" type="radio" name="evaluations[{{ $label }}]" value="{{ $value }}" required @if(old('evaluations.' . $label) == $value) checked @endif>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-4">
                            <i class="ti ti-send me-2"></i> Kirim Evaluasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>