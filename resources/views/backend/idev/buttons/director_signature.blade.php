<div class="modal fade" tabindex="-1" role="dialog" id="modalDirectorSignature">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Director Signature</h5>
            </div>
            <div class="modal-body">
                <form id="formDirectorSignature" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="approval_id" id="director_approval_id">
                    <input type="hidden" value="{{ auth()->user()->id }}" name="approve_by" id="director_approve_by">
                    <input type="hidden" name="status" id="director_status" value="close">

                    <div class="my-2">
                        <label class="form-label">Tanda Tangan</label>
                        <div class="border rounded bg-white p-2">
                            <canvas id="director-signature-pad" style="width:100%;height:220px;"></canvas>
                        </div>
                        <small class="text-muted">Tanda tangan wajib diisi untuk menyelesaikan approval director.</small>
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
        resizeCanvas();

        document.getElementById('btnClearDirectorSignature').addEventListener('click', function() {
            directorSignaturePad.clear();
        });
    }

    function setDirectorSignature(response) {
        initDirectorSignaturePad();
        $("#director_approval_id").val(response.id);
        $("#director_status").val("close");
        if (directorSignaturePad) {
            directorSignaturePad.clear();
        }
        $("#modalDirectorSignature").modal("show");
    }

    function actionDirectorSignature() {
        initDirectorSignaturePad();

        if (!directorSignaturePad || directorSignaturePad.isEmpty()) {
            Swal.fire({
                icon: 'warning',
                title: 'Oops...',
                text: 'Silakan isi tanda tangan terlebih dahulu.'
            });
            return;
        }

        let uriKey = "{{ $uri_key }}";
        var status = $("#director_status").val();
        var notes = '-';
        var approve_by = $("#director_approve_by").val();
        var token = $("input[name='_token']").val();
        var id = $("#director_approval_id").val();
        var signatureData = directorSignaturePad.toSVG();

        $.ajax({
            url: "/" + uriKey + "/" + id,
            type: "POST",
            data: {
                _token: token,
                status: status,
                notes: notes,
                approve_by: approve_by,
                director_signature: signatureData,
            },
            success: function (response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message || 'Sukses ' + status + ' data.'
                    });
                    $("#modalDirectorSignature").modal("hide");
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: response.message || 'Terjadi kesalahan saat memproses data.'
                    });
                }
            },
            error: function (xhr) {
                var errorMessage = 'Terjadi kesalahan. Silakan coba lagi.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: errorMessage
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initDirectorSignaturePad();
    });
</script>