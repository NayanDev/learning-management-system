<?php 
    $totalPeserta = count($participants);
    $fields = [
        "Penguasaan Teori" => ["A", "B", "C", "D"],
        "Penguasaan Praktek" => ["A", "B", "C", "D"],
        "Kedisiplinan & Prilaku" => ["A", "B", "C", "D"],
    ];

?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluasi Massal Peserta</title>
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
            min-height: 600px; /* Tinggi minimal agar terlihat proporsional */
        }
        .evaluation-card .card-header {
            background-color: #fff;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        
        /* Styling untuk Navigasi Kiri (Sidebar Peserta) */
        .participant-sidebar {
            background-color: #f8f9fa;
            border-right: 1px solid #e9ecef;
            height: 100%;
            max-height: 600px; /* Batas tinggi agar bisa di-scroll */
            overflow-y: auto; /* Scroll vertikal jika data banyak */
        }
        
        .nav-pills-custom .nav-link {
            border-radius: 0;
            border-left: 4px solid transparent;
            color: #495057;
            font-weight: 500;
            padding: 1rem 1.5rem;
            text-align: left;
            transition: all 0.2s ease;
            background-color: transparent;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .nav-pills-custom .nav-link:hover {
            background-color: #e9ecef;
            color: #0891B2;
        }
        .nav-pills-custom .nav-link.active {
            background-color: #E6F4F1; /* Warna latar aktif (Cyan muda) */
            color: #0891B2; /* Teks aktif (Cyan) */
            border-left-color: #0891B2; /* Border kiri aktif */
            font-weight: 600;
        }
        .nav-pills-custom .nav-link .icon-wrapper {
            display: flex;
            align-items: center;
        }
        
        /* Status Indicator di List */
        .status-dot {
            height: 8px;
            width: 8px;
            background-color: #dee2e6;
            border-radius: 50%;
            display: inline-block;
        }
        .nav-link.filled .status-dot {
            background-color: #198754; /* Hijau jika sudah diisi */
        }

        /* Area Konten Kanan */
        .content-area {
            padding: 1.5rem;
            height: 100%;
            max-height: 600px;
            overflow-y: auto;
        }

        /* Tabel Evaluasi */
        .table-evaluation th {
            text-align: center;
            vertical-align: middle;
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
            padding: 0.8rem;
        }
        .table-evaluation td {
            vertical-align: middle;
            padding: 0.8rem;
        }
        .radio-cell {
            text-align: center;
        }
        .custom-radio-input {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #0891B2;
        }
        .row-label {
            font-weight: 500;
            color: #212529;
        }
        .legend-box {
            background-color: #e0f2fe;
            border: 1px solid #bae6fd;
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            color: #0369a1;
            display: inline-flex;
            align-items: center;
        }
        
        /* Scrollbar halus */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
    </style>
</head>
<body>

    <div class="container-fluid" style="max-width: 1200px;">
        
        <div class="d-flex justify-content-between align-items-end mb-4">
            <div>
                <h3 class="fw-bold mb-1">Evaluasi Peserta Pelatihan</h3>
                <p class="text-muted mb-0">Pelatihan: <strong>{{ $event->workshop->name ?? 'N/A' }}</strong> | Total Peserta: {{ $totalPeserta }}</p>
            </div>
            <div class="legend-box">
                <i class="ti ti-info-circle me-2 fs-5"></i>
                <strong>Skala:</strong>&nbsp; A (Sangat Baik) - D (Kurang)
            </div>
        </div>

        <div class="card evaluation-card">
            
            <!-- Form Utama -->
            <form id="formEvaluationBulk" action="{{ route('submit.evaluation.bulk') }}" method="POST">
                @csrf
                
                <input type="hidden" name="token" value="{{ request('token') }}">
                <input type="hidden" name="event_id" value="{{ $event->id }}">

                <div class="row g-0 h-100">
                    
                    <!-- KOLOM KIRI: DAFTAR PESERTA (SIDEBAR) -->
                    <div class="col-md-3 participant-sidebar">
                        <div class="nav flex-column nav-pills nav-pills-custom" id="participantTabs" role="tablist" aria-orientation="vertical">
                            @foreach($participants as $index => $participant)
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-participant-{{ $participant->id }}" data-bs-toggle="pill" data-bs-target="#content-participant-{{ $participant->id }}" data-participant-id="{{ $participant->id }}" type="button" role="tab">
                                    <div class="icon-wrapper">
                                        <i class="ti ti-user me-2"></i> 
                                        <span class="participant-name">{{ $participant->name }}</span>
                                    </div>
                                    <span class="status-dot" title="Belum diisi"></span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- KOLOM KANAN: KONTEN EVALUASI -->
                    <div class="col-md-9">
                        <div class="content-area">
                            <div class="tab-content" id="participantTabsContent">

                                @foreach($participants as $index => $participant)
                                    <div 
                                        class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                        id="content-participant-{{ $participant->id }}" 
                                        role="tabpanel"
                                        data-participant-id="{{ $participant->id }}"
                                    >

                                    {{-- Header Info Peserta --}}
                                        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-3">
                                            <div>
                                                <h5 class="fw-bold mb-1">
                                                    Penilaian untuk: <span class="text-primary">{{ $participant->name }}</span>
                                                </h5>
                                                <small class="text-muted">
                                                    {{ $participant->divisi ?? 'N/A' }} | NIK: {{ $participant->nik ?? 'N/A' }}
                                                </small>
                                            </div>
                                            <span class="badge bg-light text-dark border">ID: {{ $participant->id }}</span>
                                        </div>

                                        {{-- Hidden Input: Participant ID --}}
                                        <input type="hidden" name="evaluations[{{ $index }}][participant_id]" value="{{ $participant->id }}">

                                        {{-- Tabel Evaluasi --}}
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
                                                    {{-- ← LOOP: Generate Row untuk Setiap Field --}}
                                                    @foreach($fields as $label => $values)
                                                        @php
                                                            // Buat field name yang unik per peserta
                                                            $fieldName = strtolower(str_replace([' ', '&'], ['_', 'dan'], $label));
                                                        @endphp
                                                        <tr>
                                                            <td class="row-label">{{ $label }}</td>
                                                            @foreach($values as $value)
                                                                <td class="radio-cell">
                                                                    {{-- ← NAME UNIK PER PESERTA: evaluations[index][field_name] --}}
                                                                    <input 
                                                                        class="form-check-input custom-radio-input evaluation-radio" 
                                                                        type="radio" 
                                                                        name="evaluations[{{ $index }}][{{ $fieldName }}]" 
                                                                        value="{{ $value }}"
                                                                        data-participant-id="{{ $participant->id }}"
                                                                        required
                                                                    >
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    
                                                    {{-- Row Tambahan --}}
                                                    <tr>
                                                        <td class="row-label">Evaluasi Implementasi</td>
                                                        <td colspan="4" class="text-center text-muted">Form. Lampiran 1</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                        {{-- Catatan Tambahan --}}
                                        {{-- <div class="mt-3">
                                            <label class="form-label fw-medium">Catatan Tambahan (Opsional)</label>
                                            <textarea 
                                                class="form-control" 
                                                name="evaluations[{{ $index }}][catatan]" 
                                                rows="3" 
                                                placeholder="Berikan catatan khusus untuk {{ $participant->name }}..."
                                            ></textarea>
                                        </div> --}}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Footer Tombol Simpan -->
                        <div class="border-top pt-3 mt-auto bg-white sticky-bottom p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="progress-info">
                                    <i class="ti ti-checkbox me-1"></i> 
                                    <span id="filledCount">0</span> dari {{ $totalPeserta }} peserta telah dinilai.
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg px-4" id="btnSubmit">
                                    <i class="ti ti-device-floppy me-2"></i> Simpan Semua Evaluasi
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // ← FUNCTION: Cek apakah semua field di tab peserta sudah diisi
            function checkParticipantCompletion(participantId) {
                const tabContent = $(`#content-participant-${participantId}`);
                const totalRadioGroups = tabContent.find('.row-label').length; // Total aspek penilaian
                let filledGroups = 0;

                // Cek setiap radio group apakah sudah ada yang checked
                tabContent.find('input[type="radio"]').each(function() {
                    const name = $(this).attr('name');
                    if ($(`input[name="${name}"]:checked`).length > 0) {
                        // Increment hanya sekali per group
                        if (!$(this).data('counted')) {
                            filledGroups++;
                            $(`input[name="${name}"]`).data('counted', true);
                        }
                    }
                });

                // Reset counter untuk next check
                tabContent.find('input[type="radio"]').data('counted', false);

                // Update status dot
                const navLink = $(`#tab-participant-${participantId}`);
                if (filledGroups === totalRadioGroups) {
                    navLink.addClass('filled');
                } else {
                    navLink.removeClass('filled');
                }

                return filledGroups === totalRadioGroups;
            }

            // ← FUNCTION: Update progress counter
            function updateProgress() {
                let filledCount = 0;
                $('.nav-link').each(function() {
                    if ($(this).hasClass('filled')) {
                        filledCount++;
                    }
                });
                $('#filledCount').text(filledCount);
            }

            // ← EVENT: Ketika radio button di-click
            $('.evaluation-radio').on('change', function() {
                const participantId = $(this).data('participant-id');
                checkParticipantCompletion(participantId);
                updateProgress();
            });

            // ← EVENT: Form Submit
            $('#formEvaluationBulk').on('submit', function(e) {
                e.preventDefault();

                // Validasi: Cek apakah semua peserta sudah dinilai
                const totalParticipants = {{ $totalPeserta }};
                const filledParticipants = $('.nav-link.filled').length;

                if (filledParticipants < totalParticipants) {
                    Swal.fire({
                        title: 'Evaluasi Belum Lengkap',
                        html: `
                            <p>Hanya <strong>${filledParticipants}</strong> dari <strong>${totalParticipants}</strong> peserta yang sudah dinilai.</p>
                            <p class="text-danger">Harap lengkapi penilaian untuk semua peserta.</p>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#0891B2',
                        confirmButtonText: 'Lengkapi Penilaian'
                    });
                    return;
                }

                // Konfirmasi sebelum submit
                Swal.fire({
                    title: 'Konfirmasi Simpan',
                    html: `
                        <p>Anda akan menyimpan evaluasi untuk <strong>${totalParticipants} peserta</strong>.</p>
                        <p>Pastikan semua penilaian sudah benar.</p>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0891B2',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Simpan!',
                    cancelButtonText: 'Cek Lagi'
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitEvaluations();
                    }
                });
            });

            // ← FUNCTION: Submit via AJAX
            function submitEvaluations() {
                const formData = $('#formEvaluationBulk').serialize();
                const btnSubmit = $('#btnSubmit');

                // Disable button & show loading
                btnSubmit.prop('disabled', true);
                btnSubmit.html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu, sedang menyimpan evaluasi',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: $('#formEvaluationBulk').attr('action'),
                    method: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status) {
                            Swal.fire({
                                title: 'Berhasil!',
                                html: `
                                    <div class="text-start">
                                        <p>${response.message}</p>
                                        <hr>
                                        <p><strong>Total Evaluasi:</strong> ${response.data.total_saved}</p>
                                        <p><strong>Event:</strong> ${response.data.event_name}</p>
                                    </div>
                                `,
                                icon: 'success',
                                confirmButtonColor: '#0891B2',
                                allowOutsideClick: false
                            }).then(() => {
                                // Redirect atau reload
                                window.location.href = response.redirect_url || '{{ url("/evaluation") }}';
                            });
                        } else {
                            Swal.fire({
                                title: 'Gagal!',
                                text: response.message,
                                icon: 'error',
                                confirmButtonColor: '#dc3545'
                            });
                            
                            // Re-enable button
                            btnSubmit.prop('disabled', false);
                            btnSubmit.html('<i class="ti ti-device-floppy me-2"></i> Simpan Semua Evaluasi');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                        
                        if (xhr.responseJSON) {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                            
                            if (xhr.responseJSON.errors) {
                                let errorsList = '<ul class="text-start">';
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    errorsList += `<li>${value[0]}</li>`;
                                });
                                errorsList += '</ul>';
                                errorMessage += errorsList;
                            }
                        }

                        Swal.fire({
                            title: 'Error!',
                            html: errorMessage,
                            icon: 'error',
                            confirmButtonColor: '#dc3545'
                        });

                        // Re-enable button
                        btnSubmit.prop('disabled', false);
                        btnSubmit.html('<i class="ti ti-device-floppy me-2"></i> Simpan Semua Evaluasi');
                    }
                });
            }

            // Initial check saat load
            $('.evaluation-radio').each(function() {
                const participantId = $(this).data('participant-id');
                checkParticipantCompletion(participantId);
            });
            updateProgress();
        });
    </script>
</body>
</html>