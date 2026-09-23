<div class="row g-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title bg-primary text-white rounded p-2 text-center">Peringatan: Update Tanda Tangan!</h4>
                <b></b>
                <p>
                    1. Akun anda belum memiliki tanda tangan digital. <br>
                    2. Klik tombol buat tanda tangan dibawah untuk membuat tanda tangan digital. <br>
                    3. Maka akan tampil form untuk membuat tanda tangan digital. <br>
                    4. Buat tanda tangan digital anda dengan mouse atau touchscreen. <br>
                    5. Klik tombol Ya untuk menyimpan tanda tangan. <br><br>

                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#signatureModal"> Buat Tanda Tangan </button> <br><br>

                    <div class="modal fade" tabindex="-1" role="dialog" id="signatureModal">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Create Signature</h5>
                                </div>
                                <div class="modal-body">
                                    <form id="formSignature" method="post">
                                        {{ csrf_field() }}
                                        <div class="my-2">
                                            <div class="border rounded bg-white p-2">
                                                <canvas id="director-signature-pad" style="width:100%;height:220px;"></canvas>
                                            </div>
                                        </div>

                                        <hr>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnClearDirectorSignature">Clear</button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="actionDirectorSignature()">Ya</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Tidak</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <b>Note: pastikan tanda tangan yang dibuat berukuran sedang (tidak
                        besar / kecil) posisi ditengah</b>
                    <br />
                    <b>Contoh pembuatan:</b> <br /><br />
                    <img src="{{ asset('img/signature-tutorial.png') }}" style="width: 100%" />
                </p>
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

        const button = document.querySelector('#signatureModal .btn-outline-primary');
        button.disabled = true;

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
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDirectorSignaturePad();
    });
</script>