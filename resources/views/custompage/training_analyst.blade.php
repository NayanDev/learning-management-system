@foreach($training_analyst as $data)
    @php
        $qualification = json_decode($data->qualification, true);
        $general = json_decode($data->general, true);
        $technic = json_decode($data->technic, true);
    @endphp
@endforeach

@extends('easyadmin::backend.parent')
@section('content')
@push('mtitle')
{{ $title }}
@endpush

<div class="pc-container" id="section-list-{{ $uri_key }}">
    <div class="pc-content">

        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body p-3">

                        @if(!empty($training_analyst) && is_countable($training_analyst) && count($training_analyst) > 0)
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th width="15%" rowspan="3" class="text-center">Jabatan</th>
                                        <th width="10%" rowspan="3" class="text-center">Jumlah <br> Personil</th>
                                        <th colspan="<?=count($qualification) + count($general) + count($technic) + 1 ?>" class="text-center">Jenis Pelatihan</th>
                                    </tr>
                                    <tr>
                                        <th colspan="<?=count($qualification)?>" class="text-center">Qualification</th>
                                        <th colspan="<?=count($general)?>" class="text-center">Pelatihan Umum</th>
                                        <th colspan="<?=count($technic) + 1 ?>" class="text-center">Pelatihan Khusus & Tambahan</th>
                                    </tr>
                                    <tr>
                                        @foreach($training_analyst as $data)
                                            @php
                                                $qualification = json_decode($data->qualification, true);
                                                $general = json_decode($data->general, true);
                                                $technic = json_decode($data->technic, true);
                                            @endphp
                                            @foreach($qualification as $key )
                                                <th class="text-center"><span class="vertical">{{ $key }}</span></th>
                                            @endforeach
                                            @foreach($general as $key )
                                                <th class="text-center"><span class="vertical">{{ $key }}</span></th>
                                            @endforeach
                                            @foreach($technic as $key )
                                                <th class="text-center"><span class="vertical">{{ $key }}</span></th>
                                            @endforeach
                                            <th class="text-center"><span class="vertical"></span></th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody id="itemRows">
                                    @foreach($analyst_data as $data)
                                        @php
                                            $qualification = json_decode($data->qualification, true);
                                            $general = json_decode($data->general, true);
                                            $technic = json_decode($data->technic, true);
                                        @endphp

                                        <tr>
                                            <td><input type="text" value="{{ old('position', $data->position) }}" class="form-control form-control-sm"></td>
                                            <td><input type="number" value="{{ old('personil', $data->personil) }}" class="form-control form-control-sm"></td>

                                            @foreach($qualification as $qual)
                                                <td class="text-center"><input class="form-check-input input-primary" type="checkbox" {{$qual === "true" ? 'checked' : ''}}></td>
                                            @endforeach
                                            @foreach($general as $gen)
                                                <td class="text-center"><input class="form-check-input input-primary" type="checkbox" {{$gen === "true" ? 'checked' : ''}}></td>
                                            @endforeach
                                            @foreach($technic as $tech)
                                                <td class="text-center"><input class="form-check-input input-primary" type="checkbox" {{$tech === "true" ? 'checked' : ''}}></td>   
                                            @endforeach

                                                <td class="text-center">
                                                    <button type="button" class="delete-row btn avtar avtar-s btn-link-danger btn-pc-default">
                                                        <i class="ti ti-trash f-20"></i>
                                                    </button>
                                                </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>Data belum tersedia, buat header dulu dibagian kanan atas.</p>
                        @endif

                        <br>

                        <button id="btnAddItem" class="btn btn-light-primary d-flex align-items-center gap-2">
                            <i class="ti ti-plus"></i> Add new item
                        </button>

                        <hr>

                        @php
                            $trainingAnalyst = request('training_analyst') ?? '';
                            $user = Auth::user();
                        @endphp

                        <center>
                            <button id="btnSaveData" class="btn btn-primary"><i class="ti ti-device-floppy f-20"></i> Save Data</button>
                            <button class="btn btn-danger" onclick="window.location.href='{{ route('training-analyst.pdf', ['training_analyst' => request('training_analyst')]) }}'"><i class="ti ti-printer f-20"></i> Print Data</button>
                            @if($traing_analyst_single->status === 'submit')
                                <!-- <button     
                                    type="button"
                                    class="btn btn-warning"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalInfoManager">
                                    <i class="ti ti-brand-whatsapp f-20"></i> Info ke Manager
                                </button> -->
                            @endif
                            @if($traing_analyst_single->status === 'open')
                                <div class="mt-2 mb-2">
                                    <form action="{{ route('training-analyst.submit', $trainingAnalyst) }}" method="POST" id="submitTrainingForm">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-success" id="btnSubmitManager">
                                            <i class="ti ti-send f-20"></i> Submit to Manager 
                                        </button>
                                    </form>
                                </div>
                            @elseif($traing_analyst_single->status === 'submit')
                                @if(
                                    (strtolower($user->position) === 'manager') &&
                                    strtoupper($user->divisi) === $traing_analyst_single->divisi
                                )
                                    <div class="mt-2 mb-2">
                                        <button
                                            type="button"
                                            class="btn btn-success"
                                            id="btnApproveManager"
                                            data-id="{{ $traing_analyst_single->id }}">
                                            <i class="ti ti-shield-check f-20"></i> Approve
                                        </button>
                                    </div>
                                @else
                                    <div class="mt-2 mb-2 d-flex flex-column align-items-center gap-2">
                                        <div class="pending-approval-badge d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                                            <span class="spinner-grow spinner-grow-sm text-warning" role="status" aria-hidden="true"></span>
                                            <span class="fw-semibold text-warning">Menunggu Approval Manager</span>
                                        </div>
                                        <small class="text-muted">
                                            <i class="ti ti-info-circle"></i>
                                            Dokumen telah disubmit dan sedang menunggu persetujuan Manager Divisi.
                                        </small>
                                    </div>
                                @endif
                            @elseif($traing_analyst_single->status === 'approve')
                                <button class="btn btn-success" disabled>
                                    <i class="ti ti-check f-20"></i> Approved
                                </button>
                            @endif
                        </center>
                        
                        <div style="margin-top:20px;">
                            <p>Intruksi Kerja:</p>
                            <br>
                            <p>1. Perhatikan bagian "Pelatihan Khusus dan Tambahan" terlebih dahulu.</p>
                            <p>2. Sunting bagian "Pelatihan Khusus dan Tambahan" sesuai dengan kebutuhan divisi masing-masing.</p>
                            <p>3. Isi Kolom "Jumlah Personil" sesuai dengan kebutuhan masing-masing.</p>
                            <p>4. Beri tanda "&#10003;" pada kolom "Qualifications" sesuai dengan kualifikasi dari pemangku jabatan.</p>
                            <p>5. Sesuaikan pelatihan dengan kualifikasi dari pemangku jabatan.</p>
                            <p>6. Beri tanda "&#10003;" pada kolom "Jenis pelatihan" untuk menandai pelatihan yang dibutuhkan berdasarkan kualifikasi dari pemangku jabatan.</p>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modalInfoManager" tabindex="-1" aria-labelledby="modalInfoManagerLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modalInfoManagerLabel">
                    <i class="ti ti-brand-whatsapp text-success"></i>
                    Info ke Manager
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <!-- No Telp -->
                <div class="mb-3">
                    <label for="noTelpManager" class="form-label">
                        No. Telp Manager
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="noTelpManager"
                        value="0895382720752">
                </div>

                <!-- Pesan -->
                <div class="mb-3">
                    <label for="pesanManager" class="form-label">
                        Pesan
                    </label>

                    <textarea class="form-control" id="pesanManager" rows="5">Berikut ini saya sampaikan dokumen Analisa Kebutuhan Latihan untuk dapat diketahui dan diapprove.

