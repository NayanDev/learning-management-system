<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validasi Tanda Tangan Digital</title>
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
            background-color: #e2e8f0; /* Background lebih gelap agar kartu menonjol */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        .validation-card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            background-color: #fff;
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            position: relative;
            border-top: 6px solid #10b981; /* Green Top Border for "Valid" status */
        }
        
        .card-header-status {
            background-color: #ecfdf5; /* Light Green */
            color: #047857; /* Dark Green */
            padding: 1.5rem;
            text-align: center;
            border-bottom: 1px dashed #d1fae5;
        }

        .signer-section {
            padding: 2rem 1.5rem;
            text-align: center;
            position: relative;
        }

        .signature-box {
            position: relative;
            display: inline-block;
            margin: 1rem 0;
            padding: 1rem 2rem;
            /* Garis putus-putus seperti area tanda tangan di dokumen */
            border-bottom: 2px solid #cbd5e1; 
        }

        .signature-img {
            height: 90px;
            object-fit: contain;
            display: block;
            /* Efek tinta biru gelap */
            filter: drop-shadow(1px 1px 0px rgba(0,0,0,0.1)) brightness(0.8) sepia(1) hue-rotate(180deg) saturate(3);
            mix-blend-mode: multiply;
        }

        .digital-stamp {
            position: absolute;
            top: 50%;
            right: -20px;
            transform: translateY(-50%) rotate(-15deg);
            width: 90px;
            height: 90px;
            border: 3px double #10b981; /* Green Stamp */
            border-radius: 50%;
            color: #10b981;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            box-shadow: 0 0 0 1px #10b981 inset;
            z-index: 0;
            background-color: rgba(255, 255, 255, 0.9); /* Sedikit transparan agar menimpa ttd */
        }
        .digital-stamp span {
            text-align: center;
            line-height: 1.2;
        }

        .context-info {
            background-color: #f8fafc;
            border-radius: 0.75rem;
            padding: 1rem;
            margin: 0 1.5rem 1.5rem 1.5rem;
            border: 1px solid #e2e8f0;
        }

        .verification-code {
            font-family: 'Courier New', Courier, monospace;
            background: #f1f5f9;
            padding: 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.8rem;
            letter-spacing: 1px;
            color: #475569;
            display: block;
            text-align: center;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>

    <div class="validation-card">
        <!-- Status Validitas -->
        <div class="card-header-status">
            <div class="d-flex align-items-center justify-content-center mb-1">
                <i class="ti ti-circle-check-filled fs-2 me-2"></i>
                <h5 class="fw-bold mb-0">Tanda Tangan Sah</h5>
            </div>
            <small class="opacity-75">Dokumen ini telah diverifikasi secara digital.</small>
        </div>

        <!-- Bagian Penandatangan -->
        <div class="signer-section">
            <p class="text-muted small text-uppercase fw-bold mb-0">Ditandatangani Oleh</p>
            
            <!-- Area Tanda Tangan dengan Stempel -->
            <div class="signature-box">
                <!-- Stempel Digital -->
                <div class="digital-stamp">
                    <span>Digital<br>Verified<br>LMS</span>
                </div>
                <!-- Gambar Tanda Tangan -->
                <img src="{{ asset('storage/signature/'.($participant->attendance?->signpresent->signature ?? 'default.svg')) }}" alt="Tanda Tangan" class="signature-img">
            </div>

            <h4 class="fw-bold text-dark mb-1">Bapak Budi Hartono</h4>
            <div class="d-flex align-items-center justify-content-center text-primary">
                {{-- <i class="ti ti-certificate me-1"></i> --}}
                {{-- <span class="fw-medium">Senior Safety Trainer</span> --}}
            </div>
            {{-- <p class="small text-muted mt-2">
                Ditandatangani pada: <strong>25 Okt 2025 • 08:45 WIB</strong>
            </p> --}}
        </div>

        <!-- Konteks Pelatihan -->
        <div class="context-info">
            <h6 class="fw-bold text-dark mb-2 border-bottom pb-2">Konteks Dokumen:</h6>
            <div class="d-flex mb-2">
                <i class="ti ti-file-certificate text-secondary me-2 mt-1"></i>
                <div>
                    <small class="text-muted d-block">Judul Pelatihan</small>
                    <span class="fw-medium text-dark">Pelatihan K3 Fundamental</span>
                </div>
            </div>
            <div class="d-flex">
                <i class="ti ti-building text-secondary me-2 mt-1"></i>
                <div>
                    <small class="text-muted d-block">Penyelenggara</small>
                    <span class="fw-medium text-dark">PT Sampharindo Perdana</span>
                </div>
            </div>
        </div>

        <!-- Footer ID Verifikasi -->
        {{-- <div class="bg-light p-3 text-center border-top">
            <small class="text-muted d-block mb-1">ID Verifikasi Digital:</small>
            <div class="verification-code">
                LMS-SIGN-8839-2025-XCV
            </div>
        </div> --}}
    </div>

</body>
</html>