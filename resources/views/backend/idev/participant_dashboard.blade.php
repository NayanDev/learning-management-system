@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush
<div class="pc-container">
    <div class="pc-content">

    <div class="page-header">
        <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
            Hi, <b>{{ Auth::user()->name }} </b> 
            @if(config('idev.enable_role',true))
            You are logged in as <i>{{ Auth::user()->role->name }}</i> 
            @endif
            </div>
        </div>
        </div>
    </div>

    <div class="row">

        <div class="col-12">
            <!-- Navigasi Tab -->
        <ul class="nav nav-tabs mb-4" id="trainingTab" role="tablist">
            @if(Auth::user()->signature == null || Auth::user()->signature == '')
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ Auth::user()->signature == null || Auth::user()->signature == '' ? 'active' : '' }}" id="warning-tab" data-bs-toggle="tab" data-bs-target="#warning-training" type="button" role="tab" aria-controls="warning-training" aria-selected="false">
                    Warning
                </button>
            </li>
            @endif
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ Auth::user()->signature !== null ? 'active' : '' }}" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming-training" type="button" role="tab" aria-controls="upcoming-training" aria-selected="true">
                    Surat Perintah Pelatihan
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-training" type="button" role="tab" aria-controls="completed-training" aria-selected="false">
                    Riwayat Pelatihan Selesai
                </button>
            </li>
        </ul>
        </div>
        
        <!-- Konten Tab -->
    <div class="tab-content" id="trainingTabContent">
        <!-- ============================================== -->
        <!-- Panel 1: Warning (Aktif) -->
        <!-- ============================================== -->
        <div class="tab-pane fade {{ Auth::user()->signature == null || Auth::user()->signature == '' ? 'show active' : '' }}" id="warning-training" role="tabpanel" aria-labelledby="warning-tab">
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="card">
                        @if(Auth::user()->signature == null || Auth::user()->signature == '')
                            <div class="card-body">
                                <h5 class="card-title">Peringatan: Update Tanda Tangan!</h5>
                                <b></b>
                                <p>
                                    1. masuk ke menu <b>account settings</b> dibagian kanan atas<br>
                                    2. scroll kebawah hingga menemukan bagian <b>signature</b><br>
                                    3. buat tanda tangan digital anda dengan mouse atau touchscreen.<br>
                                    4. klik <b>save as svg</b> untuk menyimpan tanda tangan dalam format svg<br>
                                    4. (optional) jika anda sudah memiliki tanda tangan dalam format gambar (png/svg), silahkan upload pada bagian <b>upload signature</b><br>
                                    5. klik <b>update profile</b> untuk menyimpan perubahan tanda tangan<br><br>
                                    <b>Note: pastikan tanda tangan yang dibuat berukuran sedang (tidak besar / kecil) posisi ditengah</b> <br><br>
                                    <b>Contoh pembuatan:</b> <br><br>
                                    <img src="{{ asset('img/signature-tutorial.png') }}" style="width:100%">
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
        
        <!-- ============================================== -->
        <!-- Panel 2: Pelatihan Akan Datang -->
        <!-- ============================================== -->
        <div class="tab-pane fade {{ Auth::user()->signature !== null ? ' show active' : '' }}" id="upcoming-training" role="tabpanel" aria-labelledby="upcoming-tab">
            <div class="row g-4">

                @php
                    $hasUpcomingTraining = false;
                @endphp

                @foreach($eventsAttendance as $event)
                    @if(!$event->attendance?->date_ready)
                        @php
                            $hasUpcomingTraining = true;
                        @endphp
                        <div class="col-lg-6">
                            <div class="card">
                                    <div class="card-body p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h5 class="fw-bold text-dark mb-1">{{ $event->event->workshop->name ?? '-' }}</h5>
                                            <p class="card-subtitle text-muted">Instruktur: 
                                                @foreach($event->event->trainers as $trainer)
                                                {{ ucwords(strtolower($trainer->user->name)) }}
                                                    @if(!$loop->last) 
                                                        ,
                                                    @endif
                                                @endforeach
                                                ({{ $event->event->instructor }})
                                            </p>
                                        </div>
                                        <span class="badge bg-warning rounded-pill px-3 py-2 flex-shrink-0">Confirmation</span>
                                    </div>

                                    <hr class="my-3">

                                    <div class="row g-3">
                                        <div class="col-md-6 d-flex align-items-center">
                                            <i class="ti ti-calendar-event fs-4 text-muted me-2"></i>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->translatedFormat('d F Y') ?? '-' }}</span>
                                        </div>
                                        <div class="col-md-6 d-flex align-items-center">
                                            <i class="ti ti-clock fs-4 text-muted me-2"></i>
                                            <span class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->format('H:i') ?? '-' }} - {{ \Carbon\Carbon::parse($event->event->end_date)->format('H:i') ?? '-' }} WIB</span>
                                        </div>
                                        <div class="col-12 d-flex align-items-center">
                                            <i class="ti ti-map-pin fs-4 text-muted me-2"></i>
                                            <span class="fw-medium">{{ $event->event->location ?? '-' }}</span>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <div class="token-area d-flex justify-content-center align-items-center ">
                                        <button class="btn btn-sm btn-info" onclick="confirmAttendance('{{ $event->event->token }}')">
                                            <i class="ti ti-check me-1"></i> Attendance
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if(!$hasUpcomingTraining)
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="ti ti-calendar-off text-muted" style="font-size: 4rem;"></i>
                                <h5 class="mt-3 text-muted">Tidak Ada Pelatihan yang Perlu Dikonfirmasi</h5>
                                <p class="text-muted">Saat ini tidak ada pelatihan yang menunggu konfirmasi kehadiran Anda.</p>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

        <!-- ============================================== -->
        <!-- Panel 3: Riwayat Pelatihan Selesai -->
        <!-- ============================================== -->
        <div class="tab-pane fade" id="completed-training" role="tabpanel" aria-labelledby="completed-tab">
            <div class="row g-4">

                <!-- Kartu 3: Pelatihan Selesai (Dari Desain Anda) -->
                @foreach($eventsAttendance as $event)
                @if($event->out_present)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h5 class="fw-bold text-dark mb-1">{{ $event->event->workshop->name ?? '-' }}</h5>
                                    <p class="card-subtitle text-muted">Instruktur: {{ $event->event->speaker ?? '-' }} ({{ $event->event->instructor }})</p>
                                </div>
                                <span class="badge bg-success rounded-pill px-3 py-2 flex-shrink-0">Selesai</span>
                            </div>

                            <hr class="my-3">

                            <div class="row g-3">
                                <div class="col-md-6 d-flex align-items-center">
                                    <i class="ti ti-calendar-event fs-4 text-muted me-2"></i>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->translatedFormat('d F Y') ?? '-' }}</span>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <i class="ti ti-clock fs-4 text-muted me-2"></i>
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->format('H:i') ?? '-' }} - {{ \Carbon\Carbon::parse($event->event->end_date)->format('H:i') ?? '-' }} WIB</span>
                                </div>
                                <div class="col-12 d-flex align-items-center">
                                    <i class="ti ti-map-pin fs-4 text-muted me-2"></i>
                                    <span class="fw-medium">{{ $event->event->location ?? '-' }}</span>
                                </div>
                            </div>

                            <hr class="my-3">
                            <p class="text-muted text-center mb-0">Pelatihan ini telah selesai.</p>
                        </div>
                    </div>
                </div>
                @endif
                @endforeach

            </div>
        </div>
    </div>


    
    </div>
    </div>
