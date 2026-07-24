function initReportTable() {

    document.querySelectorAll("table[data-url]").forEach((table) => {

        const tableId = "#" + table.id;
        const url = table.dataset.url;
        const actions = table.dataset.actions
        ? JSON.parse(table.dataset.actions)
        : [];
        const buttons = table.dataset.buttons
        ? JSON.parse(table.dataset.buttons)
        : [];

        if (!url) {
            return;
        }

        // Jika sudah aktif, cukup resize
        if ($.fn.DataTable.isDataTable(tableId)) {
            $(tableId).DataTable().columns.adjust();
            return;
        }

        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("HTTP ERROR : " + response.status);
                }
                return response.json();
            })

            .then((result) => {
                const thead = table.querySelector("thead");
                thead.innerHTML = `
                    <tr>
                    ${result.columns
                        .map((column) => {
                            return `
                                <th>
                                    ${column.title}
                                </th>
                            `;
                        })
                        .join("")}
                    </tr>
                `;

                new DataTable(tableId, {
                    data: result.data,
                    columns: result.columns.map((column) => {
                        // NOMOR
                        if (column.type === "number") {
                            return {
                                data: null,
                                width: column.width ?? "5%",
                                className: "text-center",
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row, meta) {
                                    return meta.row + 1;
                                },
                            };
                        }

                        // ACTION
                        if (column.type === "action") {
                            return {
                                data: null,
                                width: column.width ?? "10%",
                                className: "text-center",
                                orderable: false,
                                searchable: false,
                                render: function (data, type, row) {
                                    return Object.keys(actions)
                                        .map(key => {
                                            const action = actions[key];
                                            return `
                                                <button
                                                    type="button"
                                                    class="${action.class}"
                                                    onclick="handleAction(
                                                        '${key}',
                                                        ${row.id},
                                                        '${action.url ?? ""}',
                                                        '${action.modal ?? ""}'
                                                    )">
                                                    <i class="${action.icon}"></i>
                                                </button>
                                            `;
                                        })
                                        .join("");
                                }
                            };
                        }

                        // STATUS
                        if (column.type === "status") {
                            return {
                                data: column.data,
                                render: function (data) {
                                    if (data == 1) {
                                        return `
                                        <span class="badge bg-light-success">
                                            Active
                                        </span>
                                        `;
                                    }

                                    return `
                                    <span class="badge bg-light-danger">
                                        Inactive
                                    </span>
                                    `;
                                },
                            };
                        }

                        // NORMAL COLUMN
                        return {
                            data: column.data,
                            width: column.width ?? "20%",
                        };
                    }),

                    autoWidth: false,
                    responsive: true,
                    pageLength: 10,
                    searching: true,
                    ordering: true,
                    paging: true,
                    info: true,
                    language: {
                        lengthMenu: "_MENU_"
                    },

                    layout: {
                        topStart: "pageLength",
                        topEnd: "search",
                        bottomStart: "info",
                        bottomEnd: "paging",
                    },
                });
            })

            .catch((error) => {
                console.error("LOAD TABLE ERROR:", error);
            });
    });
}

// Saat halaman selesai
window.addEventListener("load", function () {
    initReportTable();
});

// Saat pindah tab bootstrap

document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
    tab.addEventListener("shown.bs.tab", function () {
        console.log("TAB OPEN");

        setTimeout(function () {
            initReportTable();
        }, 300);
    });
});


function deleteData(id, url)
{
    Swal.fire({
        title: "Yakin ingin menghapus?",
        text: "Data yang sudah dihapus tidak dapat dikembalikan.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Hapus",
        cancelButtonText: "Batal"
    })
    .then((result)=>{
        if(result.isConfirmed){
            $.ajax({
                url: url + "/" + id,
                type: "DELETE",
                headers:{
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response){
                    Swal.fire({
                        icon:"success",
                        title:"Berhasil dihapus",
                        timer:1500,
                        showConfirmButton:false
                    });

                    // reload table
                    const table = $('#jobdesc-table').DataTable();
                    table.destroy();
                    initReportTable();
                },

                error:function(xhr){
                    console.error(xhr.responseText);

                    Swal.fire({
                        icon:"error",
                        title:"Delete failed"
                    });
                }
            });
        }

    });
}

function showData(id, url, modalId)
{
    $.get(url + "/" + id, function(response){
        Object.keys(response).forEach(function(key){
            const element = document.querySelector(
                "#" + modalId + " [name='" + key + "']"
            );

            if(element){
                element.value = response[key];
            }
        });

        const modal = new bootstrap.Modal(
            document.getElementById(modalId)
        );
        modal.show();
    });
}


function handleAction(action, id, url, modal)
{
    switch (action) {

        case "show":
            showData(id, url, modal);
            break;

        case "edit":
            editData(id, url, modal);
            break;

        case "delete":
            deleteData(id, url);
            break;
    }
}
