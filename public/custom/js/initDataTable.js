function initReportTable() {

    document.querySelectorAll("table[data-url]").forEach((table) => {
        const tableId = "#" + table.id;
        const url = table.dataset.url;

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
                                    return `

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light-success me-1"
                                        onclick="editData(${row.id})">
                                        <i class="ti ti-edit"></i>
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-sm btn-light-danger"
                                        onclick="deleteData(${row.id})">
                                        <i class="ti ti-trash"></i>
                                    </button>

                                    `;
                                },
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
