<div class="col-lg-12 col-12 pb-3">
    <style>
        .tna-clickable {
            cursor: pointer;
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
            width: min(100%, 420px);
            background: #fff;
            border-radius: .5rem;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .2);
        }
    </style>
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti ti-checklist me-2"></i>
                Approval Management - {{ $selectedYear ?? now()->year }}
            </h5>
        </div>

        <div class="card-body">
            @php
                // Tentukan tab mana yang aktif pertama kali
                if ($approvalAnalisa->isNotEmpty()) {
                    $firstActiveTab = 'analysis';
                } elseif ($approvalRencanaUsulan->isNotEmpty()) {
                    $firstActiveTab = 'proposal';
                } else {
                    $firstActiveTab = 'schedule';
                }
            @endphp

            <!-- Tabs Nav -->
            <ul class="nav nav-tabs mb-3" id="approvalTabs" role="tablist">
                @if($approvalAnalisa->isNotEmpty())
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $firstActiveTab === 'analysis' ? 'active' : '' }}"
                            id="analysis-tab" data-bs-toggle="tab" data-bs-target="#analysis"
                            type="button" role="tab" aria-controls="analysis"
                            aria-selected="{{ $firstActiveTab === 'analysis' ? 'true' : 'false' }}">
                            Analisa Kebutuhan
                            <span class="badge bg-warning text-dark ms-1">{{ $approvalAnalisa->count() }}</span>
                        </button>
                    </li>
                @endif
                @if($approvalRencanaUsulan->isNotEmpty())
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $firstActiveTab === 'proposal' ? 'active' : '' }}"
                        id="proposal-tab" data-bs-toggle="tab" data-bs-target="#proposal"
                        type="button" role="tab" aria-controls="proposal"
                        aria-selected="{{ $firstActiveTab === 'proposal' ? 'true' : 'false' }}">
                        Rencana Usulan
                        <span class="badge bg-warning text-dark ms-1">{{ $approvalRencanaUsulan->count() }}</span>
                    </button>
                </li>
                @endif
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" type="button" role="tab" aria-controls="schedule" aria-selected="false">Jadwal</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="instruction-tab" data-bs-toggle="tab" data-bs-target="#instruction" type="button" role="tab" aria-controls="instruction" aria-selected="false">Surat Perintah</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="evaluation-tab" data-bs-toggle="tab" data-bs-target="#evaluation" type="button" role="tab" aria-controls="evaluation" aria-selected="false">Evaluasi</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="report-tab" data-bs-toggle="tab" data-bs-target="#report" type="button" role="tab" aria-controls="report" aria-selected="false">Laporan Pelatihan</button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="approvalTabsContent">
                <!-- Analisa Kebutuhan Tab -->
                @if($approvalAnalisa->isNotEmpty())
                <div class="tab-pane fade {{ $firstActiveTab === 'analysis' ? 'show active' : '' }}" id="analysis" role="tabpanel" aria-labelledby="analysis-tab">
                    <div class="nearby-training-list">
                        @foreach($approvalAnalisa as $analisa)
                        <a href="{{ route('training-analyst.form', ['training_analyst' => $analisa->id]) }}" class="text-decoration-none text-reset" target="_blank">
                            <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-0">
                                <div>
                                    <div class="nearby-training-title fw-bold">
                                        Analisa Kebutuhan Pelatihan {{ $analisa->training->year ?? '-' }}
                                    </div>
                                    <div class="nearby-training-meta mb-1 text-muted">
                                        <i class="ti ti-user"></i>
                                        <span>{{ $analisa->user->name ?? '-' }}</span>
                                    </div>
                                    <div class="nearby-training-meta mb-1 text-muted">
                                        <i class="ti ti-building"></i>
                                        <span>{{ $analisa->divisi ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    {!! $analisa->badge_status ?? '' !!}
                                    <span class="nearby-badge-day">
                                        <i class="ti ti-external-link"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Rencana Usulan Tab -->
                @if($approvalRencanaUsulan->isNotEmpty())
                <div class="tab-pane fade {{ $firstActiveTab === 'proposal' ? 'show active' : '' }}" id="proposal" role="tabpanel" aria-labelledby="proposal-tab">
                    <div class="nearby-training-list">
                        @foreach($approvalRencanaUsulan as $rencana)
                        <a href="{{ url('training-workshop') . '?training_need=' . $rencana->id }}" class="text-decoration-none text-reset" target="_blank">
                            <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-0">
                                <div>
                                    <div class="nearby-training-title fw-bold">
                                        Rencana Usulan Pelatihan {{ $rencana->training->year ?? '-' }}
                                    </div>
                                    <div class="nearby-training-meta mb-1 text-muted">
                                        <i class="ti ti-user"></i>
                                        <span>{{ $rencana->user->name ?? '-' }}</span>
                                    </div>
                                    <div class="nearby-training-meta mb-1 text-muted">
                                        <i class="ti ti-building"></i>
                                        <span>{{ $rencana->divisi ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-column align-items-end gap-1">
                                    {!! $rencana->badge_status ?? '' !!}
                                    <span class="nearby-badge-day">
                                        <i class="ti ti-external-link"></i>
                                    </span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="instruction" role="tabpanel" aria-labelledby="instruction-tab">
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

                <!-- Not Completed Tab -->
                <div class="tab-pane fade" id="schedule" role="tabpanel" aria-labelledby="schedule-tab">
                    <div class="nearby-training-list">
                        @forelse($trainingNeedAnalystExpired as $training)
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
                            <div class="text-muted text-center py-4">Tidak ada training expired.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Evaluation Tab -->
                <div class="tab-pane fade" id="evaluation" role="tabpanel" aria-labelledby="evaluation-tab">
                    <div class="nearby-training-list">
                        <div class="text-muted text-center py-4">Belum ada data evaluasi.</div>
                    </div>
                </div>

                <!-- Training Report Tab -->
                <div class="tab-pane fade" id="report" role="tabpanel" aria-labelledby="report-tab">
                    <div class="nearby-training-list">
                        <div class="text-muted text-center py-4">Belum ada laporan pelatihan.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tna-modal" id="tnaMoveModal" hidden aria-hidden="true">
        <div class="tna-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="tnaMoveTitle">
            <div class="p-4">
                <h5 id="tnaMoveTitle">Pindahkan Training</h5>
                <p class="mb-4">Pindahkan <strong id="tnaMoveName"></strong> ke Completed?</p>
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary" data-tna-close>Batal</button>
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
                    <button type="button" class="btn btn-secondary" data-tna-close>Batal</button>
                    <button type="button" class="btn btn-success" id="tnaConfirmButton">Ya, pindahkan</button>
                </div>
                <div class="text-danger small mt-3" id="tnaConfirmError" hidden></div>
            </div>
        </div>
    </div>
</div>
