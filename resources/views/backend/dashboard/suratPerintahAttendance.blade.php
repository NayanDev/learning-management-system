<div class="row g-4">

    @php
        $hasUpcomingTraining = false;
    @endphp

    @foreach ($eventsAttendance as $event)
        @if (!$event->attendance?->date_ready)
            @php
                $hasUpcomingTraining = true;
            @endphp
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-1">{{ $event->event->workshop->name ?? '-' }}</h5>
                                <p class="card-subtitle text-muted">Instruktur:
                                    @foreach ($event->event->trainers as $trainer)
                                        {{ ucwords(strtolower($trainer->user?->name ?? ($trainer->external ?? '-'))) }}
                                        @if (!$loop->last)
                                            ,
                                        @endif
                                    @endforeach
                                    ({{ $event->event->instructor }})
                                </p>
                            </div>
                            <span class="badge bg-warning rounded-pill px-3 py-2 flex-shrink-0">Confirmation</span>
                        </div>

                        <hr class="my-3">

                        <div class="row g-3">
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="ti ti-calendar-event fs-4 text-muted me-2"></i>
                                <span
                                    class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->translatedFormat('d F Y') ?? '-' }}</span>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <i class="ti ti-clock fs-4 text-muted me-2"></i>
                                <span
                                    class="fw-medium">{{ \Carbon\Carbon::parse($event->event->start_date)->format('H:i') ?? '-' }}
                                    - {{ \Carbon\Carbon::parse($event->event->end_date)->format('H:i') ?? '-' }}
                                    WIB</span>
                            </div>
                            <div class="col-12 d-flex align-items-center">
                                <i class="ti ti-map-pin fs-4 text-muted me-2"></i>
                                <span class="fw-medium">{{ $event->event->location ?? '-' }}</span>
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="token-area d-flex justify-content-center align-items-center ">
                            <button class="btn btn-sm btn-info"
                                onclick="confirmAttendance('{{ $event->event->token }}')">
                                <i class="ti ti-check me-1"></i> Attendance
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>