</div>

<script>
    function confirmAttendance(token) {
    var url = "{{ route('participant.attendance.form.ready') }}";
    var csrf = $('meta[name="csrf-token"]').attr('content') || "{{ csrf_token() }}";

  // Tampilkan konfirmasi dulu
    Swal.fire({
    title: 'Konfirmasi Kehadiran',
    text: 'Apakah kamu yakin ingin mengonfirmasi kehadiran?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Ya, Saya Hadir',
    cancelButtonText: 'Batal',
    reverseButtons: true
    }).then((result) => {
    if (result.isConfirmed) {
      // Ubah UI tombol
        $('#confirmButton')
        .attr('disabled', true)
        .html('<i class="ti ti-loader ti-spin me-2"></i> Memproses...');

      // Kirim request AJAX
        $.ajax({
        url: url,
        type: 'POST',
        data: {
            _token: csrf,
            token: token
        },
        success: function (response) {
            if (response.status) {
            // Notifikasi sukses
            Swal.fire({
                title: 'Berhasil!',
                text: response.message || 'Kehadiran kamu berhasil dikonfirmasi.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
              // Reload halaman setelah sukses
              location.reload(); // Memuat ulang halaman
            });

            // Update tampilan halaman
            $('#confirmationMessage').removeClass('d-none');
            $('#confirmButton').remove();
            $('.badge')
                .removeClass('text-bg-warning')
                .addClass('text-bg-success')
                .text('Sudah Konfirmasi');
            } else {
            // Gagal dari server
            $('#confirmButton').removeAttr('disabled')
                .html('<i class="ti ti-circle-check me-2"></i> Saya Hadir');

            Swal.fire('Gagal', response.message || 'Gagal memproses konfirmasi.', 'error');
            }
        },
        error: function (xhr) {
            $('#confirmButton').removeAttr('disabled')
            .html('<i class="ti ti-circle-check me-2"></i> Saya Hadir');

            var msg = 'Terjadi kesalahan. Silakan coba lagi.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
            }

            Swal.fire('Gagal', msg, 'error');
        }
        });
    } else {
      // Jika user menekan "Batal"
        Swal.fire({
        title: 'Dibatalkan',
        text: 'Konfirmasi kehadiran dibatalkan.',
        icon: 'info',
        timer: 1500,
        showConfirmButton: false
        });
    }
    });
}
</script>
@endsection
