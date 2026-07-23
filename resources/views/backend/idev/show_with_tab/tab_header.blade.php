<div class="card-header pb-0">
    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">

        @foreach($tab as $key => $item)

            <li class="nav-item" role="presentation">
                <a class="nav-link {{ $key == 0 ? 'active' : '' }}"
                    id="{{ $item['target'] }}-tab"
                    data-bs-toggle="tab"
                    href="#{{ $item['target'] }}"
                    role="tab"
                    aria-selected="{{ $key == 0 ? 'true' : 'false' }}">

                    <i class="{{ $item['icon'] }}"></i>
                    {{ ucfirst($item['target']) }}

                </a>
            </li>

        @endforeach

    </ul>
</div>