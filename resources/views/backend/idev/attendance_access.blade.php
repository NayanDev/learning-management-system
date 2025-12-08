<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('easyadmin/theme/'.config('idev.theme','default').'/css/style.css')}}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('easyadmin/theme/default/fonts/tabler-icons.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('custom/css/attendance_access.css') }}">
    <title>{{ $title }}</title>
</head>
<body>
    <div class="attendance-card">

        <h3 class="fw-bold text-dark mb-2">Kartu Kehadiran Pelatihan</h3>
        <p class="text-muted mb-4">Silakan pindai kode QR ini untuk mencatat kehadiran Anda pada sesi pelatihan.</p>

        <hr class="my-4">

        <div class="qr-code-section">
            <p class="mb-3 text-muted">Arahkan kamera Anda ke kode di bawah ini:</p>
            <div class="qrcode-center">
                {!! DNS2D::getBarcodeHTML($barcode, 'QRCODE') !!}
            </div>
            <p class="mt-3 fw-bold text-primary">Scan untuk Kehadiran!</p>
        </div>
        
        <p class="text-muted small mt-4 mb-0">Pastikan Anda telah masuk ke sistem sebelum memindai.</p>

    </div>
</body>
</html>