Link dokumen: {{ route('training-analyst.form') }}?training_analyst={{ $trainingAnalyst }}</textarea>

                </div>

                <!-- Link WhatsApp -->
                <div class="mb-3">
                    <label for="linkWhatsapp" class="form-label">
                        Link WhatsApp
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="linkWhatsapp"
                        readonly>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Batal
                </button>

                <a
                    href="#"
                    target="_blank"
                    id="btnKirimWhatsapp"
                    class="btn btn-success">
                    <i class="ti ti-brand-whatsapp"></i>
                    Kirim WhatsApp 
                </a>
            </div>
            

        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@if (session('success'))
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            confirmButtonColor: '#198754',
            confirmButtonText: 'OK'
        });
    </script>
@endif

@push('styles')
<style>
    .pending-approval-badge {
        background: rgba(255, 193, 7, 0.12);
        border: 1.5px solid rgba(255, 193, 7, 0.5);
        box-shadow: 0 0 12px rgba(255, 193, 7, 0.2);
        animation: pulse-border 2s ease-in-out infinite;
    }

    @keyframes pulse-border {
        0%, 100% { box-shadow: 0 0 8px rgba(255, 193, 7, 0.2); }
        50%       { box-shadow: 0 0 18px rgba(255, 193, 7, 0.45); }
    }
