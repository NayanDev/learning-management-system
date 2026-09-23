<div class="col-lg-6 col-12">
    <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
            <h5 class="mb-0">
                <i class="ti ti-clock me-2"></i>
                Upcoming Training - {{ $selectedYear ?? now()->year }}
            </h5>
        </div>

        <div class="card-body">
            <div class="nearby-training-list">

                @forelse($trainingcoomingsoon as $event)
                    <div class="nearby-training-item d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <div class="nearby-training-title">
                                {{ $event->workshop->name ?? '-' }}
                            </div>

                            <div class="nearby-training-meta mb-1">
                                <i class="ti ti-calendar-event"></i>
                                <span>{{ \Carbon\Carbon::parse($event->start_date)->format('d M Y') }}</span>
                                <span>|</span>
                                <span>{{ \Carbon\Carbon::parse($event->start_date)->format('H:i') }} WIB</span>
                            </div>

                            <div class="nearby-training-meta">
                                <i class="ti ti-map-pin"></i>
                                <span>{{ $event->location ?? '-' }}</span>
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
    </div>
</div>
