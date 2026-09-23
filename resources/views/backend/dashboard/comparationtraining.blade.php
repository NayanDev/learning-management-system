<div class="col-lg-6 col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent d-flex align-items-center justify-content-between">
                    <h5 class="mb-0"><i class="ti ti-chart-bar me-2"></i>Comparation Training</h5>
                    <small class="text-muted">Tahun {{ $selectedYear ?? now()->year }} - Realisasi: {{ number_format((float) ($trainingRealizationPercentage ?? 0), 2, '.', '') }}%</small>
                </div>
                <div class="card-body">
                    <div class="text-muted small mb-2">
                        ({{ (int) ($totalEvents ?? 0) }} * 100) / {{ (int) ($totalTrainingWorkshops ?? 0) }} =
                        <strong>{{ number_format((float) ($trainingRealizationPercentage ?? 0), 2, '.', '') }}%</strong>
                    </div>
                    <div style="height: 220px;">
                        <canvas id="trainingComparisonChart"></canvas>
                    </div>
                </div>
            </div>
        </div>