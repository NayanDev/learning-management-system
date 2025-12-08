<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('easyadmin/theme/'.config('idev.theme','default').'/css/style.css')}}" id="main-style-link" />
    <link rel="stylesheet" href="{{ asset('easyadmin/theme/default/fonts/tabler-icons.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('custom/css/participant_account.css') }}">
    <title>{{ $title }}</title>
</head>
<body>
    <main class="container py-5 ">
        <div class="header-info">
            <h2>Akses website Learning Management System</h2>
            <p class="text-muted">Silahkan pindai kode QR ini untuk mengakses LMS Sampharindo.</p>
            <hr>
            <div class="qr-code">
                {!! DNS2D::getBarcodeHTML($link, 'QRCODE', 15,15) !!}
            </div>
            <p class="mb-0 text-muted">Akses LMS melalui:</p>
            <a href="https://lms.sampharindo.id/" target="_blank" class="link-text">
                https://lms.sampharindo.id/<i class="ti ti-external-link icon-xs ms-1"></i>
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h3 class="mb-0">
                    <i class="ti ti-users text-primary me-2"></i>
                    Daftar Akun Peserta
                </h3>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        
                        <thead class="table-light">
                            <tr>
                                <th scope="col" style="width: 5%;">No.</th>
                                <th scope="col">Nama</th>
                                <th scope="col">Email / Username</th>
                                <th scope="col">Divisi</th>
                                <th scope="col">Password</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $index => $participant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $participant->name }}</td>
                                    <td>{{ Str::slug($participant->name) }}</td>
                                    <td>{{ $participant->divisi }}</td>
                                    <td>{{ 'pelatihan'.$participant->event->year }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>
</html>