<style>
    .signature-warning-card {
        border: none;
        border-radius: 20px;
        background: linear-gradient(135deg, #fff7ed 0%, #fff 60%);
        box-shadow: 0 4px 24px rgba(234, 88, 12, 0.08), 0 1px 4px rgba(0,0,0,0.04);
        overflow: hidden;
        position: relative;
    }

    .signature-warning-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        width: 5px; height: 100%;
        background: linear-gradient(180deg, #f97316, #ea580c);
        border-radius: 20px 0 0 20px;
    }

    .sig-warn-header {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 20px 28px 14px 32px;
        border-bottom: 1px solid #fde8d3;
    }

    .sig-warn-icon {
        width: 48px; height: 48px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
    }

    .sig-warn-icon i {
        font-size: 22px;
        color: #fff;
    }

    .sig-warn-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #c2410c;
        margin: 0;
        letter-spacing: 0.01em;
    }

    .sig-warn-subtitle {
        font-size: 0.82rem;
        color: #9a3412;
        margin: 3px 0 0 0;
        opacity: 0.75;
    }

    .sig-warn-body {
        padding: 20px 28px 24px 32px;
    }

    .sig-steps {
        list-style: none;
        margin: 0 0 22px 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .sig-step {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        font-size: 0.88rem;
        color: #374151;
    }

    .sig-step-num {
        width: 26px; height: 26px;
        background: #fff7ed;
        border: 1.5px solid #fed7aa;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        color: #ea580c;
        flex-shrink: 0;
        margin-top: 1px;
    }

    .btn-create-signature {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff !important;
        border: none;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        text-decoration: none;
        margin-bottom: 20px;
    }

    .btn-create-signature:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(249, 115, 22, 0.4);
    }

    .btn-create-signature:active {
        transform: translateY(0);
    }

    .sig-note-box {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }

    .sig-note-box i {
        color: #f97316;
        font-size: 16px;
        margin-top: 1px;
        flex-shrink: 0;
    }

    .sig-note-box p {
        margin: 0;
        font-size: 0.83rem;
        color: #7c3a12;
        line-height: 1.5;
    }

    .sig-tutorial-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 10px;
    }

    .sig-tutorial-img {
        width: 100%;
        border-radius: 12px;
        border: 1px solid #fde8d3;
        object-fit: contain;
        max-height: 220px;
    }

    /* Modal Styles */
    #signatureModal .modal-content {
        border: none;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    #signatureModal .modal-header {
        background: linear-gradient(135deg, #f97316, #ea580c);
        border: none;
        padding: 18px 24px;
    }

    #signatureModal .modal-title {
        color: #fff;
        font-weight: 700;
        font-size: 1rem;
    }

    #signatureModal .modal-body {
        padding: 20px 24px 24px;
    }

    .sig-canvas-wrapper {
        border: 2px dashed #fed7aa;
        border-radius: 14px;
        background: #fff;
        overflow: hidden;
        transition: border-color 0.2s;
    }

    .sig-canvas-wrapper:hover {
        border-color: #f97316;
    }

    #director-signature-pad {
        width: 100%;
        height: 200px;
        display: block;
        cursor: crosshair;
    }

    .sig-canvas-hint {
        text-align: center;
        font-size: 0.78rem;
        color: #9ca3af;
        margin-top: 8px;
        margin-bottom: 4px;
    }

    .sig-modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }

    .sig-modal-actions .btn {
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 8px 18px;
    }
</style>

