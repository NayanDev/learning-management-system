@extends("easyadmin::backend.parent")

@section("content")
    @push('mtitle')
        {{$title}}
    @endpush

    <div class="pc-container">
        <div class="pc-content">

            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="m-b-10">{{$title}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-6">
                    <div class="card mb-4">
                        <div class="card-body p-3">

                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-white py-3 border-bottom">
                                    <h5 class="mb-0 fw-bold text-dark">Analisa Kebutuhan</h5>
                                </div>

    <div class="card-header bg-white border-bottom pb-0">
        <ul class="nav nav-tabs" id="analisaTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link text-danger active"
                        id="pengajuan-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#pengajuan"
                        type="button"
                        role="tab">
                    Pengajuan
                </button>
            </li>

            <li class="nav-item">
                <button class="nav-link"
                        id="approve-tab"
                        data-bs-toggle="tab"
                        data-bs-target="#approve"
                        type="button"
                        role="tab">
                    Sudah Approve
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-0">

        <div class="tab-content">

            <!-- ================= TAB PENGAJUAN ================= -->
            <div class="tab-pane fade show active" id="pengajuan" role="tabpanel">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light text-secondary">
                            <tr>
                                <th>User</th>
                                <th>Tgl Pengajuan</th>
                                <th>Approve By</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>Ahmad Fauzi</td>
                                <td>2023-10-01</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        Menunggu
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-primary">
                                            Lihat
                                        </button>

                                        <button class="btn btn-sm btn-success text-dark"
                                                onclick="tinjauPengajuan(this)">
                                            Proses
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Siti Aminah</td>
                                <td>2023-10-02</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        Menunggu
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-primary">
                                            Lihat
                                        </button>

                                        <button class="btn btn-sm btn-success text-dark"
                                                onclick="tinjauPengajuan(this)">
                                            Proses
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>Rizky Pratama</td>
                                <td>2023-10-03</td>
                                <td>-</td>
                                <td>
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-2">
                                        Menunggu
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button class="btn btn-sm btn-primary">
                                            Lihat
                                        </button>

                                        <button class="btn btn-sm btn-success text-dark"
                                                onclick="tinjauPengajuan(this)">
                                            Proses
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>

            </div>

            <!-- ================= TAB SUDAH APPROVE ================= -->
            <div class="tab-pane fade" id="approve" role="tabpanel">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light text-secondary">
                            <tr>
                                <th>User</th>
                                <th>Tgl Pengajuan</th>
                                <th>Approve By</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>Budi Santoso</td>
                                <td>2023-09-25</td>
                                <td>Admin</td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>Rina Putri</td>
                                <td>2023-09-28</td>
                                <td>Admin</td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                            <tr>
                                <td>Fajar Nugroho</td>
                                <td>2023-09-30</td>
                                <td>Admin</td>
                                <td>
                                    <span class="badge bg-success rounded-pill px-3 py-2">
                                        Disetujui
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary">
                                        Lihat
                                    </button>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>

            </div>

        </div>

    </div>

</div>

                        </div>
                    </div>
                </div>

                

            </div>

        </div>
    </div>

    <script>
        function tinjauPengajuan(btn) {
            Swal.fire({
                title: 'Tinjau Pengajuan',
                text: "Tentukan status persetujuan untuk materi kursus ini.",
                icon: 'question',
                showDenyButton: true,
                showCancelButton: true,
                confirmButtonText: 'Setuju',
                denyButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#198754', // Warna Success Bootstrap
                denyButtonColor: '#dc3545',    // Warna Danger Bootstrap
                cancelButtonColor: '#6c757d',  // Warna Secondary Bootstrap
                reverseButtons: true // Membalikkan posisi tombol (opsional agar Batal ada di kiri)
            }).then((result) => {
                // Cari elemen baris (tr) terdekat dari tombol yang diklik
                let row = btn.closest('tr');
                let badge = row.querySelector('.badge');

                if (result.isConfirmed) {
                    Swal.fire('Disetujui!', 'Materi kursus telah disetujui.', 'success');
                    
                    // Update tampilan UI badge ke Disetujui
                    badge.className = 'badge bg-success rounded-pill px-3 py-2';
                    badge.innerText = 'Disetujui';
                    btn.remove(); // Hilangkan tombol Proses
                    
                } else if (result.isDenied) {
                    Swal.fire('Ditolak!', 'Materi kursus telah ditolak.', 'error');
                    
                    // Update tampilan UI badge ke Ditolak
                    badge.className = 'badge bg-danger rounded-pill px-3 py-2';
                    badge.innerText = 'Ditolak';
                    btn.remove(); // Hilangkan tombol Proses
                }
            });
        }
    </script>
@endsection