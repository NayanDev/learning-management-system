<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f4f7f6;
        /* Warna latar belakang simulasi dashboard */
    }

    /* Styling khusus untuk Card Peringatan */
    .tna-warning-card {
        border-left: 6px solid #ffc107;
        transition: all 0.3s ease;
        background: linear-gradient(to right, #fffcf5, #ffffff);
    }

    .tna-warning-card:hover {
        box-shadow: 0 10px 25px rgba(255, 193, 7, 0.15) !important;
        transform: translateY(-2px);
    }

    /* Icon Container dengan Animasi Pulse */
    .icon-circle {
        width: 60px;
        height: 60px;
        background-color: rgba(255, 193, 7, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .icon-pulse {
        animation: pulse-warning 2s infinite;
    }

    @keyframes pulse-warning {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 10px rgba(255, 193, 7, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(255, 193, 7, 0);
        }
    }

    /* Countdown Badge Styling */
    .countdown-box {
        background-color: #fff3cd;
        color: #856404;
        padding: 8px 12px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
        min-width: 60px;
        border: 1px solid #ffeeba;
    }

    .countdown-label {
        font-size: 0.7rem;
        text-transform: uppercase;
        display: block;
        margin-top: 2px;
        color: #b58500;
    }

    /* Animasi penutupan card */
    .fade-out {
        opacity: 0;
        transform: scale(0.95);
        transition: opacity 0.4s ease, transform 0.4s ease;
    }

    /* Tombol Close kustom */
    .btn-close-custom {
        position: absolute;
        top: 15px;
        right: 15px;
        background: none;
        border: none;
        color: #adb5bd;
        font-size: 1.2rem;
        transition: color 0.2s;
    }

    .btn-close-custom:hover {
        color: #dc3545;
    }
</style>

<div class="mt-3 mb-1">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-12">

            <!-- Mulai Section/Card Warning TNA -->
            <div id="tnaAlert"
                class="card tna-warning-card shadow-sm border-0 rounded-4 position-relative overflow-hidden">

                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center">

                        <!-- Kolom Ikon -->
                        <div class="col-auto mb-4 mb-md-0">
                            <div class="icon-circle icon-pulse">
                                <i class="ti ti-alert-triangle text-warning"></i>
                            </div>
                        </div>

                        <!-- Kolom Teks Informasi -->
                        <div class="col-12 col-md mb-4 mb-md-0">
                            <span class="badge bg-warning text-dark mb-2 px-3 py-2 rounded-pill fw-semibold">
                                <i class="ti ti-bell me-1"></i> Wajib Diisi
                            </span>
                            <h4 class="fw-bold mb-1 text-dark">Peringatan: Pengisian <span class="text-warning">Training
                                    Need Analysis (TNA)</span></h4>
                            <p class="text-secondary mb-0 mt-2">
                                Form Pengisian Training Needs Analysis (TNA) telah dibuka. Silakan segera mengisi
                                kebutuhan pelatihan sesuai dengan kebutuhan pekerjaan, pengembangan kompetensi, serta
                                tantangan yang dihadapi dalam menjalankan pekerjaan. Data yang disampaikan akan menjadi
                                bahan pertimbangan dalam penyusunan program pelatihan dan pengembangan kompetensi agar
                                lebih tepat sasaran dan sesuai dengan kebutuhan. Mohon mengisi form dengan lengkap,
                                jelas, dan sesuai dengan kebutuhan masing-masing.
                            </p>
                            <p class="text-secondary mb-0 mt-2">
                                <b>Catatan dari HR :</b> <br>
                                {{ $hasTrainingNeed->description ?? 'Tidak ada catatan tambahan dari HR.' }}
                            </p>
                        </div>

                        <!-- Kolom Action & Countdown -->
                        <div
                            class="col-12 col-lg-auto d-flex flex-column align-items-start align-items-lg-end border-start-lg ps-lg-4">

                            <!-- Waktu Mundur -->
                            <p class="text-muted small fw-semibold mb-2">Sisa Waktu Pengisian:</p>
                            <div class="d-flex gap-2 mb-3" id="countdown-timer">
                                <div class="countdown-box shadow-sm">
                                    <span id="cd-days" class="fs-5">00</span>
                                    <span class="countdown-label">Hari</span>
                                </div>
                                <div class="countdown-box shadow-sm">
                                    <span id="cd-hours" class="fs-5">00</span>
                                    <span class="countdown-label">Jam</span>
                                </div>
                                <div class="countdown-box shadow-sm">
                                    <span id="cd-mins" class="fs-5">00</span>
                                    <span class="countdown-label">Menit</span>
                                </div>
                            </div>

                            @php
                                $user = Auth::user();
                                $developer = $user->role->name === 'developer';
                                $adminTnaUser = $tnaAdmins;
                                $adminTna = in_array($user->id, $adminTnaUser);
                                $manager = $user->position === 'MANAGER';
                            @endphp

                            <!-- Tombol Aksi -->
                            @if ($adminTna || $developer || $manager)
                                <div class="d-flex gap-2 w-100 mb-2">
                                    <a href="{{ url('training-analyst') . '?training_id=' . $hasTrainingNeed->id }}"
                                        class="btn btn-warning fw-bold px-4 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                        Analisa Kebutuhan
                                    </a>
                                </div>
                            @endif
                            @if ((!$hasTrainingNeed->trainingNeeds->first()?->divisi ?? false) && $adminTna || (!$hasTrainingNeed->trainingNeeds->first()?->divisi && $developer) || (!$hasTrainingNeed->trainingNeeds->first()?->divisi && $manager))
                                <div class="d-flex gap-2 w-100 mb-2">
                                    <button type="button"
                                        class="btn btn-primary fw-bold px-4 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2"
                                        data-bs-toggle="modal" data-bs-target="#modalFormUsulan">
                                        Form Rencana Usulan
                                    </button>
                                </div>
                            @elseif($hasTrainingNeed->trainingNeeds->first()?->divisi === strtoupper($user->divisi))
                                <div class="d-flex gap-2 w-100 mb-2">
                                    <a href="{{ url('training-workshop') . '?training_need=' . $hasTrainingNeed->trainingNeeds->first()->id }}"
                                        class="btn btn-success fw-bold px-4 flex-grow-1 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                        Rencana Usulan
                                    </a>
                                </div>
                            @else
                            <p class="text-muted small fw-semibold mb-2">Menunggu admin divisi membuat form</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <!-- Akhir Section/Card Warning TNA -->

        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalFormUsulan" tabindex="-1" aria-labelledby="modalFormUsulanLabel" aria-hidden="true">
    <div class="modal-dialog modal modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalFormUsulanLabel">
                    Buat Form Rencana Usulan Pelatihan
                </h5>
                <button type="button" class="btn-close" onclick="closeFormUsulanModal()" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <!-- Isi form di sini -->
                <form id="formUsulan" action="{{ route('training-need.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="training_id" value="{{ $trainingId ?? '' }}">

                    <div class="mb-3">
                        <label for="training_id_display" class="form-label">Training</label>
                        <input type="text" class="form-control" id="training_id_display"
                            value="{{ $hasTrainingNeed?->year ?? now()->year + 1 }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="user_display" class="form-label">User</label>
                        <input type="text" class="form-control" id="user_display"
                            value="{{ Auth::user()->name }}" readonly>
                    </div>

                    <div class="mb-3">
                        <label for="divisi_display" class="form-label">Divisi</label>
                        <input type="text" class="form-control" id="divisi_display"
                            value="{{ Auth::user()->divisi }}" readonly>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-danger" onclick="closeFormUsulanModal()">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function closeFormUsulanModal() {
        const modalElement = document.getElementById('modalFormUsulan');

        if (!modalElement) {
            return;
        }

        const modalInstance = bootstrap.Modal.getInstance(modalElement)
            || bootstrap.Modal.getOrCreateInstance(modalElement);
        modalInstance.hide();
        document.getElementById('formUsulan')?.reset();
    }

    document.addEventListener("DOMContentLoaded", function() {

        // 1. Logika Countdown Timer
        const trainingEndDate = @json($trainingEndDate ?? null);
        const deadline = trainingEndDate ? new Date(trainingEndDate.replace(' ', 'T')) : null;

        function updateCountdown() {
            if (!deadline || Number.isNaN(deadline.getTime())) {
                document.getElementById("countdown-timer").innerHTML =
                    "<span class='badge bg-secondary p-2 fs-6'>Deadline belum tersedia</span>";
                return;
            }

            const now = new Date().getTime();
            const distance = deadline - now;

            if (distance < 0) {
                // Waktu habis
                document.getElementById("countdown-timer").innerHTML =
                    "<span class='badge bg-danger p-2 fs-6'>Waktu Pengisian Berakhir</span>";
                return;
            }

            // Kalkulasi hari, jam, menit
            const days = Math.floor(distance / (1000 * 60 * 60 * 24));
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            // const seconds = Math.floor((distance % (1000 * 60)) / 1000); // Opsional jika butuh detik

            // Update DOM dengan memastikan format 2 digit (01, 02, dsb)
            document.getElementById("cd-days").textContent = days.toString().padStart(2, '0');
            document.getElementById("cd-hours").textContent = hours.toString().padStart(2, '0');
            document.getElementById("cd-mins").textContent = minutes.toString().padStart(2, '0');
        }

        // Jalankan fungsi update setiap detik
        updateCountdown(); // Jalankan sekali saat load
        setInterval(updateCountdown, 1000); // Perbarui setiap 1 detik
    });
</script>
