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

    console.log($('#jobdescPdfViewer').length); // harus 1

    document.getElementById('jobdescPdfViewer').src = pdfUrl;

    console.log(document.getElementById('jobdescPdfViewer').src);

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


(function () {
    'use strict';

    var MODAL_ID  = '{{ $modalId }}';
    var SAVE_LABEL = '{{ $saveLabel }}';

    /* ── Helper: get element inside this modal ──────────────────────── */
    function $m(id) { return document.getElementById(MODAL_ID + '-' + id); }

    /* ══════════════════════════════════════════════════════════════════
       window.openCrudModal
       Called by initDataTable.js (edit action) and the Create button.
       ══════════════════════════════════════════════════════════════════ */
    window.openCrudModal = function (selector, options) {
        var modalEl = document.querySelector(selector);
        if (!modalEl) {
            console.warn('openCrudModal: element not found:', selector);
            return;
        }

        var formEl    = document.getElementById(modalEl.id + '-form');
        var methodEl  = document.getElementById(modalEl.id + '-_method');
        var secEl     = document.getElementById(modalEl.id + '-section_id');
        var titleEl   = modalEl.querySelector('.modal-title');
        var baseUrl   = modalEl.dataset.baseUrl   || '';
        var baseTitle = modalEl.dataset.baseTitle  || 'Form';
        var mode      = (options && options.mode)  || 'create';

        /* Reset form */
        formEl.reset();
        formEl.removeAttribute('data-submit-url'); // clear previous url

        /* Clear file hints */
        modalEl.querySelectorAll('[id$="-hint"]').forEach(function (el) {
            el.textContent = '';
        });

        /* ── CREATE mode ─────────────────────────────────────────────── */
        if (mode !== 'edit') {
            formEl.dataset.submitUrl = baseUrl;
            methodEl.value           = 'POST';
            titleEl.textContent      = (options && options.title) || ('Add ' + baseTitle);

            /* section_id from option or from nearest table[data-section-id] */
            if (secEl) {
                var sid = (options && options.sectionId) || '';
                if (!sid) {
                    var tbl = document.querySelector('table[data-section-id]');
                    if (tbl) sid = tbl.dataset.sectionId || '';
                }
                secEl.value = sid;
            }

        /* ── EDIT mode ───────────────────────────────────────────────── */
        } else {
            formEl.dataset.submitUrl = baseUrl + '/' + options.id;
            methodEl.value           = 'PUT';
            titleEl.textContent      = (options && options.title) || ('Edit ' + baseTitle);

            /* Pre-fill text / hidden fields */
            var data = options.data || {};
            Object.keys(data).forEach(function (key) {
                var input = formEl.querySelector('[name="' + key + '"]');
                if (input && input.type !== 'file') {
                    input.value = data[key] != null ? data[key] : '';
                }
            });

            /* Show current filename as hint below file input */
            if (data.file) {
                var parts    = data.file.split('/');
                var filename = parts[parts.length - 1];
                var hint = modalEl.querySelector('[id$="file-hint"]');
                if (hint) hint.textContent = 'Current file: ' + filename;
            }
        }

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    };

    /* ══════════════════════════════════════════════════════════════════
       DOMContentLoaded wiring
       ══════════════════════════════════════════════════════════════════ */
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById(MODAL_ID);
        if (!modalEl) return;

        var formEl    = document.getElementById(MODAL_ID + '-form');
        var submitBtn = document.getElementById(MODAL_ID + '-btn-save');
        var btnLabel  = document.getElementById(MODAL_ID + '-btn-label');

        /* ── Ajax form submit ────────────────────────────────────────── */
        formEl.addEventListener('submit', function (e) {
            e.preventDefault();

            var submitUrl = formEl.dataset.submitUrl || '';
            if (!submitUrl) {
                Swal.fire({ icon: 'error', title: 'Konfigurasi Error', text: 'Submit URL tidak ditemukan.' });
                return;
            }

            // Log debug info ke console
            var secInput = formEl.querySelector('[name="section_id"]');
            console.log('[CrudModal] Submit →', submitUrl);
            console.log('[CrudModal] section_id =', secInput ? secInput.value : 'input not found');
            console.log('[CrudModal] _method =', (formEl.querySelector('[name="_method"]') || {}).value);

            /* Disable button & show loading */
            if (submitBtn) { submitBtn.disabled = true; }
            if (btnLabel)  { btnLabel.textContent = 'Menyimpan…'; }

            var formData  = new FormData(formEl);
            var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = csrfMeta ? csrfMeta.content : '';

            fetch(submitUrl, {
                method : 'POST',          // always POST; _method field handles PUT spoofing
                body   : formData,
                headers: {
                    'X-CSRF-TOKEN'     : csrfToken,
                    'X-Requested-With' : 'XMLHttpRequest',
                    'Accept'           : 'application/json',
                }
            })
            .then(function (res) {
                console.log('[CrudModal] Response HTTP', res.status, res.url);
                return res.text().then(function (text) {
                    var body = {};
                    try { body = JSON.parse(text); } catch(e) {
                        console.error('[CrudModal] Response bukan JSON:', text.substring(0, 500));
                    }
                    return { ok: res.ok, status: res.status, body: body };
                });
            })
            .then(function (result) {
                console.log('[CrudModal] Response body:', result.body);

                if (result.ok && result.body.status !== false) {
                    /* ── SUCCESS ──────────────────────────────────────── */
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                    // Gunakan global reloadDataTable dari initDataTable.js
                    if (typeof window.reloadDataTable === 'function') {
                        window.reloadDataTable();
                    }

                    Swal.fire({
                        icon             : 'success',
                        title            : 'Berhasil!',
                        text             : result.body.message || 'Data berhasil disimpan.',
                        timer            : 2000,
                        showConfirmButton : false,
                        timerProgressBar : true,
                    });

                } else {
                    /* ── VALIDATION / SERVER ERROR ───────────────────── */
                    var errors = result.body.errors || {};
                    var lines  = Object.values(errors).flat();
                    var html   = lines.length
                        ? '<ul class="text-start mb-0">' + lines.map(function(l){ return '<li>' + l + '</li>'; }).join('') + '</ul>'
                        : (result.body.message || 'Terjadi kesalahan. Status: ' + result.status);

                    console.error('[CrudModal] Gagal:', result.status, errors);
                    Swal.fire({
                        icon : 'error',
                        title: 'Gagal menyimpan!',
                        html : html,
                    });
                }

            })
            .catch(function (err) {
                console.error('[CrudModal] fetch error:', err);
                Swal.fire({
                    icon : 'error',
                    title: 'Network Error',
                    text : err.message || 'Gagal terhubung ke server.',
                });
            })
            .finally(function () {
                if (submitBtn) { submitBtn.disabled = false; }
                if (btnLabel)  { btnLabel.textContent = SAVE_LABEL; }
            });
        });
    });

})();
