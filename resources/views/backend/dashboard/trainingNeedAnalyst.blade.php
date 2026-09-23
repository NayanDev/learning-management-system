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
            <div class="tna-modal-header p-4">
                <h5 id="tnaFormTitle" class="mb-0">Apakah anda ingin implementasi TNA ?</h5>
            </div>
            <div class="tna-modal-body p-4">
                <form id="tnaExpiredForm">
                    <!-- Implementation Status -->
                    <div class="mb-3">
                        <input type="text" class="form-control" id="tnaFormStatus" name="implementation_status">
                    </div>

                    <!-- Nama Pelatihan -->
                    <div class="mb-3">
                        <label for="tnaFormWorkshopName" class="form-label">Nama Pelatihan</label>
                        <input type="text" class="form-control" id="tnaFormWorkshopName" name="workshop_name" readonly>
                    </div>

                    <!-- Start Date -->
                    <div class="mb-3">
                        <label for="tnaFormStartDate" class="form-label">Tanggal Mulai</label>
                        <input type="datetime-local" class="form-control" id="tnaFormStartDate" name="start_date" required>
                    </div>

                    <!-- End Date -->
                    <div class="mb-3">
                        <label for="tnaFormEndDate" class="form-label">Tanggal Selesai</label>
                        <input type="datetime-local" class="form-control" id="tnaFormEndDate" name="end_date">
                    </div>

                    <!-- Divisi -->
                    <div class="mb-3">
                        <label for="tnaFormDivisi" class="form-label">Divisi</label>
                        <input type="text" class="form-control" id="tnaFormDivisi" name="divisi" readonly>
                    </div>

                    <!-- Instructor -->
                    <div class="mb-3">
                        <label for="tnaFormInstructor" class="form-label">Instruktur</label>
                        <input type="text" class="form-control" id="tnaFormInstructor" name="instructor" readonly>
                    </div>

                    <!-- User -->
                    <div class="mb-3">
                        <label for="tnaFormUser" class="form-label">Trainer</label>
                        <input type="text" class="form-control" id="tnaFormUser" name="user_name" readonly>
                    </div>

                    {{-- Location --}}
                    <div class="mb-3">
                        <label for="tnaFormLocation" class="form-label">Location</label>
                        <input type="text" class="form-control" id="tnaFormLocation" name="location">
                    </div>
                </form>
            </div>
            <div class="tna-modal-footer p-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-danger" data-tna-close>Batal</button>
                <button type="submit" form="tnaExpiredForm" class="btn btn-primary">Simpan</button>
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
        const formModal = document.getElementById('tnaFormModal');
        const moveModal = document.getElementById('tnaMoveModal');
        const confirmModal = document.getElementById('tnaConfirmModal');
        const moveName = document.getElementById('tnaMoveName');
        const confirmError = document.getElementById('tnaConfirmError');
        
        // Form fields
        const formFields = {
            status: document.getElementById('tnaFormStatus'),
            workshopName: document.getElementById('tnaFormWorkshopName'),
            startDate: document.getElementById('tnaFormStartDate'),
            endDate: document.getElementById('tnaFormEndDate'),
            divisi: document.getElementById('tnaFormDivisi'),
            instructor: document.getElementById('tnaFormInstructor'),
            user: document.getElementById('tnaFormUser')
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
            const popover = item.querySelector('.tna-popover');
            const actionBtn = item.querySelector('.tna-expired-action');
            
            item.addEventListener('click', (e) => {
                // Jika klik pada tombol, jangan tampilkan form modal
                if (e.target.closest('.tna-expired-action')) {
                    return;
                }
                
                e.stopPropagation();
                document.querySelectorAll('.tna-popover').forEach(p => p.classList.remove('show'));
                
                // Tampilkan form modal dengan data
                state.id = item.dataset.tnaId;
                state.name = item.dataset.tnaName || 'training ini';
                state.date = item.dataset.tnaDate || '';
                
                // Populate all form fields
                formFields.status.value = item.dataset.tnaStatus || 'open';
                formFields.workshopName.value = item.dataset.tnaName || '-';
                formFields.startDate.value = item.dataset.tnaDate || '';
                formFields.endDate.value = item.dataset.tnaEnddate || '';
                formFields.divisi.value = item.dataset.tnaDivisi || '-';
                formFields.instructor.value = item.dataset.tnaInstructor || '-';
                formFields.user.value = item.dataset.tnaUser || '-';
                
                showModal(formModal);
            });

            if (actionBtn) {
                actionBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    state.id = item.dataset.tnaId;
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
                state.id = item.dataset.tnaId;
                state.name = item.dataset.tnaName || 'training ini';
                moveName.textContent = state.name;
                showModal(moveModal);
            });
        });

        // Close popover when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.tna-popover').forEach(p => p.classList.remove('show'));
        });

        // Handle form submit
        document.getElementById('tnaExpiredForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(document.getElementById('tnaExpiredForm'));
            console.log('Form submitted:', Object.fromEntries(formData));
            hideModal(formModal);
            // TODO: Add API call here to save the form data
        });

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
