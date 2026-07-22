@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush
<style>
    .nearby-training-list {
        max-height: 320px;
        overflow-y: auto;
        padding-right: 4px;
    }

    .nearby-training-item {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 12px 14px;
        background: #fff;
        transition: all 0.2s ease;
    }

    .nearby-training-item + .nearby-training-item {
        margin-top: 10px;
    }

    .nearby-training-item:hover {
        border-color: #b6d4fe;
        box-shadow: 0 6px 20px rgba(13, 110, 253, 0.08);
        transform: translateY(-1px);
    }

    .nearby-training-title {
        font-size: 0.95rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 2px;
    }

    .nearby-training-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #6c757d;
        font-size: 0.82rem;
    }

    .nearby-badge-day {
        min-width: 64px;
        text-align: center;
        padding: 6px 10px;
        border-radius: 999px;
        background: #e7f1ff;
        color: #0d6efd;
        font-weight: 700;
        font-size: 0.75rem;
        white-space: nowrap;
    }

    .nearby-badge-status {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .nearby-badge-status.overdue {
        background: #fff3cd;
        color: #856404;
    }

    .nearby-badge-status.filled {
        background: #d1e7dd;
        color: #0f5132;
    }

    .nearby-badge-status.empty {
        background: #f8d7da;
        color: #842029;
    }

    .nearby-empty {
        min-height: 220px;
        border: 1px dashed #ced4da;
        border-radius: 12px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
</style>
<div class="pc-container">
    <div class="pc-content">

    <div class="page-header">
        <div class="page-block">
        <div class="row align-items-center justify-content-between g-3">
            <div class="col-md-8">
            Hi, <b>{{ Auth::user()->name }} </b>
            @if(config('idev.enable_role',true))
            You are logged in as <i>{{ Auth::user()->role->name }}</i>
            @endif
            </div>
            <div class="col-md-4">
                <form method="GET" action="{{ url()->current() }}" class="d-flex justify-content-md-end">
                    <div class="input-group input-group-sm" style="max-width: 220px;">
                        <span class="input-group-text bg-white">Year</span>
                        <select name="year" class="form-select" onchange="this.form.submit()">
                            @foreach(($availableYears ?? [now()->year]) as $year)
                                <option value="{{ $year }}" {{ (int) ($selectedYear ?? now()->year) === (int) $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </div>

    @php
        $allowedRoles = ['adminhr', 'admin'];
        $allowedRoleTwo = ['officer', 'staff', 'manager'];
        $user = Auth::user();
    @endphp

    @if(in_array($user->role->name, $allowedRoles))

    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <!-- Total Pelatihan Diikuti -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-primary">
                                <i class="ti ti-school fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Trainings</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalEvents ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pelatihan Selesai -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-success">
                                <i class="ti ti-checks fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Completed Trainings</h6>
                            <h3 class="mb-0 fw-bold">{{ $completedEvents ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menunggu Konfirmasi -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-warning">
                                <i class="ti ti-clock fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Open Trainings</h6>
                            <h3 class="mb-0 fw-bold">{{ $upcomingEvents ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sertifikat -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-info">
                                <i class="ti ti-certificate fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Certificates</h6>
                            <h3 class="mb-0 fw-bold">{{ $countCertificates ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pelatihan Diikuti -->
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="avtar avtar-s bg-light-danger">
                                <i class="ti ti-stars fs-4"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0 text-muted">Total Training Requests</h6>
                            <h3 class="mb-0 fw-bold">{{ $totalTrainingWorkshops ?? '0' }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Comparation Training</h5>
                    <small class="text-muted">Tahun {{ $selectedYear ?? now()->year }} - Realisasi: {{ number_format((float) ($trainingRealizationPercentage ?? 0), 2, '.', '') }}%</small>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        ({{ (int) ($totalEvents ?? 0) }} * 100) / {{ (int) ($totalTrainingWorkshops ?? 0) }} =
                        <strong>{{ number_format((float) ($trainingRealizationPercentage ?? 0), 2, '.', '') }}%</strong>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="trainingComparisonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="ti ti-clock me-2"></i>
                        Upcoming Training - {{ $selectedYear ?? now()->year }}
                    </h5>
                </div>

                <div class="card-body">
                    <div class="nearby-training-list">

                        @forelse($trainingcoomingsoon as $event)
                            <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="nearby-training-title">
                                        {{ $event->workshop->name ?? '-' }}
                                    </div>

                                    <div class="nearby-training-meta mb-1">
                                        <i class="ti ti-calendar-event"></i>
                                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                        <span>|</span>
                                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                                    </div>

                                    <div class="nearby-training-meta">
                                        <i class="ti ti-map-pin"></i>
                                        <span>{{ $event->location ?? '-' }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-calendar-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Pelatihan Mendatang
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada jadwal pelatihan yang akan datang.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="ti ti-clock me-2"></i>
                        Evaluation after 3 month - {{ $selectedYear ?? now()->year }}
                    </h5>
                </div>

                <div class="card-body">
                    <div class="nearby-training-list">

                        @forelse($evaluationTriMonths as $event)
                            <a href="javascript:void(0)"
                            onclick="showEvaluationModal({{ $event->id }}, '{{ $event->token }}')"
                            class="text-decoration-none text-reset">

                                <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="nearby-training-title">
                                            {{ $event->workshop->name ?? '-' }}
                                        </div>

                                        <div class="nearby-training-meta mb-1">
                                            <i class="ti ti-calendar-event"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                            <span>|</span>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                                        </div>

                                        <div class="nearby-training-meta">
                                            <i class="ti ti-map-pin"></i>
                                            <span>{{ $event->location ?? '-' }}</span>
                                        </div>

                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach($event->evaluation_trainer_statuses ?? [] as $trainerStatus)
                                                @if($trainerStatus['is_filled'])
                                                    <span class="nearby-badge-status filled">
                                                        Sudah diisi oleh {{ $trainerStatus['name'] }}
                                                    </span>
                                                @else
                                                    @if(!empty($trainerStatus['overdue_badge']))
                                                        <span class="nearby-badge-status overdue">
                                                            Terlambat {{ $trainerStatus['overdue_badge'] }}
                                                        </span>
                                                    @endif

                                                    <span class="nearby-badge-status empty">
                                                        Belum diisi oleh {{ $trainerStatus['name'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-clipboard-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Evaluasi 3 Bulan
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada evaluasi 3 bulan yang perlu ditindaklanjuti.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif


    @if(in_array($user->role->name, $allowedRoleTwo))

    <div class="row g-3 mb-4">

        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="ti ti-clock me-2"></i>
                        Upcoming Training
                    </h5>
                </div>

                <div class="card-body">
                    <div class="nearby-training-list">

                        @forelse($trainingcoomingsoon as $event)
                            <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3">
                                <div>
                                    <div class="nearby-training-title">
                                        {{ $event->workshop->name ?? '-' }}
                                    </div>

                                    <div class="nearby-training-meta mb-1">
                                        <i class="ti ti-calendar-event"></i>
                                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                        <span>|</span>
                                        <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                                    </div>

                                    <div class="nearby-training-meta">
                                        <i class="ti ti-map-pin"></i>
                                        <span>{{ $event->location ?? '-' }}</span>
                                    </div>
                                </div>

                                <span class="nearby-badge-day">
                                    {{ $event->is_trainer ? 'Trainer' : 'Participant' }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-calendar-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Pelatihan Mendatang
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada jadwal pelatihan yang akan datang.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">
                        <i class="ti ti-clock me-2"></i>
                        Evaluation after 3 month
                    </h5>
                </div>

                <div class="card-body">
                    <div class="nearby-training-list">

                        @forelse($evaluationTriMonths as $event)
                            <a href="{{ url('evaluation-month') }}?token={{ $event->token }}"
                            class="text-decoration-none text-reset">

                                <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="nearby-training-title">
                                            {{ $event->workshop->name ?? '-' }}
                                        </div>

                                        <div class="nearby-training-meta mb-1">
                                            <i class="ti ti-calendar-event"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                            <span>|</span>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                                        </div>

                                        <div class="nearby-training-meta">
                                            <i class="ti ti-map-pin"></i>
                                            <span>{{ $event->location ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>

                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-clipboard-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Evaluasi 3 Bulan
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada evaluasi 3 bulan yang perlu diisi.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif


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
            {{-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed-training" type="button" role="tab" aria-controls="completed-training" aria-selected="false">
                    Riwayat Pelatihan Selesai
                </button>
            </li> --}}
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
                                                {{ ucwords(strtolower($trainer->user?->name ?? $trainer->external ?? '-')) }}
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof Chart === 'undefined') {
        return;
    }

    var chartCanvas = document.getElementById('trainingComparisonChart');
    if (!chartCanvas) {
        return;
    }

    var totalPermintaan = {{ (int) ($totalTrainingWorkshops ?? 0) }};
    var totalRealisasi = {{ (int) ($totalEvents ?? 0) }};

    new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: ['Jumlah Rencana (Training Request)', 'Jumlah Terealisasi (Total Trainings)'],
            datasets: [{
                label: 'Jumlah',
                data: [totalPermintaan, totalRealisasi],
                backgroundColor: ['rgba(13, 110, 253, 0.70)', 'rgba(25, 135, 84, 0.70)'],
                borderColor: ['rgba(13, 110, 253, 1)', 'rgba(25, 135, 84, 1)'],
                borderWidth: 1,
                borderRadius: 8,
                maxBarThickness: 80
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            return 'Jumlah: ' + context.raw;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});

    function showEvaluationModal(eventId, token) {
        Swal.fire({
            title: 'Evaluasi Pelatihan',
            text: 'Pilih salah satu aksi berikut:',
            icon: 'question',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Isi Evaluasi',
            denyButtonText: 'Lihat Report',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            denyButtonColor: '#6c757d',
        }).then((result) => {
            if (result.isConfirmed) {
                // Isi Evaluasi
                window.location.href = '/evaluation-month?token=' + token;
            } else if (result.isDenied) {
                // View Report
                window.location.href = '/evaluation-pdf?event_id=' + eventId;
            }
        });
    }

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