<div class="row g-4 mb-2 pt-3">
    <div class="col-lg-12">
        <div class="card signature-warning-card">
            <div class="sig-warn-header">
                <div class="sig-warn-icon">
                    <i class="ti ti-signature"></i>
                </div>
                <div>
                    <h5 class="sig-warn-title">⚠️ Tanda Tangan Digital Belum Dibuat</h5>
                    <p class="sig-warn-subtitle">Lengkapi akun Anda dengan tanda tangan digital untuk mengaktifkan semua fitur.</p>
                </div>
            </div>

            <div class="sig-warn-body">
                <ul class="sig-steps">
                    <li class="sig-step">
                        <span class="sig-step-num">1</span>
                        <span>Akun Anda belum memiliki tanda tangan digital.</span>
                    </li>
                    <li class="sig-step">
                        <span class="sig-step-num">2</span>
                        <span>Klik tombol <strong>Buat Tanda Tangan</strong> di bawah untuk membuka form pembuatan.</span>
                    </li>
                    <li class="sig-step">
                        <span class="sig-step-num">3</span>
                        <span>Buat tanda tangan digital Anda menggunakan mouse atau touchscreen pada area yang tersedia.</span>
                    </li>
                    <li class="sig-step">
                        <span class="sig-step-num">4</span>
                        <span>Klik tombol <strong>Simpan</strong> untuk menyimpan tanda tangan Anda.</span>
                    </li>
                </ul>

                <button type="button" class="btn-create-signature" data-bs-toggle="modal" data-bs-target="#signatureModal">
                    <i class="ti ti-pencil"></i>
                    Buat Tanda Tangan
                </button>

                <div class="sig-note-box">
                    <i class="ti ti-info-circle"></i>
                    <p>Pastikan tanda tangan dibuat dengan ukuran <strong>sedang</strong> — tidak terlalu besar atau kecil — dan posisi <strong>di tengah</strong> area yang tersedia.</p>
                </div>

                <div class="sig-tutorial-label">Contoh Pembuatan Tanda Tangan</div>
                <img src="{{ asset('img/signature-tutorial.png') }}" class="sig-tutorial-img" alt="Contoh tanda tangan" />
            </div>
        </div>
    </div>
</div>

{{-- Signature Modal --}}
<div class="modal fade" tabindex="-1" role="dialog" id="signatureModal">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="ti ti-signature me-2"></i>Buat Tanda Tangan Digital
                </h5>
            </div>
            <div class="modal-body">
                <form id="formSignature" method="post">
                    {{ csrf_field() }}
                    <div class="sig-canvas-wrapper">
                        <canvas id="director-signature-pad"></canvas>
                    </div>
                    <p class="sig-canvas-hint">Gambar tanda tangan Anda di area atas menggunakan mouse atau jari.</p>

                    <div class="sig-modal-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="btnClearDirectorSignature">
                            <i class="ti ti-eraser me-1"></i>Hapus
                        </button>
                        <button type="button" class="btn btn-warning btn-sm ms-auto" onclick="actionDirectorSignature()">
                            <i class="ti ti-device-floppy me-1"></i>Simpan
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-dismiss="modal">
                            <i class="ti ti-x me-1"></i>Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>
    let directorSignaturePad = null;

    function initDirectorSignaturePad() {
        const canvas = document.getElementById('director-signature-pad');
        if (!canvas || directorSignaturePad) {
            return;
        }

        directorSignaturePad = new SignaturePad(canvas, {
            backgroundColor: 'rgb(255,255,255)',
            penColor: 'rgba(11, 11, 170, 0.91)'
        });

        const resizeCanvas = function() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            const data = directorSignaturePad.toData();

            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext('2d').scale(ratio, ratio);

            directorSignaturePad.clear();
            if (data.length > 0) {
                directorSignaturePad.fromData(data);
            }
        };

        window.addEventListener('resize', resizeCanvas);
        document.getElementById('signatureModal').addEventListener('shown.bs.modal', resizeCanvas);
        resizeCanvas();

        document.getElementById('btnClearDirectorSignature').addEventListener('click', function() {
            directorSignaturePad.clear();
        });
    }

    function actionDirectorSignature() {
        if (!directorSignaturePad || directorSignaturePad.isEmpty()) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Silakan isi tanda tangan terlebih dahulu.'
            });
            return;
        }

        const button = document.querySelector('#signatureModal .btn-warning');
        button.disabled = true;
        button.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i>Menyimpan...';

        fetch('{{ route('user.signature.update') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                signature_data: directorSignaturePad.toDataURL('image/svg+xml')
            })
        })
        .then(response => response.json().then(data => ({ ok: response.ok, data })))
        .then(({ ok, data }) => {
            if (!ok || !data.status) {
                throw new Error(data.message || 'Signature gagal disimpan.');
            }

            const modalElement = document.getElementById('signatureModal');
            const modalInstance = bootstrap.Modal.getInstance(modalElement)
                || bootstrap.Modal.getOrCreateInstance(modalElement);
            modalInstance.hide();

            Swal.fire({
                icon: 'success',
                title: 'Sukses',
                text: data.message
            }).then(() => window.location.reload());
        })
        .catch(error => {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: error.message
            });
            button.disabled = false;
            button.innerHTML = '<i class="ti ti-device-floppy me-1"></i>Simpan';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDirectorSignaturePad();
    });
</script>