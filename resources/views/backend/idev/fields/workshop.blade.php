@php
    $prefix_repeatable = isset($repeatable) ? true : false;
    $select_id = isset($field['name']) ? $field['name'] : 'id_' . $key;
    $select_name = isset($field['name']) ? $field['name'] : 'name_' . $key;
    $preffix_method = isset($method) ? $method . '_' : '';
@endphp
<div class="{{ isset($field['class']) ? $field['class'] : 'form-group' }}">
    <label>{{ isset($field['label']) ? $field['label'] : 'Label ' . $key }}
        @if (isset($field['required']) && $field['required'])
            <small class="text-danger">*</small>
        @endif
    </label>
    <select id="{{ $preffix_method }}{{ $select_id }}" name="{{ $select_name }}"
        class="form-control idev-form support-live-select2 @if ($prefix_repeatable) field-repeatable @endif">
        @foreach ($field['options'] as $key => $opt)
            <option value="{{ $opt['value'] }}" @if ($opt['value'] == $field['value'] || $opt['value'] == request($select_name)) selected @endif>{{ $opt['text'] }}
            </option>
        @endforeach
    </select>

    <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#addWorkshopModal">
        +
    </button>

    <!-- Modal Tambah Workshop -->
    <div class="modal fade" id="addWorkshopModal" tabindex="-1" aria-labelledby="addWorkshopModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addWorkshopModalLabel">
                        <i class="fas fa-clipboard-list me-2"></i> Tambah Workshop
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <form id="workshopForm">
                        <p class="text-muted mb-4">Masukkan nama-nama workshop. Anda bisa menambahkan beberapa sekaligus.</p>
                        
                        <!-- Container untuk input yang berulang (repeatable) -->
                        <div id="workshopInputsContainer">
                            <!-- Input Pertama (Default, tidak bisa dihapus) -->
                            <div class="row mb-3 workshop-item">
                                <div class="col-md-11 col-10">
                                    <div class="form-floating">
                                        <input type="text" class="form-control workshop-input" name="workshop_name[]" placeholder="Nama Workshop" required>
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
                                <i class="fas fa-plus me-1"></i> Tambah Baris Workshop
                            </button>
                        </div>
                    </form>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="saveWorkshopBtn">
                        <i class="fas fa-save me-1"></i> Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Bootstrap 5 JS Bundle dengan Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Script Kustom untuk logika Repeatable Form -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('workshopInputsContainer');
            const addBtn = document.getElementById('addMoreBtn');
            const saveBtn = document.getElementById('saveWorkshopBtn');
            const form = document.getElementById('workshopForm');
            const savedList = document.getElementById('savedWorkshopsList');
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
                            <i class="fas fa-times"></i>
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

            saveBtn.addEventListener('click', function() {
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
                    // Hapus pesan "Belum ada workshop" jika ada
                    const emptyMessage = savedList.querySelector('.text-muted.fst-italic');
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }

                    // Tambahkan list item baru untuk setiap workshop
                    newWorkshops.forEach(workshop => {
                        const li = document.createElement('li');
                        li.className = 'list-group-item d-flex justify-content-between align-items-center';
                        li.innerHTML = `
                            <span><i class="fas fa-check text-success me-2"></i> ${workshop}</span>
                            <span class="badge bg-primary rounded-pill">Baru</span>
                        `;
                        savedList.appendChild(li);
                    });

                    // Menutup modal menggunakan API Bootstrap
                    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('addWorkshopModal'));
                    modalInstance.hide();

                    Swal.fire({
                        title: 'Berhasil!',
                        text: `${newWorkshops.length} workshop telah berhasil ditambahkan.`,
                        icon: 'success',
                        confirmButtonColor: '#0d6efd',
                        confirmButtonText: 'Tutup',
                        timer: 3000,
                        timerProgressBar: true
                    });

                    // Reset form: hapus semua input tambahan dan kosongkan input pertama
                    setTimeout(() => {
                        const items = container.querySelectorAll('.workshop-item');
                        items.forEach((item, index) => {
                            if (index === 0) {
                                // Kosongkan input pertama
                                item.querySelector('input').value = '';
                            } else {
                                // Hapus input tambahan
                                item.remove();
                            }
                        });
                        inputCount = 1;
                    }, 500); // Tunggu sampai animasi modal selesai
                }
            });
            
            const myModalEl = document.getElementById('addWorkshopModal');
            myModalEl.addEventListener('hidden.bs.modal', function (event) {
                 // Opsional: Anda bisa mereset form di sini juga jika ingin
                 // form selalu kembali ke 1 input kosong saat modal dibuka lagi
            });
        });
    </script>

@if (isset($field['filter']))
    @push('scripts')
        <script>
            var currentUrl = "{{ url()->current() }}"

            $('#{{ $select_id }}').on('change', function() {
                if (currentUrl.includes("?")) {
                    currentUrl += "&{{ $select_name }}=" + $(this).val()
                } else {
                    currentUrl += "?{{ $select_name }}=" + $(this).val()
                }
                window.location.replace(currentUrl);
            })
        </script>
    @endpush
@endif
