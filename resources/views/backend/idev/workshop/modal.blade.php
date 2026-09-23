<style>
    .workshop-item {
            transition: all 0.3s ease;
        }
        .workshop-item.removing {
            opacity: 0;
            transform: translateX(20px);
        }
        .add-btn-wrapper {
            margin-top: 15px;
            border-top: 1px dashed #ccc;
            padding-top: 15px;
        }
</style>

<!-- Modal Tambah Workshop -->
<div class="modal fade" id="addWorkshopModal" tabindex="-1" aria-labelledby="addWorkshopModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h3 class="modal-title text-white" id="addWorkshopModalLabel">
                    <i class="ti ti-clipboard-list me-2"></i> Tambah Workshop
                </h3>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="workshopForm">
                    <!-- Container untuk input yang berulang (repeatable) -->
                    <div id="workshopInputsContainer">
                        <!-- Input Pertama (Default, tidak bisa dihapus) -->
                        <div class="row mb-3 workshop-item">
                            <div class="col-md-11 col-10">
                                <div class="form-floating">
                                    <input type="text" class="form-control workshop-input" name="workshop_name[]"
                                        placeholder="Nama Workshop" required>
                                    <label>Nama Workshop 1</label>
                                </div>
                            </div>
                            <!-- Kolom kosong agar sejajar dengan input tambahan yang memiliki tombol hapus -->
                            <div class="col-md-1 col-2 d-flex align-items-center">
                                <!-- Tombol hapus tidak ditampilkan di input pertama -->
                            </div>
                        </div>
                    </div>

                    <div class="add-btn-wrapper text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addMoreBtn">
                            <i class="ti ti-plus me-1"></i> Tambah Baris Workshop
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="saveWorkshopBtn">
                    <i class="ti ti-save me-1"></i> Simpan
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script Kustom untuk logika Repeatable Form -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('workshopInputsContainer');
        const addBtn = document.getElementById('addMoreBtn');
        const saveBtn = document.getElementById('saveWorkshopBtn');
        const form = document.getElementById('workshopForm');
        let inputCount = 1;

        addBtn.addEventListener('click', function() {
            inputCount++;

            // Membuat elemen div baru untuk baris input
            const newRow = document.createElement('div');
            newRow.className = 'row mb-3 workshop-item';

            // Struktur HTML untuk baris baru
            newRow.innerHTML = `
                    <div class="col-md-11 col-10">
                        <div class="form-floating">
                            <input type="text" class="form-control workshop-input" name="workshop_name[]" placeholder="Nama Workshop" required>
                            <label>Nama Workshop ${inputCount}</label>
                        </div>
                    </div>
                    <div class="col-md-1 col-2 d-flex align-items-center">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-btn" title="Hapus baris ini">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                `;

            // Menambahkan baris baru ke dalam container
            container.appendChild(newRow);

            // Fokus otomatis ke input yang baru dibuat
            const newInput = newRow.querySelector('.workshop-input');
            newInput.focus();
        });

        // Menggunakan event delegation di container karena elemen ditambahkan secara dinamis
        container.addEventListener('click', function(e) {
            // Mengecek apakah yang diklik adalah tombol hapus atau icon di dalamnya
            const removeBtn = e.target.closest('.remove-btn');

            if (removeBtn) {
                const rowToRemove = removeBtn.closest('.workshop-item');

                // Efek animasi sebelum menghapus
                rowToRemove.classList.add('removing');

                // Menunggu animasi selesai sebelum benar-benar menghapus elemen dari DOM
                setTimeout(() => {
                    rowToRemove.remove();
                    updateLabels();
                }, 300);
            }
        });

        function updateLabels() {
            const labels = container.querySelectorAll('label');
            labels.forEach((label, index) => {
                label.textContent = `Nama Workshop ${index + 1}`;
            });
            // Update counter based on remaining items
            inputCount = labels.length;
        }

        saveBtn.addEventListener('click', async function() {
            // Validasi form HTML5
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Mengumpulkan data dari semua input
            const inputs = container.querySelectorAll('.workshop-input');
            const newWorkshops = [];

            inputs.forEach(input => {
                const val = input.value.trim();
                if (val !== "") {
                    newWorkshops.push(val);
                }
            });

            if (newWorkshops.length > 0) {
                saveBtn.disabled = true;

                try {
                    const savedWorkshops = [];

                    for (const name of newWorkshops) {
                        const response = await fetch('{{ route('workshop.store-name') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ name })
                        });
                        const data = await response.json();

                        if (!response.ok || !data.status) {
                            throw new Error(data.message || 'Workshop gagal disimpan.');
                        }

                        savedWorkshops.push(data.data);
                    }

                    const modalElement = document.getElementById('addWorkshopModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement)
                        || bootstrap.Modal.getOrCreateInstance(modalElement);
                    modalInstance.hide();

                    // Bersihkan backdrop yang mungkin tertinggal setelah animasi penutupan.
                    setTimeout(() => {
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.removeProperty('padding-right');
                        document.body.style.removeProperty('overflow');
                    }, 350);

                    Swal.fire({
                        title: 'Berhasil!',
                        text: `${savedWorkshops.length} workshop berhasil disimpan ke database.`,
                        icon: 'success',
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'Tutup',
                        timer: 3000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.reload();
                    });

                    form.reset();
                    container.querySelectorAll('.workshop-item').forEach((item, index) => {
                        if (index > 0) item.remove();
                    });
                    inputCount = 1;
                } catch (error) {
                    Swal.fire({
                        title: 'Gagal!',
                        text: error.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                } finally {
                    saveBtn.disabled = false;
                }
            }
        });

        const myModalEl = document.getElementById('addWorkshopModal');
        myModalEl.addEventListener('hidden.bs.modal', function(event) {
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            document.body.style.removeProperty('overflow');
        });
    });
</script>
