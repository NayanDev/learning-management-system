<div class="col-lg-6 col-12">
    <style>
        .tna-clickable {
            cursor: pointer;
        }

        .tna-expired-item {
            cursor: pointer;
            position: relative;
        }

        .tna-popover {
            position: absolute;
            bottom: 100%;
            right: 0;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15);
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            display: none;
            z-index: 1050;
            gap: 0.5rem;
        }

        .tna-popover.show {
            display: flex;
        }

        .tna-popover::after {
            content: '';
            position: absolute;
            top: 100%;
            right: 0.75rem;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #dee2e6;
        }

        .tna-popover::before {
            content: '';
            position: absolute;
            top: calc(100% - 1px);
            right: 0.75rem;
            width: 0;
            height: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-top: 6px solid #fff;
        }

        .tna-modal[hidden] {
            display: none;
        }

        .tna-modal {
            position: fixed;
            inset: 0;
            z-index: 1060;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(0, 0, 0, .45);
        }

        .tna-modal-dialog {
            width: min(100%, 600px);
            background: #fff;
            border-radius: .5rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .2);
            display: flex;
            flex-direction: column;
            max-height: 90vh;
        }

        .tna-modal-body {
            overflow-y: auto;
            flex: 1;
        }

        .tna-modal-header {
            flex-shrink: 0;
            border-bottom: 1px solid #dee2e6;
        }

        .tna-modal-footer {
            flex-shrink: 0;
            border-top: 1px solid #dee2e6;
        }
    </style>
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti ti-clock me-2"></i>
                Training Need Analyst - {{ $selectedYear ?? now()->year }}
            </h5>
        </div>

        <div class="card-body">
            <!-- Tabs Nav -->
            <ul class="nav nav-tabs mb-3" id="tnaTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#upcoming" type="button" role="tab" aria-controls="upcoming" aria-selected="true">Upcoming<span class="badge bg-warning rounded-pill ms-1">{{ $trainingNeedAnalystUpcoming->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="not-completed-tab" data-bs-toggle="tab" data-bs-target="#not-completed" type="button" role="tab" aria-controls="not-completed" aria-selected="false">Expired<span class="badge bg-danger rounded-pill ms-1">{{ $trainingNeedAnalystExpired->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="used-tab" data-bs-toggle="tab" data-bs-target="#used" type="button" role="tab" aria-controls="used" aria-selected="false">Used<span class="badge bg-info rounded-pill ms-1">{{ $trainingNeedAnalystUsed->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completed" type="button" role="tab" aria-controls="completed" aria-selected="false">Completed<span class="badge bg-success rounded-pill ms-1">{{ $trainingNeedAnalystCompleted->count() }}</span></button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="tnaTabsContent">
                <!-- Upcoming Tab -->
                <div class="tab-pane fade show active" id="upcoming" role="tabpanel" aria-labelledby="upcoming-tab">
                    <div class="nearby-training-list">
                        @forelse($trainingNeedAnalystUpcoming as $training)
                        <div class="nearby-training-item tna-clickable d-flex justify-content-between align-items-start gap-3 mb-0"
                            data-tna-id="{{ $training->id }}" data-tna-name="{{ $training->workshop->name ?? '-' }}">
                            <div>
                                <div class="nearby-training-title fw-bold">
                                    {{ $training->workshop->name ?? '-' }}
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>{{ \Carbon\Carbon::parse($training->start_date)->format('d M Y') }}</span>
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-user"></i>
                                    <span>{{ $training->user->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-calendar-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Pelatihan Mendatang
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada jadwal pelatihan yang akan datang.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </div>
                    <div class="tab-pane fade" id="not-completed" role="tabpanel" aria-labelledby="not-completed-tab">
                    <div class="nearby-training-list">
                        @forelse($trainingNeedAnalystExpired as $training)
                        <div class="nearby-training-item tna-expired-item d-flex justify-content-between align-items-start gap-3 mb-0" 
                            data-tna-id="{{ $training->id }}" 
                            data-tna-name="{{ $training->workshop->name ?? '-' }}" 
                            data-tna-date="{{ \Carbon\Carbon::parse($training->start_date)->format('Y-m-d\TH:i') }}"
                            data-tna-enddate="{{ $training->end_date ? \Carbon\Carbon::parse($training->end_date)->format('Y-m-d\TH:i') : '' }}"
                            data-tna-divisi="{{ $training->divisi ?? '-' }}"
                            data-tna-instructor="{{ $training->instructor ?? '-' }}"
                            data-tna-position="{{ $training->position ?? '-' }}"
                            data-tna-status="{{ $training->implementation_status ?? 'used' }}"
                            data-tna-appstatus="{{ $training->application_status ?? '-' }}"
                            data-tna-user="{{ $training->user->name ?? '-' }}">
                            <div>
                                <div class="nearby-training-title fw-bold">
                                    {{ $training->workshop->name ?? '-' }}
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>{{ \Carbon\Carbon::parse($training->start_date)->format('d M Y') }}</span>
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-user"></i>
                                    <span>{{ $training->user->name ?? '-' }}</span>
                                </div>
                            </div>
                            <div class="tna-popover" role="tooltip">
                                <button type="button" class="btn btn-sm btn-primary tna-expired-action" title="Pindahkan ke Completed"><i class="ti ti-arrow-right"></i></button>
                            </div>
                        </div>
                        @empty
                            <div class="text-muted text-center py-4">Tidak ada training expired.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Used Tab -->
                <div class="tab-pane fade" id="used" role="tabpanel" aria-labelledby="used-tab">
                    <div class="nearby-training-list">
                        @forelse($trainingNeedAnalystUsed as $training)
                        <div class="nearby-training-item tna-clickable d-flex justify-content-between align-items-start gap-3 mb-0" data-tna-id="{{ $training->id }}" data-tna-name="{{ $training->workshop->name ?? '-' }}">
                            <div>
                                <div class="nearby-training-title fw-bold">
                                    {{ $training->workshop->name ?? '-' }}
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>{{ \Carbon\Carbon::parse($training->start_date)->format('d M Y') }}</span>
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-user"></i>
                                    <span>{{ $training->user->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-muted text-center py-4">Tidak ada data training.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                    <div class="nearby-training-list">
                        @forelse($trainingNeedAnalystCompleted as $training)
                        <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-0">
                            <div>
                                <div class="nearby-training-title fw-bold">
                                    {{ $training->workshop->name ?? '-' }}
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>{{ \Carbon\Carbon::parse($training->start_date)->format('d M Y') }}</span>
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-user"></i>
                                    <span>{{ $training->user->name ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                            <div class="text-muted text-center py-4">Tidak ada training completed.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tna-modal" id="tnaFormModal" hidden aria-hidden="true">
        <div class="tna-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="tnaFormTitle">
            <div class="tna-modal-header p-4 d-flex align-items-center justify-content-between">
                <h5 id="tnaFormTitle" class="mb-0">
                    <i class="ti ti-rocket me-2 text-primary"></i>Implementasi TNA
                </h5>
                <button type="button" class="btn-close" data-tna-close aria-label="Tutup"></button>
            </div>
            <div class="tna-modal-body p-4">
                <div class="alert alert-info d-flex align-items-center gap-2 py-2 mb-4">
                    <i class="ti ti-info-circle"></i>
                    <span>Data berikut akan digunakan untuk membuat <strong>Event Training</strong> baru.</span>
                </div>

                <form id="tnaExpiredForm">
                    {{-- Hidden fields --}}
                    <input type="hidden" id="tnaFormTnaId" name="tna_id">

                    {{-- Nama Pelatihan (readonly info) --}}
                    <div class="mb-3">
                        <label for="tnaFormWorkshopName" class="form-label fw-semibold">Nama Pelatihan</label>
                        <input type="text" class="form-control bg-light" id="tnaFormWorkshopName" name="workshop_name" readonly>
                    </div>

                    <div class="row g-3 mb-3">
                        {{-- Start Date --}}
                        <div class="col-md-6">
                            <label for="tnaFormStartDate" class="form-label fw-semibold">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" id="tnaFormStartDate" name="start_date" required>
                        </div>
                        {{-- End Date --}}
                        <div class="col-md-6">
                            <label for="tnaFormEndDate" class="form-label fw-semibold">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="datetime-local" class="form-control" id="tnaFormEndDate" name="end_date" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        {{-- Divisi --}}
                        <div class="col-md-6">
                            <label for="tnaFormDivisi" class="form-label fw-semibold">Divisi</label>
                            <input type="text" class="form-control bg-light" id="tnaFormDivisi" name="divisi" readonly>
                        </div>
                        {{-- User --}}
                        <div class="col-md-6">
                            <label for="tnaFormUser" class="form-label fw-semibold">Peserta (User)</label>
                            <input type="text" class="form-control bg-light" id="tnaFormUser" name="user_name" readonly>
                        </div>
                    </div>

                    {{-- Instructor --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Tipe Instruktur <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructor" id="instructorInternal" value="internal" required>
                                <label class="form-check-label" for="instructorInternal">
                                    <i class="ti ti-building me-1"></i> Internal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="instructor" id="instructorExternal" value="external">
                                <label class="form-check-label" for="instructorExternal">
                                    <i class="ti ti-world me-1"></i> External
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="mb-3">
                        <label for="tnaFormLocation" class="form-label fw-semibold">Lokasi</label>
                        <input type="text" class="form-control" id="tnaFormLocation" name="location" placeholder="Contoh: Ruang Rapat Lt.2">
                    </div>

                    {{-- Error display --}}
                    <div class="text-danger small mt-2" id="tnaFormError" hidden></div>
                </form>
            </div>
            <div class="tna-modal-footer p-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" data-tna-close>
                    <i class="ti ti-x me-1"></i>Batal
                </button>
                <button type="submit" form="tnaExpiredForm" class="btn btn-primary" id="tnaFormSubmitBtn">
                    <i class="ti ti-check me-1"></i>Simpan &amp; Buat Event
                </button>
            </div>
        </div>
    </div>

    <div class="tna-modal" id="tnaMoveModal" hidden aria-hidden="true">
        <div class="tna-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="tnaMoveTitle">
            <div class="p-4">
                <h5 id="tnaMoveTitle">Pindahkan Training</h5>
                <p class="mb-4">Pindahkan <strong id="tnaMoveName"></strong> ke Completed?</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-danger" data-tna-close>Batal</button>
                    <button type="button" class="btn btn-primary" id="tnaMoveButton">Pindah ke Completed</button>
                </div>
            </div>
        </div>
    </div>

    <div class="tna-modal" id="tnaConfirmModal" hidden aria-hidden="true">
        <div class="tna-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="tnaConfirmTitle">
            <div class="p-4">
                <h5 id="tnaConfirmTitle">Konfirmasi</h5>
                <p class="mb-4">Apakah Anda yakin ingin memindahkan data ini ke Completed?</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-danger" data-tna-close>Batal</button>
                    <button type="button" class="btn btn-success" id="tnaConfirmButton">Ya, pindahkan</button>
                </div>
                <div class="text-danger small mt-3" id="tnaConfirmError" hidden></div>
            </div>
        </div>
    </div>
</div>

    <script>
    (() => {
        const formModal    = document.getElementById('tnaFormModal');
        const moveModal    = document.getElementById('tnaMoveModal');
        const confirmModal = document.getElementById('tnaConfirmModal');
        const moveName     = document.getElementById('tnaMoveName');
        const confirmError = document.getElementById('tnaConfirmError');
        const formError    = document.getElementById('tnaFormError');
        const formSubmitBtn = document.getElementById('tnaFormSubmitBtn');

        // Form fields
        const formFields = {
            tnaId:        document.getElementById('tnaFormTnaId'),
            workshopName: document.getElementById('tnaFormWorkshopName'),
            startDate:    document.getElementById('tnaFormStartDate'),
            endDate:      document.getElementById('tnaFormEndDate'),
            divisi:       document.getElementById('tnaFormDivisi'),
            user:         document.getElementById('tnaFormUser'),
            location:     document.getElementById('tnaFormLocation'),
        };

        const state = { id: null, name: '', date: '' };

        const showModal = modal => {
            modal.hidden = false;
            modal.setAttribute('aria-hidden', 'false');
        };

        const hideModal = modal => {
            modal.hidden = true;
            modal.setAttribute('aria-hidden', 'true');
        };

        // Handle popover for expired items
        document.querySelectorAll('.tna-expired-item').forEach(item => {
            const popover   = item.querySelector('.tna-popover');
            const actionBtn = item.querySelector('.tna-expired-action');

            item.addEventListener('click', (e) => {
                if (e.target.closest('.tna-expired-action')) return;

                e.stopPropagation();
                document.querySelectorAll('.tna-popover').forEach(p => p.classList.remove('show'));

                state.id   = item.dataset.tnaId;
                state.name = item.dataset.tnaName || 'training ini';

                // Populate form fields
                formFields.tnaId.value        = item.dataset.tnaId || '';
                formFields.workshopName.value = item.dataset.tnaName || '-';
                formFields.startDate.value    = item.dataset.tnaDate || '';
                formFields.endDate.value      = item.dataset.tnaEnddate || '';
                formFields.divisi.value       = item.dataset.tnaDivisi || '-';
                formFields.user.value         = item.dataset.tnaUser || '-';
                formFields.location.value     = '';

                // Pre-select instructor radio from data attribute
                const instructorVal = item.dataset.tnaInstructor || '';
                document.querySelectorAll('#tnaExpiredForm input[name="instructor"]').forEach(radio => {
                    radio.checked = (radio.value === instructorVal);
                });

                // Reset error
                formError.hidden = true;
                formError.textContent = '';
                formSubmitBtn.disabled = false;

                showModal(formModal);
            });

            if (actionBtn) {
                actionBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    state.id   = item.dataset.tnaId;
                    state.name = item.dataset.tnaName || 'training ini';
                    moveName.textContent = state.name;
                    if (popover) popover.classList.remove('show');
                    showModal(moveModal);
                });
            }
        });

        // Handle click for other tabs (upcoming, used, completed)
        document.querySelectorAll('#upcoming [data-tna-id], #used [data-tna-id], #completed [data-tna-id]').forEach(item => {
            item.addEventListener('click', () => {
                state.id   = item.dataset.tnaId;
                state.name = item.dataset.tnaName || 'training ini';
                moveName.textContent = state.name;
                showModal(moveModal);
            });
        });

        // Close popover when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.tna-popover').forEach(p => p.classList.remove('show'));
        });

        // ─── Handle TNA Implement Form Submit ───────────────────────────────
        document.getElementById('tnaExpiredForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            formError.hidden = true;
            formSubmitBtn.disabled = true;
            formSubmitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            const formData = new FormData(e.target);

            // Build JSON payload
            const payload = {
                start_date: formData.get('start_date'),
                end_date:   formData.get('end_date'),
                instructor: formData.get('instructor'),
                location:   formData.get('location') || null,
            };

            const tnaId = formFields.tnaId.value;

            try {
                const response = await fetch(`{{ url('dashboard/training-workshop') }}/${tnaId}/implement`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const data = await response.json();

                if (!response.ok || !data.status) {
                    // Handle Laravel validation errors
                    if (data.errors) {
                        const msgs = Object.values(data.errors).flat().join('\n');
                        throw new Error(msgs);
                    }
                    throw new Error(data.message || 'Gagal menyimpan data.');
                }

                hideModal(formModal);

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Implementasi TNA berhasil disimpan dan Event Training telah dibuat.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0d6efd',
                    timer: 3000,
                    timerProgressBar: true,
                });

                window.location.reload();

            } catch (error) {
                formError.textContent = error.message;
                formError.hidden = false;
                formSubmitBtn.disabled = false;
                formSubmitBtn.innerHTML = '<i class="ti ti-check me-1"></i>Simpan &amp; Buat Event';
            }
        });

        // ─── Move to Completed ───────────────────────────────────────────────
        moveModal.querySelector('#tnaMoveButton').addEventListener('click', () => {
            hideModal(moveModal);
            confirmError.hidden = true;
            showModal(confirmModal);
        });

        document.querySelectorAll('[data-tna-close]').forEach(button => {
            button.addEventListener('click', () => {
                hideModal(moveModal);
                hideModal(confirmModal);
                hideModal(formModal);
            });
        });

        confirmModal.querySelector('#tnaConfirmButton').addEventListener('click', async event => {
            const button = event.currentTarget;
            button.disabled = true;
            confirmError.hidden = true;

            try {
                const response = await fetch(`{{ url('dashboard/training-workshop') }}/${state.id}/complete`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();

                if (!response.ok || !data.status) {
                    throw new Error(data.message || 'Data gagal dipindahkan.');
                }

                window.location.reload();
            } catch (error) {
                confirmError.textContent = error.message;
                confirmError.hidden = false;
                button.disabled = false;
            }
        });
    })();
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
