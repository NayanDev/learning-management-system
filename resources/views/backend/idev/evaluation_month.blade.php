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
            <div class="d-flex gap-2 align-items-center">
                <div class="legend-box">
                    <i class="ti ti-info-circle me-2 fs-5"></i>
                    <strong>Skala:</strong>&nbsp; 0-5(D) | 6-10(C) | 11-15(B) | 16-20(A)
                </div>
            </div>
        </div>

        <div class="card evaluation-card">
            
            <!-- Form Utama -->
            <form id="formEvaluationBulk" action="{{ route('submit.evaluation.month') }}" method="POST">
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

                                        {{-- Formulir Penilaian Kinerja --}}
                                        <div class="mb-4">
                                            <h6 class="fw-bold text-secondary mb-3 pb-2 border-bottom">
                                                <i class="ti ti-clipboard-check me-2"></i>Formulir Penilaian Kinerja
                                            </h6>

                                            {{-- Aspek Kinerja --}}
                                            <div class="card border mb-3">
                                                <div class="card-header bg-light py-2">
                                                    <small class="fw-semibold text-uppercase">Aspek Teknis Pekerjaan</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Efektivitas & Efisiensi Kerja</label>
                                                            <input type="number" name="evaluations[{{ $index }}][efektivitas_efisiensi]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Ketepatan Waktu Dalam Menyelesaikan Tugas</label>
                                                            <input type="number" name="evaluations[{{ $index }}][ketepatan_waktu]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Kemampuan Mencapai Target </label>
                                                            <input type="number" name="evaluations[{{ $index }}][kemampuan_target]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Tertib Administrasi</label>
                                                            <input type="number" name="evaluations[{{ $index }}][tertib_administrasi]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Inisiatif</label>
                                                            <input type="number" name="evaluations[{{ $index }}][inisiatif]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Kerjasama / Koordinasi Antar Bagian</label>
                                                            <input type="number" name="evaluations[{{ $index }}][kerjasama_koordinasi]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Aspek Perilaku --}}
                                            <div class="card border mb-3">
                                                <div class="card-header bg-light py-2">
                                                    <small class="fw-semibold text-uppercase">Aspek Kepribadian</small>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Perilaku</label>
                                                            <input type="number" name="evaluations[{{ $index }}][perilaku]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Kedisiplinan</label>
                                                            <input type="number" name="evaluations[{{ $index }}][kedisiplinan]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Tanggung Jawab & Loyalitas</label>
                                                            <input type="number" name="evaluations[{{ $index }}][tanggung_jawab_loyalitas]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label fw-medium">Ketaatan Terhadap Instruksi Kerja</label>
                                                            <input type="number" name="evaluations[{{ $index }}][ketaatan_instruksi]" class="form-control" min="0" max="20" placeholder="0-20">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            @php
                                                $pesertaPelatihan = App\Models\User::where('nik', $participant->nik)->first();
                                            @endphp

                                            {{-- Aspek Kepemimpinan --}}
                                            @if($pesertaPelatihan->is_leader)
                                                <div class="card border mb-3">
                                                    <div class="card-header bg-light py-2">
                                                        <small class="fw-semibold text-uppercase">Aspek Kepemimpinan</small>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-medium">Koordinasi Bawahan</label>
                                                                <input type="number" name="evaluations[{{ $index }}][koordinasi_bawahan]" class="form-control" min="0" max="20" placeholder="0-20">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-medium">Kontrol / Pengendalian Bawahan</label>
                                                                <input type="number" name="evaluations[{{ $index }}][kontrol_bawahan]" class="form-control" min="0" max="20" placeholder="0-20">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-medium">Evaluasi dan Pembinaan Bawahan</label>
                                                                <input type="number" name="evaluations[{{ $index }}][evaluasi_pembinaan]" class="form-control" min="0" max="20" placeholder="0-20">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-medium">Delegasi Tanggung Jawab dan Wewenang</label>
                                                                <input type="number" name="evaluations[{{ $index }}][delegasi_tanggung_jawab]" class="form-control" min="0" max="20" placeholder="0-20">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-medium">Kecepatan & Ketepatan Pengambilan Keputusan</label>
                                                                <input type="number" name="evaluations[{{ $index }}][kecepatan_keputusan]" class="form-control" min="0" max="20" placeholder="0-20">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>

                                    
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

            const eventPrefix = `evaluation_event_{{ $event->id }}`;

            function participantKey(participantId) {
                return `${eventPrefix}_participant_${participantId}`;
            }

            function activeKey() {
                return `${eventPrefix}_active`;
            }

            // ← FUNCTION: Cek apakah semua field di tab peserta sudah diisi
            function checkParticipantCompletion(participantId) {
                const tabContent = $(`#content-participant-${participantId}`);

                // Ambil semua input number dalam tab ini
                const numberInputs = tabContent.find('input[type="number"]');
                const totalInputs = numberInputs.length;
                let filledInputs = 0;

                // Cek setiap input apakah sudah diisi
                numberInputs.each(function() {
                    const value = $(this).val();
                    if (value !== '' && value !== null && value !== undefined) {
                        filledInputs++;
                    }
                });

                // Update status dot
                const navLink = $(`#tab-participant-${participantId}`);
                if (filledInputs === totalInputs && totalInputs > 0) {
                    navLink.addClass('filled');
                } else {
                    navLink.removeClass('filled');
                }

                return filledInputs === totalInputs && totalInputs > 0;
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

            // Save participant inputs to localStorage
            function saveParticipantToLocal(participantId) {
                const tabContent = $(`#content-participant-${participantId}`);
                if (!tabContent.length) return;

                const inputs = {};
                tabContent.find('input, select, textarea').each(function() {
                    const name = $(this).attr('name');
                    if (!name) return;
                    inputs[name] = $(this).val();
                });

                try {
                    localStorage.setItem(participantKey(participantId), JSON.stringify(inputs));
                } catch (e) {
                    console.warn('localStorage write failed', e);
                }
            }

            // Restore participant inputs from localStorage
            function restoreParticipantFromLocal(participantId) {
                const raw = localStorage.getItem(participantKey(participantId));
                if (!raw) return false;

                try {
                    const inputs = JSON.parse(raw);
                    const tabContent = $(`#content-participant-${participantId}`);
                    if (!tabContent.length) return false;

                    Object.keys(inputs).forEach(function(name) {
                        const el = tabContent.find(`[name="${name}"]`);
                        if (el.length) el.val(inputs[name]);
                    });
                    return true;
                } catch (e) {
                    console.warn('localStorage parse failed', e);
                    return false;
                }
            }

            // Save active participant id
            function saveActiveParticipant(participantId) {
                try {
                    localStorage.setItem(activeKey(), String(participantId));
                } catch (e) {
                    console.warn('localStorage write failed', e);
                }
            }

            // Restore active participant
            function restoreActiveParticipant() {
                const raw = localStorage.getItem(activeKey());
                if (!raw) return;
                const id = raw;
                const btn = $(`#tab-participant-${id}`);
                if (btn.length) {
                    // Use Bootstrap 5 show via click
                    btn.trigger('click');
                }
            }

            // Remove all localStorage keys for this event (participants + active)
            function clearEventLocalStorage() {
                try {
                    const keysToRemove = [];
                    for (let i = 0; i < localStorage.length; i++) {
                        const key = localStorage.key(i);
                        if (key && key.indexOf(eventPrefix) === 0) keysToRemove.push(key);
                    }
                    keysToRemove.forEach(k => localStorage.removeItem(k));
                } catch (e) {
                    console.warn('localStorage clear failed', e);
                }
            }

            // ← EVENT: Ketika input diubah (semua tipe)
            $(document).on('input change', '.tab-pane input, .tab-pane select, .tab-pane textarea', function() {
                const participantId = $(this).closest('.tab-pane').data('participant-id');
                if (participantId) {
                    checkParticipantCompletion(participantId);
                    updateProgress();
                    saveParticipantToLocal(participantId);
                }
            });

            // Track tab change (Bootstrap 5)
            $(document).on('shown.bs.tab', '#participantTabs button[data-bs-toggle="pill"]', function(e) {
                const participantId = $(e.target).data('participant-id');
                if (participantId) saveActiveParticipant(participantId);
            });

            // ← EVENT: Form Submit
            $('#formEvaluationBulk').on('submit', function(e) {
                e.preventDefault();

                // Validasi: Cek apakah ada peserta yang sudah dinilai
                const totalParticipants = {{ $totalPeserta }};
                const filledParticipants = $('.nav-link.filled').length;

                // Jika belum ada yang dinilai sama sekali
                if (filledParticipants === 0) {
                    Swal.fire({
                        title: 'Belum Ada Penilaian',
                        html: `
                            <p class="text-danger">Anda belum mengisi penilaian untuk satupun peserta.</p>
                            <p>Silakan isi minimal satu peserta terlebih dahulu.</p>
                        `,
                        icon: 'warning',
                        confirmButtonColor: '#0891B2',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                // Jika belum semua dinilai, berikan peringatan dengan opsi lanjut
                if (filledParticipants < totalParticipants) {
                    Swal.fire({
                        title: 'Evaluasi Belum Lengkap',
                        html: `
                            <p>Hanya <strong>${filledParticipants}</strong> dari <strong>${totalParticipants}</strong> peserta yang sudah dinilai lengkap.</p>
                            <p>Apakah Anda ingin menyimpan data yang sudah diisi?</p>
                        `,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#0891B2',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Simpan',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitEvaluations();
                        }
                    });
                } else {
                    // Semua sudah lengkap, konfirmasi normal
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
                }
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
                            // Clear localStorage related to this evaluation
                            clearEventLocalStorage();

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

            // Initial restore & check saat load
            $('.tab-pane').each(function() {
                const participantId = $(this).data('participant-id');
                if (participantId) {
                    // Try restore participant inputs
                    restoreParticipantFromLocal(participantId);
                    checkParticipantCompletion(participantId);
                }
            });
            updateProgress();
            // restore active tab if any
            restoreActiveParticipant();
        });
    </script>
</body>
</html>
