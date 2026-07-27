/**
 * initDataTable.js
 * Inisialisasi DataTable berbasis fetch dari data-url.
 * Mendukung: create, edit, delete, show — semua via AJAX tanpa reload halaman.
 */

/* ── Global: destroy semua DataTable aktif lalu reinit ─────────────────── */
window.reloadDataTable = function () {
    document.querySelectorAll('table[data-url]').forEach(function (tbl) {
        try {
            if ($.fn && $.fn.DataTable && $.fn.DataTable.isDataTable(tbl)) {
                $(tbl).DataTable().destroy();
            }
        } catch (e) { /* ignore */ }
    });

    setTimeout(initReportTable, 150);
};

/* ── Init semua table[data-url] yang belum diinisialisasi ──────────────── */
function initReportTable() {

    document.querySelectorAll("table[data-url]").forEach((table) => {

        const tableId = "#" + table.id;
        const url     = table.dataset.url;
        const actions = table.dataset.actions
            ? JSON.parse(table.dataset.actions)
            : [];

        if (!url) return;

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
                        .map((column) => `<th>${column.title}</th>`)
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
                                            const safeUrl = (action.url ?? '')
                                                .replace(/'/g, "\\'");
                                            const safeModal = (action.modal ?? '')
                                                .replace(/'/g, "\\'");
                                            const safeRow = JSON.stringify(row)
                                                .replace(/"/g, '&quot;');

                                            return `
                                                <button
                                                    type="button"
                                                    class="${action.class}"
                                                    onclick="handleAction(
                                                        '${key}',
                                                        ${safeRow},
                                                        '${safeUrl}',
                                                        '${safeModal}'
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
                                        return `<span class="badge bg-light-success">Active</span>`;
                                    }
                                    return `<span class="badge bg-light-danger">Inactive</span>`;
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

// Saat halaman selesai dimuat
window.addEventListener("load", function () {
    initReportTable();
});

// Saat pindah tab Bootstrap
document.querySelectorAll('[data-bs-toggle="tab"]').forEach((tab) => {
    tab.addEventListener("shown.bs.tab", function () {
        setTimeout(function () {
            initReportTable();
        }, 300);
    });
});

/* ══════════════════════════════════════════════════════════════════════════
    handleAction — dispatcher untuk semua action button di DataTable
   ══════════════════════════════════════════════════════════════════════════ */
function handleAction(action, row, url, modal)
{
    switch (action) {

        case 'show':
            showData(row, url, modal);
            break;

        case 'edit':
            editData(row, url, modal);
            break;

        case 'delete':
            deleteData(row.id, url);
            break;

    }
}


/* ══════════════════════════════════════════════════════════════════════════
    showData — buka modal view (PDF viewer dll.), tidak ada form filling
   ══════════════════════════════════════════════════════════════════════════ */
function showData(row, id, url, modalId)
{
    const pdfUrl = '/storage/jobdesc/section/' + row.file + '#toolbar=0&navpanes=0';
    document.getElementById('jobdescPdfViewer').src = pdfUrl;
    $('#jobdesc-show').modal('show');
}


/* ══════════════════════════════════════════════════════════════════════════
    editData — fetch data lalu buka modal CRUD dalam mode edit
   ══════════════════════════════════════════════════════════════════════════ */
function editData(id, url, modalId)
{
    if (typeof window.openCrudModal !== 'function') {
        console.error('[editData] openCrudModal tidak ditemukan!');
        Swal.fire({ icon: 'error', title: 'Error', text: 'Modal belum siap.' });
        return;
    }

    // Tampilkan loading cursor
    document.body.style.cursor = 'wait';

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    fetch(url + '/' + id, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN'     : csrfToken,
            'X-Requested-With' : 'XMLHttpRequest',
            'Accept'           : 'application/json',
        }
    })
    .then(function (res) {
        if (!res.ok) throw new Error('HTTP ' + res.status);
        return res.json();
    })
    .then(function (data) {
        window.openCrudModal('#' + modalId, {
            mode : 'edit',
            id   : id,
            data : data,
        });
    })
    .catch(function (err) {
        console.error('[editData] fetch gagal:', err);
        Swal.fire({
            icon : 'error',
            title: 'Gagal memuat data!',
            text : 'Tidak dapat mengambil data dari server.',
        });
    })
    .finally(function () {
        document.body.style.cursor = '';
    });
}


/* ══════════════════════════════════════════════════════════════════════════
    deleteData — konfirmasi SweetAlert lalu hapus via fetch DELETE
                Tidak ada reload halaman — hanya reinit DataTable
   ══════════════════════════════════════════════════════════════════════════ */
function deleteData(id, url)
{
    Swal.fire({
        title            : 'Yakin ingin menghapus?',
        text             : 'Data yang sudah dihapus tidak dapat dikembalikan.',
        icon             : 'warning',
        showCancelButton : true,
        confirmButtonColor : '#d33',
        cancelButtonColor  : '#6c757d',
        confirmButtonText  : 'Ya, hapus!',
        cancelButtonText   : 'Batal',
    })
    .then(function (result) {
        if (!result.isConfirmed) return;

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        fetch(url + '/' + id, {
            method : 'DELETE',
            headers: {
                'X-CSRF-TOKEN'     : csrfToken,
                'X-Requested-With' : 'XMLHttpRequest',
                'Accept'           : 'application/json',
            },
        })
        .then(function (res) {
            return res.json().then(function (body) {
                return { ok: res.ok, body: body };
            });
        })
        .then(function (result) {
            if (result.ok) {
                Swal.fire({
                    icon             : 'success',
                    title            : 'Berhasil dihapus!',
                    text             : result.body.message || 'Data berhasil dihapus.',
                    timer            : 1800,
                    showConfirmButton : false,
                    timerProgressBar : true,
                });

                // Reinit DataTable tanpa reload halaman
                window.reloadDataTable();

            } else {
                Swal.fire({
                    icon : 'error',
                    title: 'Gagal menghapus!',
                    text : result.body.message || 'Terjadi kesalahan saat menghapus data.',
                });
            }
        })
        .catch(function (err) {
            console.error('[deleteData] fetch error:', err);
            Swal.fire({
                icon : 'error',
                title: 'Gagal menghapus!',
                text : 'Terjadi kesalahan koneksi ke server.',
            });
        });
    });
}