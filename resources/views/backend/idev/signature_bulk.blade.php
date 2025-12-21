<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Update Tanda Tangan Massal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Tabler Icons CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F0F7F8;
            padding: 2rem;
        }
        .main-card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }
        .table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            text-transform: uppercase;
            background-color: #f8f9fa;
            vertical-align: middle;
        }
        .table tbody td {
            vertical-align: middle;
        }
        .signature-preview {
            width: 120px;
            height: 60px;
            border: 1px dashed #dee2e6;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            overflow: hidden;
        }
        .signature-preview img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        .signature-pad-wrapper {
            border: 2px dashed #dee2e6;
            border-radius: 0.5rem;
            background-color: #fff;
            height: 300px;
            width: 100%;
            position: relative;
            touch-action: none;
        }
        canvas {
            width: 100%;
            height: 100%;
            display: block;
            cursor: crosshair;
        }
        .status-indicator {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
        }
        .status-indicator.updated {
            background-color: #28a745;
        }
        .status-indicator.pending {
            background-color: #ffc107;
        }
    </style>
</head>
<body>

    <div class="container-fluid" style="max-width: 1400px;">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold mb-1">Manajemen Tanda Tangan Peserta</h3>
                <p class="text-muted mb-0">Update tanda tangan pengguna secara massal.</p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-secondary" id="btnClearLocalStorage">
                    <i class="ti ti-trash me-1"></i> Hapus Data Sementara
                </button>
                <button type="submit" form="bulkUpdateForm" class="btn btn-primary" id="btnSubmitAll">
                    <i class="ti ti-device-floppy me-1"></i> Simpan Semua Perubahan
                </button>
            </div>
        </div>

        <!-- Alert Info LocalStorage -->
        <div class="alert alert-info d-none" id="alertLocalStorage">
            <i class="ti ti-info-circle me-2"></i>
            <strong>Data tersimpan sementara!</strong> Ditemukan <span id="localStorageCount">0</span> tanda tangan yang belum tersimpan ke database.
            <button type="button" class="btn btn-sm btn-success ms-2" id="btnRestoreLocalStorage">
                <i class="ti ti-refresh me-1"></i> Pulihkan Data
            </button>
        </div>

        <div class="card main-card">
            <div class="card-body p-0">
                <!-- FORM UTAMA -->
                <form id="bulkUpdateForm" method="POST">
                    @csrf

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr>
                                    <th class="ps-4" style="width: 5%;">No</th>
                                    <th style="width: 20%;">Nama</th>
                                    <th style="width: 15%;">Divisi & Unit Kerja</th>
                                    <th style="width: 20%;">Tanda Tangan Saat Ini</th>
                                    <th style="width: 30%;">Update Tanda Tangan Baru</th>
                                    <th style="width: 10%; text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $index => $user)
                                    <tr data-user-id="{{ $user->id }}">
                                        <td class="ps-4">{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                                            <div class="small text-muted">{{ $user->email }}</div>
                                            <!-- ID User Hidden -->
                                            <input type="hidden" name="users[{{ $index }}][id]" value="{{ $user->id }}">
                                        </td>
                                        <td>
                                            <div class="fw-medium">{{ $user->divisi ?? '-' }}</div>
                                            <span class="badge bg-info-subtle text-info-emphasis rounded-pill">
                                                {{ $user->unit_kerja ?? '-' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="signature-preview">
                                                @if($user->signature)
                                                    <img src="{{ asset('storage/signature/' . $user->signature) }}" 
                                                         alt="Current Signature">
                                                @else
                                                    <span class="text-muted small">Belum ada</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column gap-2">
                                                <!-- Tombol Gambar (Trigger Modal) -->
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-primary w-100"
                                                        onclick="openSignaturePad({{ $user->id }}, {{ $index }})">
                                                    <i class="ti ti-pencil me-1"></i> Gambar Tanda Tangan
                                                </button>

                                                <!-- Hidden Input untuk menampung Data Base64 dari Modal -->
                                                <input type="hidden" 
                                                       name="users[{{ $index }}][signature_base64]" 
                                                       id="sig_input_{{ $user->id }}"
                                                       data-user-id="{{ $user->id }}">

                                                <!-- Preview Kecil jika sudah gambar -->
                                                <div id="new_sig_preview_{{ $user->id }}"
                                                    class="d-none mt-1 p-2 bg-light border rounded text-center">
                                                    <small class="text-success d-block mb-1">
                                                        <i class="ti ti-check"></i> Tergambar
                                                    </small>
                                                    <img src="" style="height: 40px;" alt="New Signature">
                                                    <button type="button" 
                                                            class="btn btn-link btn-sm text-danger p-0 ms-2"
                                                            onclick="clearSignature({{ $user->id }})">
                                                        Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="status-indicator" id="status_{{ $user->id }}" 
                                                  title="{{ $user->signature ? 'Sudah ada' : 'Belum ada' }}">
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>

                <!-- Footer Info -->
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Total User: {{ $users->count() }} | 
                            Tanda Tangan Baru: <span id="newSignatureCount">0</span>
                        </small>
                        <small class="text-muted">
                            Terakhir disimpan: <span id="lastSavedTime">-</span>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MODAL SIGNATURE PAD -->
    <!-- ========================================== -->
    <div class="modal fade" id="signatureModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        Tanda Tangan untuk: <span id="modalUserName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-2">
                        <i class="ti ti-info-circle me-1"></i>
                        Silakan tanda tangan di dalam kotak di bawah ini. Gunakan mouse atau touchscreen.
                    </p>
                    <div class="signature-pad-wrapper" id="signaturePadWrapper">
                        <canvas id="signatureCanvas"></canvas>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-info">Format: SVG</span>
                            <span class="badge bg-secondary ms-1">Auto-save ke localStorage</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="clearCanvasBtn">
                            <i class="ti ti-eraser me-1"></i> Bersihkan
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveSignatureBtn">
                        <i class="ti ti-check me-1"></i> Gunakan Tanda Tangan
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Signature Pad -->
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // ========================================
        // GLOBAL VARIABLES
        // ========================================
        let activeUserId = null;
        let activeUserIndex = null;
        let signaturePad = null;
        const signatureModal = new bootstrap.Modal(document.getElementById('signatureModal'));
        const canvas = document.getElementById('signatureCanvas');
        const LOCALSTORAGE_KEY = 'bulk_signatures_backup';

        // ========================================
        // INITIALIZE
        // ========================================
        $(document).ready(function() {
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Signature Pad
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgba(255, 255, 255, 0)',
                penColor: 'rgba(11, 11, 170, 0.91)',
                minWidth: 1,
                maxWidth: 3
            });

            // Resize canvas saat modal dibuka
            $('#signatureModal').on('shown.bs.modal', resizeCanvas);

            // Auto-save ke localStorage setiap ada perubahan
            signaturePad.addEventListener('endStroke', () => {
                autoSaveToLocalStorage();
            });

            // Check localStorage saat load
            checkLocalStorage();

            // Update counter
            updateSignatureCounter();
        });

        // ========================================
        // CANVAS FUNCTIONS
        // ========================================
        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            
            // Restore dari localStorage jika ada
            const savedData = getLocalStorageData();
            if (savedData && savedData[activeUserId]) {
                signaturePad.fromDataURL(savedData[activeUserId]);
            }
        }

        function clearCanvas() {
            signaturePad.clear();
            autoSaveToLocalStorage();
        }

        $('#clearCanvasBtn').on('click', clearCanvas);

        // ========================================
        // MODAL FUNCTIONS
        // ========================================
        function openSignaturePad(userId, userIndex) {
            activeUserId = userId;
            activeUserIndex = userIndex;
            
            // Update modal title
            const userName = $(`tr[data-user-id="${userId}"]`).find('.fw-bold').text();
            $('#modalUserName').text(userName);
            
            signaturePad.clear();
            signatureModal.show();
        }

        // Save signature dari modal ke form
        $('#saveSignatureBtn').on('click', function() {
            if (signaturePad.isEmpty()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Ada Tanda Tangan',
                    text: 'Mohon tanda tangan terlebih dahulu.',
                    confirmButtonColor: '#0891B2'
                });
                return;
            }

            try {
                // ✅ Export sebagai SVG (format terbaik)
                const svgData = signaturePad.toDataURL('image/svg+xml');
                
                // Masukkan ke hidden input
                const hiddenInput = $(`#sig_input_${activeUserId}`);
                if (hiddenInput.length) {
                    hiddenInput.val(svgData);
                }

                // Tampilkan preview
                const previewContainer = $(`#new_sig_preview_${activeUserId}`);
                const previewImg = previewContainer.find('img');
                
                if (previewContainer.length && previewImg.length) {
                    // Untuk preview gunakan PNG
                    const pngData = signaturePad.toDataURL('image/png');
                    previewImg.attr('src', pngData);
                    previewContainer.removeClass('d-none');
                }

                // Update status indicator
                $(`#status_${activeUserId}`)
                    .removeClass('pending')
                    .addClass('updated');

                // Save ke localStorage
                saveToLocalStorage(activeUserId, svgData);

                // Update counter
                updateSignatureCounter();

                signatureModal.hide();

                // Show success notification
                const toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000
                });

                toast.fire({
                    icon: 'success',
                    title: 'Tanda tangan tersimpan!'
                });

            } catch (error) {
                console.error('Error saving signature:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal menyimpan tanda tangan: ' + error.message
                });
            }
        });

        // ========================================
        // CLEAR SIGNATURE
        // ========================================
        function clearSignature(userId) {
            $(`#sig_input_${userId}`).val('');
            $(`#new_sig_preview_${userId}`).addClass('d-none');
            $(`#status_${userId}`).removeClass('updated pending');
            
            // Remove dari localStorage
            removeFromLocalStorage(userId);
            
            updateSignatureCounter();
        }

        // ========================================
        // LOCALSTORAGE FUNCTIONS
        // ========================================
        function getLocalStorageData() {
            try {
                const data = localStorage.getItem(LOCALSTORAGE_KEY);
                return data ? JSON.parse(data) : {};
            } catch (e) {
                console.error('Error reading localStorage:', e);
                return {};
            }
        }

        function saveToLocalStorage(userId, signatureData) {
            try {
                const data = getLocalStorageData();
                data[userId] = signatureData;
                localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(data));
                
                // Update UI
                checkLocalStorage();
            } catch (e) {
                console.error('Error saving to localStorage:', e);
            }
        }

        function removeFromLocalStorage(userId) {
            try {
                const data = getLocalStorageData();
                delete data[userId];
                localStorage.setItem(LOCALSTORAGE_KEY, JSON.stringify(data));
                
                checkLocalStorage();
            } catch (e) {
                console.error('Error removing from localStorage:', e);
            }
        }

        function autoSaveToLocalStorage() {
            if (activeUserId && !signaturePad.isEmpty()) {
                const svgData = signaturePad.toDataURL('image/svg+xml');
                saveToLocalStorage(activeUserId, svgData);
            }
        }

        function checkLocalStorage() {
            const data = getLocalStorageData();
            const count = Object.keys(data).length;
            
            if (count > 0) {
                $('#alertLocalStorage').removeClass('d-none');
                $('#localStorageCount').text(count);
            } else {
                $('#alertLocalStorage').addClass('d-none');
            }
        }

        // Restore data dari localStorage
        $('#btnRestoreLocalStorage').on('click', function() {
            const data = getLocalStorageData();
            
            Object.keys(data).forEach(userId => {
                const signatureData = data[userId];
                const hiddenInput = $(`#sig_input_${userId}`);
                
                if (hiddenInput.length) {
                    hiddenInput.val(signatureData);
                    
                    // Show preview
                    const previewContainer = $(`#new_sig_preview_${userId}`);
                    const previewImg = previewContainer.find('img');
                    
                    if (previewContainer.length && previewImg.length) {
                        previewImg.attr('src', signatureData);
                        previewContainer.removeClass('d-none');
                    }
                    
                    // Update status
                    $(`#status_${userId}`).addClass('updated');
                }
            });
            
            updateSignatureCounter();
            
            Swal.fire({
                icon: 'success',
                title: 'Data Dipulihkan!',
                text: `Berhasil memulihkan ${Object.keys(data).length} tanda tangan.`,
                timer: 2000,
                showConfirmButton: false
            });
        });

        // Clear localStorage
        $('#btnClearLocalStorage').on('click', function() {
            Swal.fire({
                title: 'Hapus Data Sementara?',
                text: 'Tanda tangan yang belum disimpan ke database akan hilang.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    localStorage.removeItem(LOCALSTORAGE_KEY);
                    checkLocalStorage();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Terhapus!',
                        text: 'Data sementara telah dihapus.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // ========================================
        // UPDATE COUNTER
        // ========================================
        function updateSignatureCounter() {
            let count = 0;
            $('input[name*="signature_base64"]').each(function() {
                if ($(this).val()) {
                    count++;
                }
            });
            $('#newSignatureCount').text(count);
        }

        // ========================================
        // FORM SUBMIT
        // ========================================
        $('#bulkUpdateForm').on('submit', function(e) {
            e.preventDefault();

            const formData = $(this).serializeArray();
            const signatureCount = $('input[name*="signature_base64"]').filter(function() {
                return $(this).val();
            }).length;

            if (signatureCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Tidak Ada Perubahan',
                    text: 'Tidak ada tanda tangan baru yang perlu disimpan.',
                    confirmButtonColor: '#0891B2'
                });
                return;
            }

            // Konfirmasi
            Swal.fire({
                title: 'Simpan Perubahan?',
                html: `
                    <p>Anda akan menyimpan <strong>${signatureCount} tanda tangan</strong> ke database.</p>
                    <p class="text-muted small">Proses ini tidak dapat dibatalkan.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0891B2',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    submitForm();
                }
            });
        });

        function submitForm() {
            const formData = $('#bulkUpdateForm').serialize();
            const btnSubmit = $('#btnSubmitAll');

            // Disable button & show loading
            btnSubmit.prop('disabled', true);
            btnSubmit.html('<span class="spinner-border spinner-border-sm me-2"></span> Menyimpan...');

            Swal.fire({
                title: 'Memproses...',
                html: 'Mohon tunggu, sedang menyimpan tanda tangan ke server dan database.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route("users.bulk.update.signatures") }}',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        // Hapus localStorage setelah berhasil save
                        localStorage.removeItem(LOCALSTORAGE_KEY);
                        
                        // Update last saved time
                        const now = new Date();
                        $('#lastSavedTime').text(now.toLocaleTimeString('id-ID'));
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            html: `
                                <div class="text-start">
                                    <p>${response.message}</p>
                                    <hr>
                                    <p><strong>Total disimpan:</strong> ${response.data.updated_count}</p>
                                    ${response.data.errors.length > 0 ? 
                                        `<p class="text-danger"><strong>Error:</strong> ${response.data.total_errors}</p>` : 
                                        ''}
                                </div>
                            `,
                            confirmButtonColor: '#0891B2',
                            allowOutsideClick: false
                        }).then(() => {
                            // Reload page
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: response.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menyimpan.';
                    
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
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });

                    console.error('AJAX Error:', xhr);
                },
                complete: function() {
                    // Re-enable button
                    btnSubmit.prop('disabled', false);
                    btnSubmit.html('<i class="ti ti-device-floppy me-1"></i> Simpan Semua Perubahan');
                }
            });
        }
    </script>
</body>
</html>