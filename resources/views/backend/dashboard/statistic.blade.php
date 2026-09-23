@php
    $allowedRoles = ['developer', 'supervisi', 'admin'];
    $user = Auth::user();
@endphp

@if(in_array($user->role->name, $allowedRoles))

    <div class="row">

        @foreach ($statistics as $stat)
            <div class="col-md-12 col-xl-3">
                <div class="card bg-{{ $stat['color'] }} order-card">
                    <div class="card-body">

                        <h5 class="text-white">
                            {{ $stat['title'] }}
                        </h5>

                        <h3 class="text-white">
                            {{ $stat['value'] }}
                        </h3>

                        <p class="m-b-0">
                            {{ $stat['description'] }}
                        </p>

                        <i class="ti {{ $stat['icon'] }} d-block f-46 card-icon text-white"></i>

                    </div>
                </div>
            </div>
        @endforeach

    </div>

@endif