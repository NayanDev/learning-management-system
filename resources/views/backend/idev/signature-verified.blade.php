<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Approver Dokumen</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts (Inter) -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9; /* Latar belakang abu-abu sangat muda */
        }
        
        .card {
            border-radius: 1rem;
            transition: all 0.3s ease;
        }

        .info-label {
            color: #6c757d;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .info-value {
            font-weight: 600;
            color: #212529;
        }

        .section-title {
            font-size: 0.8rem;
            letter-spacing: 1px;
            color: #495057;
        }

        .custom-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-top-left-radius: 1rem !important;
            border-top-right-radius: 1rem !important;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8 col-xl-6">
                
                <!-- Card Container -->
                <div class="card shadow-sm border-0">
                    
                    <!-- Header Card -->
                    <div class="card-header custom-header text-white p-4 border-0 d-flex align-items-center">
                        <div class="bg-white bg-opacity-25 p-3 rounded-circle me-3 d-flex justify-content-center align-items-center" style="width: 50px; height: 50px;">
                            <i class="fas fa-file-signature fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-1 fw-bold">Learning Management System</h4>
                            <p class="mb-0 text-white-50 small">Halaman detail persetujuan dokumen pelatihan.</p>
                        </div>
                    </div>

                    <!-- Body Card -->
                    <div class="card-body p-4 p-md-5">
                        
                        <!-- Section 1: Informasi Approver -->
                        <h6 class="text-uppercase fw-bold section-title mb-4 border-bottom pb-2">
                            <i class="fas fa-user-tie me-2 text-primary"></i> Informasi Approver
                        </h6>
                        
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 info-label">Nama</div>
                            <div class="col-sm-8 info-value">{{ $approval->user->name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 info-label">Divisi</div>
                            <div class="col-sm-8 info-value">{{ $approval->user->divisi ?? '-' }}</div>
                        </div>
                        <div class="row mb-4 align-items-center">
                            <div class="col-sm-4 info-label">Jabatan</div>
                            <div class="col-sm-8 info-value">
                                {{ $approval->user?->role?->name === 'manager'
                                    ? 'Manager ' . ($approval->user?->divisi ?? '')
                                    : $approval->user->unit_kerja }}
                            </div>
                        </div>

                        <!-- Section 2: Informasi Dokumen -->
                        <h6 class="text-uppercase fw-bold section-title mb-4 mt-5 border-bottom pb-2">
                            <i class="fas fa-file-alt me-2 text-primary"></i> Informasi Dokumen
                        </h6>

                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 info-label">Nama Dokumen</div>
                            <div class="col-sm-8 info-value">{{ $approval->name ?? '-' }}</div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 info-label">Nomor Dokumen</div>
                            <div class="col-sm-8">
                                <span class="badge bg-light text-dark border border-secondary-subtle px-3 py-2 fs-6">{{ $nomordokumen ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-sm-4 info-label">Tanggal Submit</div>
                            <div class="col-sm-8 info-value">
                                <i class="far fa-calendar-alt me-1 text-muted"></i> {{ $approval->date . ' WIB' ?? '-' }}
                            </div>
                        </div>
                        
                    </div>

                    <!-- Footer Card (Action Buttons) -->

                </div>
                <!-- End Card Container -->

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>