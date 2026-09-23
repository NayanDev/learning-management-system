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
        $user = Auth::user();
        $roleManager = $userRole->isManager();
        $roleDeveloper = $userRole->isDeveloper();
        $roleSupervisiHR = $userRole->isSupervisiHR();
        $roleSupervisi = $userRole->isSupervisi();
        $roleAdministrator = $userRole->isAdministrator();
        $roleTrainer = $userRole->isTrainer();
        $roleParticipant = $userRole->isParticipant();
    @endphp
    
    {{-- Training Need Warning --}}
    @if($hasOpenNextYearTraining && $hasTrainingNeed->end_date >= now()->format('Y-m-d'))
        @include('backend.dashboard.trainingNeedWarning', ['hasTrainingNeed' => $hasTrainingNeed ?? false,
        'tnaAdmins' => $tnaAdmins ?? []])
    @endif

    {{-- Include Signature Warning --}}
    @if(Auth::user()->signature == null || Auth::user()->signature == '')
        @include('backend.dashboard.signatureWarning')
    @endif

    {{-- Include Surat Perintah Attendance --}}
    @if($eventsAttendance->isNotEmpty())
        @include('backend.dashboard.suratPerintahAttendance', ['eventsAttendance' => $eventsAttendance])
    @endif

    {{-- Dashboard Statistics --}}
    @include('backend.dashboard.statistic', ['statistics' => $statistics])
    
    @if($roleDeveloper || $roleSupervisiHR)

        <div class="row g-3 pb-4">
            
            {{-- Include Comparison Training --}}
            @include('backend.dashboard.comparationtraining', [
                'selectedYear' => $selectedYear,
                'totalTrainingWorkshops' => $totalTrainingWorkshops,
                'totalEvents' => $totalEvents,
                'trainingRealizationPercentage' => $trainingRealizationPercentage
            ])

            {{-- Include Upcoming Training --}}
            @include('backend.dashboard.upcomingtraining', [
                'selectedYear' => $selectedYear,
                'trainingcoomingsoon' => $trainingcoomingsoon
            ])

            {{-- Include Evaluation After 3 Month --}}
            @include('backend.dashboard.evaluationAfterTriMonth', [
                'selectedYear' => $selectedYear,
                'evaluationTriMonths' => $evaluationTriMonths,
                'evaluationNotCompleted' => $evaluationNotCompleted ?? collect()
            ])

            {{-- Include Training Need Analyst --}}
            @include('backend.dashboard.trainingNeedAnalyst')

            {{-- Include Approval Management --}}
            @include('backend.dashboard.approvalManagement')
        </div>

    @endif

    @if($roleSupervisi)
        <div class="row g-3 pb-4">
            {{-- Include Upcoming Training --}}
            @include('backend.dashboard.upcomingtraining', [
                'selectedYear' => $selectedYear,
                'trainingcoomingsoon' => $trainingcoomingsoon
            ])

            {{-- Include Evaluation After 3 Month --}}
            @include('backend.dashboard.evaluationAfterTriMonth', [
                'selectedYear' => $selectedYear,
                'evaluationTriMonths' => $evaluationTriMonths,
                'evaluationNotCompleted' => $evaluationNotCompleted ?? collect()
            ])

            {{-- Include Training Need Analyst --}}
            @include('backend.dashboard.trainingNeedAnalyst')

            @if($roleManager)
                {{-- Include Approval Management --}}
                @include('backend.dashboard.approvalManagement')
            @endif

        </div>
    @endif

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
