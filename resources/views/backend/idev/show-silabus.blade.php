@php
$counting = 0;
$arrKey = [];
$da = $detail->toArray();
@endphp
@foreach ($da as $key => $d)
@if(str_contains($key, "btn_") == false)
@php
$counting++;
$arrKey[] = $key;
@endphp
@endif
@endforeach
<div class="card">
    <div class='card-body'>
        <form action="">
            <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="codeModul">Code Modul</label>
                            <input type="text" class="form-control" id="codeModul" placeholder="Enter code modul">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="duration">Duration</label>
                            <input type="text" class="form-control" id="duration" placeholder="Enter duration">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="target">Target</label>
                            <input type="text" class="form-control" id="target" placeholder="Enter target">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="qualification">Qualification</label>
                            <input type="text" class="form-control" id="qualification" placeholder="Enter qualification">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="materiId">Materi ID</label>
                            <input type="text" class="form-control" id="materiId" placeholder="Enter materi ID">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="workshopId">Workshop ID</label>
                            <input type="text" value="{{ $detail->workshopId}}" class="form-control" id="workshopId" placeholder="Enter workshop ID">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="pic">PIC</label>
                            <input type="text" class="form-control" id="pic" placeholder="Enter PIC">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="grading">Grading</label>
                            <input type="text" class="form-control" id="grading" placeholder="Enter grading">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="user">User</label>
                            <input type="text" class="form-control" id="user" placeholder="Enter user">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="manager">Manager</label>
                            <input type="text" class="form-control" id="manager" placeholder="Enter manager">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="director">Director</label>
                            <input type="text" class="form-control" id="director" placeholder="Enter director">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label" for="description">Description</label>
                            <textarea name="description" id="description" cols="30" rows="10" class="form-control" placeholder="Enter description"></textarea>
                        </div>
                    </div>

            {{ $detail }}

                @if($counting > 6)
                @php $max = round($counting/2); @endphp
                <div class="col-md-6">
                    @for($i = 0; $i < $max; $i++) <p>
                        <small>{{ str_replace("_", " ", $arrKey[$i])  }} </small><br>
                        @if ($arrKey[$i] == 'view_image')
                        <img src="{{ $da[$arrKey[$i]] }}" class='img-thumbnail img-responsive' width='120px' alt="">
                        @else
                        <b>{{ $da[$arrKey[$i]] ?? "-" }}</b>
                        @endif
                        </p>
                        @endfor
                </div>
                <div class="col-md-6">
                    @for($j = $max; $j < $counting; $j++) <p>
                        <small>{{ str_replace("_", " ", $arrKey[$j])  }} </small><br>
                        @if ($arrKey[$j] == 'view_image')
                        <img src="{{ $da[$arrKey[$j]] }}" class='img-thumbnail img-responsive' width='120px' alt="">
                        @else
                        <b>{{ $da[$arrKey[$j]] ?? "-" }}</b>
                        @endif
                        </p>
                        @endfor
                </div>
                @else
                <div class="col-md-6">
                    @for($i = 0; $i < $counting; $i++) <p>
                        <small>{{ str_replace("_", " ", $arrKey[$i])  }} </small><br>
                        @if ($arrKey[$i] == 'view_image')
                        <img src="{{ $da[$arrKey[$i]] }}" class='img-thumbnail img-responsive' width='120px' alt="">
                        @else
                        <b>{{ $da[$arrKey[$i]] ?? "-" }}</b>
                        @endif
                        </p>
                        @endfor
                </div>
                @endif

            </div>
        </form>
    </div>
</div>