</style>
@if(isset($import_styles))
@foreach($import_styles as $ist)
<link rel="stylesheet" href="{{$ist['source']}}">
@endforeach
@endif
@endpush

@push('scripts')

@if(isset($import_scripts))
@foreach($import_scripts as $isc)
<script src="{{$isc['source']}}"></script>
@endforeach
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {

    const noTelp = document.getElementById('noTelpManager');
    const pesan = document.getElementById('pesanManager');
    const linkWhatsapp = document.getElementById('linkWhatsapp');
    const btnKirimWhatsapp = document.getElementById('btnKirimWhatsapp');

    function generateWhatsappLink() {

        // Hilangkan karakter selain angka
        let nomor = noTelp.value.replace(/\D/g, '');

        // Jika nomor Indonesia diawali 0, ubah menjadi 62
        if (nomor.startsWith('0')) {
            nomor = '62' + nomor.substring(1);
        }

        // Encode pesan agar aman dimasukkan ke URL
        const pesanEncoded = encodeURIComponent(pesan.value);

        const url = `https://wa.me/${nomor}?text=${pesanEncoded}`;

        // Tampilkan link
        linkWhatsapp.value = url;

        // Set link tombol
        btnKirimWhatsapp.href = url;
    }

    // Generate saat halaman dibuka
    generateWhatsappLink();

    // Update ketika nomor atau pesan berubah
    noTelp.addEventListener('input', generateWhatsappLink);
    pesan.addEventListener('input', generateWhatsappLink);

});

    var btnSubmit = document.getElementById('btnSubmitManager');
    if (btnSubmit) {
        btnSubmit.addEventListener('click', function () {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Apakah Anda yakin ingin submit data ke Manager?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Submit',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('submitTrainingForm').submit();
                }
            });
        });
    }

    var btnApprove = document.getElementById('btnApproveManager');
    if (btnApprove) {
        btnApprove.addEventListener('click', function () {
            var id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Konfirmasi Approve',
                text: 'Apakah Anda yakin ingin menyetujui (approve) Analisa Kebutuhan ini?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Approve',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/training-analyst/' + id,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            status: 'approve',
                            approve_by: '{{ Auth::id() }}',
                            notes: '-'
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Loading...',
                                allowOutsideClick: false,
                                didOpen: () => Swal.showLoading()
                            });
                        },
                        success: function (response) {
                            Swal.close();
                            if (response.status) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil',
                                    text: 'Analisa Kebutuhan berhasil diapprove.',
                                    confirmButtonColor: '#198754'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal',
                                    text: response.message || 'Terjadi kesalahan.'
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.close();
                            var msg = 'Terjadi kesalahan. Silakan coba lagi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                msg = xhr.responseJSON.message;
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: msg
                            });
                        }
                    });
                }
            });
        });
    }

    const analystHeadId = new URLSearchParams(window.location.search).get('training_analyst');

    $(document).ready(function() {
        if ($(".idev-actionbutton").children().length == 0) {
            $("#dropdownMoreTopButton").remove()
            $(".idev-actionbutton").remove()
        }
        idevTable("list-{{$uri_key}}")
        $('form input').on('keypress', function(e) {
            return e.which !== 13;
        });

        const defaultJabatanListByDivisi = {
    "QA": [
        "Manager", "Asisten Manager", "Staff / SPV", "Kepala Kelompok",
        "Inspektor", "Administrasi", "", "", "", "",
    ],

    "QC": [
        "Manager", "Asisten Manager", "Staff / SPV", "Analis", "Inspektor (IPC)",
        "Administrasi", "", "", "", ""
    ],

    "RND": [
        "Manager", "Asisten Manager", "Staff / SPV", "Analis / Formulator",
        "Operator", "Administrasi", "", "", "", ""
    ],

    "PRODUKSI": [
        "Manager", "Asisten Manager", "Staff / SPV", "Kepala Kelompok",
        "Operator", "Administrasi", "", "", "", ""
    ],

    "SCM": [
        "Manager", "Asisten Manager", "Staff / SPV", "Kepala Kelompok",
        "Operator", "Administrasi", "", "", "", ""
    ],

    "QS": [
        "Asisten Manager", "Staff / SPV", "Inspektor",
        "Operator", "Administrasi", "", "", "", ""
    ],

    "TEKNIK": [
        "Manager", "Asisten Manager", "Staff / SPV", "Kepala Kelompok",
        "Teknisi", "Administrasi", "", "", "", ""
    ],

    "UMUM & SDM": [
        "Manager", "Asisten Manager", "Staff / SPV", "Pelaksana",
        "Administrasi", "", "", "", "", ""
    ],

    "IT": [
        "Manager", "Asisten Manager", "Staff / SPV",
        "Pelaksana", "Administrasi", "", "", "", "", ""
    ],

    "KEUANGAN": [
        "Manager", "Asisten Manager", "Staff / SPV", "Pelaksana",
        "", "", "", "", "", ""
    ],

    // Default
    DEFAULT: [
        "", "", "", "", "",
        "", "", "", "", ""
    ]
};

// Divisi user dari Laravel
const userDivisi = @json(Auth::user()->divisi);

// Ambil berdasarkan divisi.
// Jika tidak ditemukan → gunakan DEFAULT.
const defaultJabatanList =
    defaultJabatanListByDivisi[userDivisi]
    || defaultJabatanListByDivisi.DEFAULT;


        function createNewRow(jabatanName = '') {
            return `
                <tr>
                    <td><input name="position[]" type="text" class="form-control form-control-sm" value="${jabatanName}"></td>
                    <td><input name="personil[]" type="number" class="form-control form-control-sm"></td>
                                
                    {{-- Qualifications --}}
                    @if (!empty($qualification) && is_countable($qualification))
                        @foreach($qualification as $qual)
                            <td class="text-center">
                                <input class="form-check-input input-primary" type="checkbox">
                            </td>
                        @endforeach
                    @endif

                    {{-- General Training --}}
                    @if (!empty($general) && is_countable($general))
                        @foreach($general as $gen)
                            <td class="text-center">
                                <input class="form-check-input input-primary" type="checkbox">
                            </td>
                        @endforeach
                    @endif

                    {{-- technic Training --}}
                    @if (!empty($technic) && is_countable($technic))
                        @foreach($technic as $tech)
                            <td class="text-center">
                                <input class="form-check-input input-primary" type="checkbox">
                            </td>
                        @endforeach
                    @endif

                        <td class="text-center">
                            <button type="button" class="delete-row btn avtar avtar-s btn-link-danger btn-pc-default">
                                <i class="ti ti-trash f-20"></i>
                            </button>
                        </td>
                </tr>
            `;
        }

        $("#btnAddItem").click(function() {
            $("#itemRows").append(createNewRow(''));
        });

        // Load 10 default data if table is empty
        if ($("#itemRows tr").length === 0) {
            defaultJabatanList.forEach(function(jabatan) {
                $("#itemRows").append(createNewRow(jabatan));
            });
        }

    // Fungsi untuk menghapus baris langsung tanpa konfirmasi
    $("#itemRows").on("click", ".delete-row", function() {
        $(this).closest('tr').remove();
    });

    // Handler untuk tombol Save Data
    $("#btnSaveData").click(function() {
        var trainingData = [];
        
        // Loop setiap baris di tabel
        $("#itemRows tr").each(function() {
            var row = $(this);
            var rowData = {
                position: row.find('td:eq(0) input').val(),
                personil: row.find('td:eq(1) input').val(),
                qualification: [],
                general: [],
                technic: []
            };

            // Ambil data qualification
            var qualIndex = 2;

            // Ambil data qualification
            @if (!empty($qualification) && is_countable($qualification))
                @foreach($qualification as $key => $val)
                    rowData.qualification.push(row.find(`td:eq(${qualIndex}) input`).is(':checked') ? "true" : "false");
                    qualIndex++;
                @endforeach
            @endif

            // Ambil data general training
            @if (!empty($general) && is_countable($general))
                @foreach($general as $key => $val)
                    rowData.general.push(row.find(`td:eq(${qualIndex}) input`).is(':checked') ? "true" : "false");
                    qualIndex++;
                @endforeach
            @endif

            // Ambil data technic training
            @if (!empty($technic) && is_countable($technic))
                @foreach($technic as $key => $val)
                    rowData.technic.push(row.find(`td:eq(${qualIndex}) input`).is(':checked') ? "true" : "false");
                    qualIndex++;
                @endforeach
            @endif

            trainingData.push(rowData);
        });

        // Kirim data ke server — gunakan JSON agar empty array [] tetap terkirim
        var doSave = function() {
            $.ajax({
                url: '{{ route("training-analyst.saveAll") }}',
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: JSON.stringify({
                    training_analyst_id: analystHeadId,
                    training_data: trainingData
                }),
                success: function(response) {
                    if(response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: response.message
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat menyimpan data'
                    });
                }
            });
        };

        // Jika semua baris dihapus, minta konfirmasi sebelum kirim
        if (trainingData.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Semua baris dihapus',
                text: 'Seluruh data akan dihapus dari database. Lanjutkan?',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus semua',
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) doSave();
            });
        } else {
            doSave();
        }
    });


})
    $(".search-list-{{$uri_key}}").keyup(delay(function(e) {
        var dInput = this.value;
        if (dInput.length > 3 || dInput.length == 0) {
            $(".current-paginate-{{$uri_key}}").val(1)
            $(".search-list-{{$uri_key}}").val(dInput)
            updateFilter()
        }
    }, 500))

    $("#manydatas-show-{{$uri_key}}").change(function(){
        $(".current-manydatas-{{$uri_key}}").val($(this).val())
        idevTable("list-{{$uri_key}}")
    });

    function updateFilter() {
        var queryParam = $("#form-filter-list-{{$uri_key}}").serialize();
        var currentHrefPdf = $("#export-pdf").attr('data-base-url')
        var currentHrefExcel = $("#export-excel").attr('data-base-url')

        $("#export-pdf").attr('href', currentHrefPdf + "?" + queryParam)
        $("#export-excel").attr('href', currentHrefExcel + "?" + queryParam)
        idevTable("list-{{$uri_key}}")
    }
</script>

@foreach($actionButtonViews as $key => $abv)
@include($abv)
@endforeach
@endpush
@endsection