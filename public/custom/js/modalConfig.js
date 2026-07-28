function openJobdescModal() {
    $("#jobdescForm")[0].reset();

    $("#jobdesc_id").val("");
    $("#jobdesc_old_file").val("");

    $("#jobdesc_file_info")
        .removeClass("text-primary")
        .addClass("text-muted")
        .text("Kosongkan jika tidak ingin mengganti file.");

    $("#jobdescModal .modal-title").text("Tambah Jobdesc");

    $("#jobdescModal").modal("show");
}

// dipanggil oleh handleAction untuk edit
function editData(row, url, modal) {
    editJobdesc(row);
}

// isi modal edit
function editJobdesc(data) {
    $("#jobdesc_id").val(data.id);
    $("#jobdesc_old_file").val(data.file ?? "");

    $('#jobdescForm input[name="name"]').val(data.name);
    $('#jobdescForm select[name="is_active"]').val(data.is_active);

    // Ubah informasi file
    if (data.file) {
        $("#jobdesc_file_info")
            .removeClass("text-muted")
            .addClass("text-muted")
            .text("File saat ini: " + data.file);
    } else {
        $("#jobdesc_file_info")
            .removeClass("text-primary")
            .addClass("text-muted")
            .text("Kosongkan jika tidak ingin mengganti file.");
    }

    $("#jobdescModal .modal-title").text("Edit Jobdesc");
    $("#jobdescModal").modal("show");
}

// submit create + update
$(document).on("submit", "#jobdescForm", function (e) {
    e.preventDefault();

    let id = $("#jobdesc_id").val();
    let formData = new FormData(this);
    let form = this;
    let dataURLForm = form.dataset.url;
    let url = id ? dataURLForm + "/" + id : dataURLForm;

    // update menggunakan method PUT
    if (id) {
        formData.append("_method", "PUT");
    }

    fetch(url, {
        method: "POST",
        body: formData,
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((result) => {

            if (result.status) {
                Swal.fire({
                    icon: "success",
                    title: "Berhasil!",
                    text: result.message ?? "Data jobdesc berhasil disimpan.",
                    timer: 2000,
                    showConfirmButton: false,
                });

                $("#jobdescModal").modal("hide");

                $("#jobdescForm")[0].reset();

                $("#jobdesc_id").val("");
                $("#jobdesc_old_file").val("");

                if (typeof window.reloadDataTable === "function") {
                    window.reloadDataTable();
                }
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Gagal!",
                    text: result.message ?? "Data gagal disimpan.",
                    target: document.body,
                    backdrop: true,
                    allowOutsideClick: false,
                });
            }
        })
        .catch((error) => {
            console.error(error);

            Swal.fire({
                icon: "error",
                title: "Error!",
                text: "Terjadi kesalahan pada server.",
            });
        });
});
