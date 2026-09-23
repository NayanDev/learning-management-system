@extends("easyadmin::backend.parent")
@section("content")
@push('mtitle')
{{$title}}
@endpush

@push('styles')
    <style>
        .table-responsive {
            overflow-x: auto;
            position: relative;
        }

        #table-list-{{$uri_key}} th.col-action,
        #table-list-{{$uri_key}} td.col-action {
            position: sticky;
            right: 0;
            background-color: #fff; 
            z-index: 2; 
            box-shadow: -5px 0 5px -5px rgba(0,0,0,0.15);
        }

        #table-list-{{$uri_key}} tr:hover td.col-action {
            background-color: #f8f9fa;
        }

        #table-list-{{$uri_key}} th.col-action {
            background-color: #fff; 
            z-index: 3;
        }

        .pending-approval-badge-tw {
            background: rgba(255, 193, 7, 0.12);
            border: 1.5px solid rgba(255, 193, 7, 0.5);
            box-shadow: 0 0 12px rgba(255, 193, 7, 0.2);
            animation: pulse-border-tw 2s ease-in-out infinite;
        }

        @keyframes pulse-border-tw {
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

<div class="pc-container" id="section-list-{{$uri_key}}">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                @if(isset($headerLayout))
                    @include('backend.idev.parts.'.$headerLayout.'')
                @else
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <span class="count-total-list-{{$uri_key}} float-end mt-2">0 Data</span>
                        </div>
                        @if (in_array('create', $permissions))
                        <a class="btn btn-secondary float-end text-white mx-1" data-bs-toggle="offcanvas" data-bs-target="#createForm-{{$uri_key}}">
                            Create
                        </a>
                        @endif

                        <a href="#" class="btn btn-success shadow-sm mx-1 float-end" data-bs-toggle="modal" data-bs-target="#addWorkshopModal">
                            Add Workshop
                        </a>

                        @php
                            $trainingNeedRecord = request('training_need')
                                ? \App\Models\TrainingNeed::find(request('training_need'))
                                : null;
                            $managerDivisi = strtolower(Auth::user()->position) == 'manager' && $trainingNeedRecord->divisi == Auth::user()->divisi;
                            $creator = $trainingNeedRecord->user_id == Auth::user()->id;
                            $managerHR = in_array(
                                strtolower(trim(Auth::user()->position)),
                                ['manager', 'asman']
                            );
                            $developer = strtolower(Auth::user()->role->name) == 'developer';
                        @endphp

                        @if($managerDivisi || $creator || $managerHR || $developer)
                            <a href="{{ route('training-need.pdf') .  '?training_id=' . request('training_need') }}" class="btn btn-danger float-end text-white mx-1">
                                Lihat Dokumen 
                            </a>
                        @endif

                        @php
                            $trainingNeedRecord = request('training_need')
                                ? \App\Models\TrainingNeed::find(request('training_need'))
                                : null;
                            $authUser = Auth::user();
                            $developer = strtolower(Auth::user()->role->name) == 'developer';
                        @endphp

                        @if($trainingNeedRecord)
                            @if($trainingNeedRecord->status === 'open')
                                @if($developer || $creator)
                                    <form action="{{ route('training-need.submit', $trainingNeedRecord->id) }}" method="POST" class="d-inline" id="formSubmitTrainingNeed">
                                        @csrf
                                        @method('PATCH')
                                        <button type="button" class="btn btn-warning shadow-sm mx-1 float-end" id="btnSubmitTrainingNeed">
                                            <i class="ti ti-send f-20"></i> Submit to Manager
                                        </button>
                                    </form>
                                @endif
                            @elseif($trainingNeedRecord->status === 'submit')
                                @if(
                                    strtolower($authUser->position) === 'manager' &&
                                    strtoupper($authUser->divisi) === $trainingNeedRecord->divisi
                                )
                                    <button
                                        type="button"
                                        class="btn btn-primary shadow-sm mx-1 float-end"
                                        id="btnApproveTrainingNeed"
                                        data-id="{{ $trainingNeedRecord->id }}">
                                        <i class="ti ti-shield-check f-20"></i> Approve
                                    </button>
                                @else
                                    <div class="d-flex flex-column align-items-end gap-1 mx-1 float-end">
                                        <div class="pending-approval-badge-tw d-flex align-items-center gap-2 px-3 py-2 rounded-3">
                                            <span class="spinner-grow spinner-grow-sm text-warning" role="status" aria-hidden="true"></span>
                                            <span class="fw-semibold text-warning small">Menunggu Approval Manager</span>
                                        </div>
                                    </div>
                                @endif
                            @elseif($trainingNeedRecord->status === 'approve')
                                <button class="btn btn-success shadow-sm mx-1 float-end" disabled>
                                    <i class="ti ti-check f-20"></i> Approved
                                </button>
                            @endif
                        @endif

                        @include('backend.idev.workshop.modal')
                    </div>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="col-8 col-md-6">
                                @foreach ($more_actions as $ma)
                                @if (isset($ma['key']) && in_array($ma['key'], $permissions))
                                {!! $ma['html_button'] !!}
                                @endif
                                @endforeach
                            </div>
                            <div class="col-4 col-md-6">
                            </div>
                            <div class="col-md-12">
                                <form id="form-filter-list-{{$uri_key}}" action="{{$uri_list_api}}" method="get">
                                    <div class="row my-3">
                                        <div class="col-md-2">
                                            <small for="">Search</small>
                                            <div class="form-group">
                                                <input class="form-control search-list-{{$uri_key}}" name="search" placeholder="Type for search...">
                                                <input type="hidden" name="route_name" class="route-name-{{$uri_key}}" value="{{$uri_key}}">
                                                <input type="hidden" name="page" class="current-paginate-{{$uri_key}}">
                                                <input type="hidden" name="order" class="current-order-{{$uri_key}}">
                                                <input type="hidden" name="manydatas" class="current-manydatas-{{$uri_key}}" value="10">
                                                <input type="hidden" name="order_state" class="current-order-state-{{$uri_key}}" value="ASC">
                                            </div>
                                        </div>
                                        @if(isset($filters))
                                        @foreach ($filters as $key => $filter)
                                            @if (View::exists('backend.idev.filters.'.$filter['type']))
                                                @include('backend.idev.filters.'.$filter['type'])
                                            @else
                                                @include('easyadmin::backend.idev.filters.'.$filter['type'])
                                            @endif
                                        @endforeach
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="table-responsive p-0">
                            <table id="table-list-{{$uri_key}}" class="table table-hover">
                                <thead>
                                    <tr>
                                        @foreach($table_headers as $header)
                                        @php
                                        $header_name = $header['name'];
                                        $header_column = $header['column'];
                                        @endphp
                                        @if($header['order'])
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="white-space: nowrap;">{{$header_name}}
                                            <button class="btn btn-sm btn-link" onclick="orderBy('list-{{$uri_key}}','{{$header_column}}')"><i class="ti ti-arrow-up"></i></button>
                                        </th>
                                        @else
                                        <th style="white-space: nowrap;">{{$header_name}}
                                        </th>
                                        @endif
                                        @endforeach
                                        <th class="col-action"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-1 col-lg-1 col-2">
                                    <select class="form-control form-control-sm" id="manydatas-show-{{$uri_key}}">
                                        @foreach(['10', '20', '50', '100', 'All'] as $key => $showData)
                                        <option value="{{$showData}}">{{$showData}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-11">
                                    <div id="paginate-list-{{$uri_key}}"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="pc-container" id="section-preview-{{$uri_key}}" style="display:none;">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                           <b>Detail {{$title}}</b> 
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger float-end close-preview">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-1">
            <div class="col-md-12 content-preview"></div>
        </div>
    </div>
</div>

@push('styles')
@if(isset($import_styles))
@foreach($import_styles as $ist)
<link rel="stylesheet" href="{{$ist['source']}}">
@endforeach
@endif
@endpush

@push('scripts')
@if (in_array('create', $permissions))
<div class="offcanvas offcanvas-end @if(isset($drawerExtraClass)) {{ $drawerExtraClass }} @endif" tabindex="-1" id="createForm-{{$uri_key}}" aria-labelledby="createForm-{{$uri_key}}">
    <div class="offcanvas-header border-bottom bg-secondary p-4">
        <h5 class="text-white m-0">Create New</h5>
        <button type="button" class="btn-close text-white text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <form id="form-create-{{$uri_key}}" action="{{$url_store}}" method="post">
            @csrf
            <div class="row">
                @php $method = "create"; @endphp
                @foreach($fields as $key => $field)
                @if (View::exists('backend.idev.fields.'.$field['type']))
                    @include('backend.idev.fields.'.$field['type'])
                @else
                    @include('easyadmin::backend.idev.fields.'.$field['type'])
                @endif
                @endforeach
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group my-2">
                        <button id="btn-for-form-create-{{$uri_key}}" type="button" class="btn btn-outline-secondary" onclick="softSubmit('form-create-{{$uri_key}}', 'list-{{$uri_key}}')">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@if(isset($import_scripts))
@foreach($import_scripts as $isc)
<script src="{{$isc['source']}}"></script>
@endforeach
@endif
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        if ($(".idev-actionbutton").children().length == 0) {
            $("#dropdownMoreTopButton").remove()
            $(".idev-actionbutton").remove()
        }
        idevTable("list-{{$uri_key}}")
        $('form input').on('keypress', function(e) {
            return e.which !== 13;
        });

        // Submit to Manager
        var btnSubmit = document.getElementById('btnSubmitTrainingNeed');
        if (btnSubmit) {
            btnSubmit.addEventListener('click', function() {
                Swal.fire({
                    title: 'Submit ke Manager?',
                    text: 'Data akan disubmit ke Manager dan QR Code akan dibuat otomatis.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#f0ad4e',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Submit!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btnSubmit.disabled = true;
                        btnSubmit.innerHTML = '<i class="ti ti-loader"></i> Memproses...';
                        document.getElementById('formSubmitTrainingNeed').submit();
                    }
                });
            });
        }

        // Approve by Manager
        var btnApprove = document.getElementById('btnApproveTrainingNeed');
        if (btnApprove) {
            btnApprove.addEventListener('click', function() {
                var trainingNeedId = btnApprove.getAttribute('data-id');
                Swal.fire({
                    title: 'Approve Rencana Training?',
                    text: 'Status akan diubah menjadi Approved dan QR Code akan dibuat otomatis.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btnApprove.disabled = true;
                        btnApprove.innerHTML = '<i class="ti ti-loader"></i> Memproses...';
                        fetch('/training-need/' + trainingNeedId + '/approve', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-HTTP-Method-Override': 'PATCH'
                            },
                            body: JSON.stringify({
                                status: 'approve',
                                approve_by: {{ Auth::id() }},
                                _method: 'PATCH'
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status) {
                                Swal.fire({
                                    title: 'Berhasil!',
                                    text: 'Data berhasil di-approve.',
                                    icon: 'success',
                                    confirmButtonColor: '#0d6efd',
                                    timer: 2000,
                                    timerProgressBar: true
                                }).then(() => { window.location.reload(); });
                            } else {
                                Swal.fire('Gagal!', data.message || 'Terjadi kesalahan.', 'error');
                                btnApprove.disabled = false;
                                btnApprove.innerHTML = '<i class="ti ti-shield-check f-20"></i> Approve';
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error!', 'Terjadi kesalahan jaringan.', 'error');
                            btnApprove.disabled = false;
                            btnApprove.innerHTML = '<i class="ti ti-shield-check f-20"></i> Approve';
                        });
                    }
                });
            });
        }
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