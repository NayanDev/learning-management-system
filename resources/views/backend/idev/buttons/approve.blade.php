<div class="modal fade" tabindex="-1" role="dialog" id="modalApproval">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Training Approval</h5>
            </div>
            <div class="modal-body">
                <div class="my-2">
                    <p id="note"></p>
                </div>
                <form id="formApproval" class="m-t-20" method="post">
                    {{ csrf_field() }}
                    
                    <!-- Hidden input untuk ID -->
                    <input type="hidden" name="approval_id" id="approval_id">
                    <input type="hidden" value="{{ auth()->user()->id }}" name="approve_by" id="approve_by">

                    <div class="my-2">
                        <label for="apprej">Status</label>
                        
                        <select name="status" id="apprej" class="form-control">
                            <option value="">Pilih Opsi</option>
                            <option value="Approve">Approve</option>
                            <option value="Reject">Reject</option>
                        </select>
                    </div>

                    <div class="my-2">
                        <label for="approval_notes">Notes</label>
                        <textarea name="notes" id="approval_notes" cols="30" rows="4" class="form-control"></textarea>
                    </div>

                    <hr>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="actionApproval()">Ya</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">Tidak</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setApproval(response) {
        let $select = $('#apprej');

        $("#approval_id").val(response.id); // simpan ID ke hidden input
        
        if (response.status === "open" || response.status === "reject") {
            $select.html(`
            <option value="submit">submit</option>
        `);
        } else if(response.status === "submit") {
            $select.html(`
            <option value="approve">Approve</option>
            <option value="reject">Reject</option>
        `);
        } else if(response.status === "approve") {
            $select.html(`
            <option value="close">Approve</option>
            <option value="reject">Reject</option>
        `);
        } else if(response.status === "close") {
            $select.html(`
            <option value="close">data is close</option>
        `);
        } else {
            $select.html(`
            <option value="open">data not found</option>
        `);
        }

        if (response.notes === null) {
            $("#note").text("Note: data not found");
        } else {
            $("#note").text("Note: " + response.notes);
        }
        $("#modalApproval").modal("show");
    }

    function actionApproval() {
        let uriKey = "{{ $uri_key }}";
        var status = $("#apprej").val();
        var notes = $("#approval_notes").val();
        var approve_by = $("#approve_by").val();
        var token = $("input[name='_token']").val();
        var id = $("#approval_id").val(); // Ambil ID dari hidden input

        if (status === "") {
            Swal.fire({
            icon: 'warning',
            title: 'Oops...',
            text: 'Silakan pilih status terlebih dahulu.',
        });
        return;
        }

        $.ajax({
            url: "/" + uriKey + "/" + id, // ID dikirim via URL
            type: "POST",
            data: {
                _token: token,
                status: status,
                notes: notes,
                approve_by: approve_by,
            },
            success: function (response) {
                if (response.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sukses',
                        text: response.message || 'Sukses ' + status + ' data.'
                    });
                    $("#modalApproval").modal("hide");
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
</script>