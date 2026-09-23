<div class="col-lg-6 col-12">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti ti-clock me-2"></i>
                Evaluation after 3 month - {{ $selectedYear ?? now()->year }}
            </h5>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs mb-3" id="evaluationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="ev-upcoming-tab" data-bs-toggle="tab"
                        data-bs-target="#ev-upcoming" type="button" role="tab" aria-controls="ev-upcoming"
                        aria-selected="true">Upcoming<span class="badge bg-warning rounded-pill ms-1">{{ $evaluationTriMonths->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ev-not-completed-tab" data-bs-toggle="tab"
                        data-bs-target="#ev-not-completed" type="button" role="tab" aria-controls="ev-not-completed"
                        aria-selected="false">Expired<span
                            class="badge bg-danger rounded-pill ms-1">{{ $evaluationNotCompleted->count() }}</span></button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ev-completed-tab" data-bs-toggle="tab" data-bs-target="#ev-completed"
                        type="button" role="tab" aria-controls="ev-completed" aria-selected="false">Completed<span
                            class="badge bg-success rounded-pill ms-1">0</span></button>
                </li>
            </ul>
            <div class="tab-content" id="evTabsContent">
                <div class="tab-pane fade show active" id="ev-upcoming" role="tabpanel" aria-labelledby="ev-upcoming-tab">

                    <div class="nearby-training-list">

                        @forelse($evaluationTriMonths as $event)
                            <a href="javascript:void(0)"
                                onclick="showEvaluationModal({{ $event->id }}, '{{ $event->token }}')"
                                class="text-decoration-none text-reset">

                                <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <div class="nearby-training-title">
                                            {{ $event->workshop->name ?? '-' }}
                                        </div>

                                        <div class="nearby-training-meta mb-1">
                                            <i class="ti ti-calendar-event"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                            <span>|</span>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }}
                                                WIB</span>
                                        </div>

                                        <div class="nearby-training-meta">
                                            <i class="ti ti-map-pin"></i>
                                            <span>{{ $event->location ?? '-' }}</span>
                                        </div>

                                        <div class="mt-2 d-flex flex-wrap gap-2">
                                            @foreach ($event->evaluation_trainer_statuses ?? [] as $trainerStatus)
                                                @if ($trainerStatus['is_filled'])
                                                    <span class="nearby-badge-status filled">
                                                        {{ $trainerStatus['name'] }}
                                                    </span>
                                                @else
                                                    @if (!empty($trainerStatus['overdue_badge']))
                                                        <span class="nearby-badge-status overdue">
                                                            Terlambat {{ $trainerStatus['overdue_badge'] }}
                                                        </span>
                                                    @endif

                                                    <span class="nearby-badge-status empty">
                                                        {{ $trainerStatus['name'] }}
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-clipboard-off text-muted" style="font-size:4rem;"></i>

                                <h5 class="mt-3 text-muted">
                                    Tidak Ada Evaluasi 3 Bulan
                                </h5>

                                <p class="text-muted mb-0">
                                    Saat ini tidak ada evaluasi 3 bulan yang perlu ditindaklanjuti.
                                </p>
                            </div>
                        @endforelse

                    </div>
                </div>

                <!-- Completed Tab -->
                <div class="tab-pane fade" id="ev-completed" role="tabpanel" aria-labelledby="ev-completed-tab">
                    <div class="nearby-training-list">
                        <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <div class="nearby-training-title fw-bold">
                                    Pelatihan Komunikasi Dasar (Completed)
                                </div>
                                <div class="nearby-training-meta mb-1 text-muted">
                                    <i class="ti ti-calendar-event"></i>
                                    <span>15 Agu 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Not Completed Tab -->
                <div class="tab-pane fade" id="ev-not-completed" role="tabpanel" aria-labelledby="ev-not-completed-tab">
                    <div class="nearby-training-list">
                        @forelse($evaluationNotCompleted as $event)
                            <a href="javascript:void(0)"
                                onclick="showEvaluationModal({{ $event->id }}, '{{ $event->token }}')"
                                class="text-decoration-none text-reset">
                                <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3 mb-2">
                                    <div>
                                        <div class="nearby-training-title">
                                            {{ $event->workshop->name ?? '-' }} ({{ $event->instructor }})
                                        </div>
                                        <div class="nearby-training-meta mb-1">
                                            <i class="ti ti-calendar-event"></i>
                                            <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                            <span>|</span>
                                            <span class="text-danger">
                                                Terlambat {{ (int) $event->evaluation_overdue_days }} Hari
                                            </span>
                                        </div>
                                        <div class="nearby-training-meta mb-1">
                                            <i class="ti ti-map-pin"></i>
                                            <span>{{ $event->location ?? '-' }}</span>
                                        </div>
                                        <div class="nearby-training-meta mb-1">
                                        
                                            @foreach($event->evaluation_trainer_statuses ?? [] as $trainerStatus)
                                                @if($trainerStatus['is_filled'])
                                                    <i class="ti ti-check text-success"></i>
                                                    <span>{{ $trainerStatus['name'] }}</span>
                                                @else
                                                    <i class="ti ti-x text-danger"></i>
                                                    <span>{{ $trainerStatus['name'] }}</span>
                                                @endif
                                            @endforeach

                                        </div>

                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center py-5">
                                <i class="ti ti-clipboard-check text-muted" style="font-size:4rem;"></i>
                                <h5 class="mt-3 text-muted">Tidak Ada Evaluasi Terlambat</h5>
                                <p class="text-muted mb-0">Saat ini tidak ada evaluasi yang melewati waktu pengisian.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>


    </div>
</div>
