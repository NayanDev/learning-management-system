<div class="modal fade" tabindex="-1" role="dialog" id="modalApproval">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Approval</h5>
            </div>
            <div class="modal-body">
                <form id="formApproval" method="POST">
                    @csrf

                    <!-- ID data yang di-approve -->
                    <input type="hidden" name="approval_id" id="approval_id">

                    <!-- tipe modul -->
                    <input type="hidden" name="approval_type" id="approval_type" value="TrainingAnalyst">

                    <!-- user login (submitter) -->
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">

                    <!-- manager akan diisi saat approve -->
                    <input type="hidden" name="manager_id" value="{{ auth()->id() }}">

                    <!-- director flag -->
                    <input type="hidden" name="director" value="0">

                    <!-- tanggal otomatis -->
                    <input type="hidden" name="date" value="{{ now()->toDateString() }}">

                    <!-- NAME APPROVAL -->
                    <div class="mb-3">
                        <label class="form-label">Nama Approval</label>
                        <input type="text"
                            class="form-control"
                            name="name"
                            id="approval_name"
                            placeholder="Nama Approval"
                            readonly>
                    </div>

                    <!-- STATUS (TIDAK DIPILIH USER SEBENARNYA) -->
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="status" class="form-control" readonly>
                            <option value="submit">Submit</option>
                            <option value="approve">Approve</option>
                        </select>
                    </div>

                    <hr>

                    <button type="button"
                            class="btn btn-primary btn-sm"
                            onclick="actionApproval()">
                        Simpan
                    </button>

                    <button type="button"
                            class="btn btn-secondary btn-sm"
                            data-bs-dismiss="modal">
                        Batal
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setApproval(response) {

        $("#approval_id").val(response.id);
        $("#approval_type").val("TrainingAnalyst");
        $("#approval_name").val("Training Analyst");

        let $select = $("#status");

        switch (response.status) {

            case "open":
            case "reject":
            case null:
            case "":
                $select.html(`<option value="submit">Submit</option>`);
                $select.val("submit");
                break;

            case "submit":
                $select.html(`<option value="approve">Approve</option>`);
                $select.val("approve");
                break;

            case "approve":
                $select.html(`<option value="" disabled selected>Data sudah disetujui</option>`);
                break;

            default:
                $select.html(`<option value="submit">Status tidak dikenal</option>`);
        }

        $("#modalApproval").modal("show");
    }

    function actionApproval() {

        let uriKey = "{{ $uri_key }}";
        let status = $("#status").val();

        if (!status) {

            Swal.fire({
                icon: "warning",
                title: "Peringatan",
                text: "Silakan pilih status."
            });

            return;
        }

        $.ajax({

            url: "/" + uriKey + "/" + $("#approval_id").val(),

            type: "POST",

            data: {

                _token: $("input[name='_token']").val(),

                name: $("#approval_name").val(),

                approval_type: $("#approval_type").val(),

                approval_id: $("#approval_id").val(),

                user_id: $("input[name='user_id']").val(),

                manager_id: $("input[name='manager_id']").val(),

                director: $("input[name='director']").val(),

                status: status,

                date: $("input[name='date']").val()

            },

            beforeSend: function () {

                Swal.fire({
                    title: "Loading...",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

            },

            success: function (response) {

                Swal.close();

                if (response.status) {

                    Swal.fire({
                        icon: "success",
                        title: "Berhasil",
                        text: response.message
                    }).then(() => {
                        $("#modalApproval").modal("hide");
                        location.reload();
                    });

                } else {

                    Swal.fire({
                        icon: "error",
                        title: "Gagal",
                        text: response.message
                    });

                }

            },

            error: function (xhr) {

                Swal.close();

                let msg = "Terjadi kesalahan.";

                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors)
                        .map(e => e[0])
                        .join("<br>");
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: "error",
                    title: "Error",
                    html: msg
                });

            }

        });

    }
</script>