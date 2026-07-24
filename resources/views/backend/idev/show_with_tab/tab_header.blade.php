<div class="card-header pb-0">
    <ul class="nav nav-tabs profile-tabs" id="myTab" role="tablist">

        @foreach($tabs as $key => $item)

            <li class="nav-item" role="presentation">
                <a class="nav-link {{ !empty($item['active']) ? 'active' : '' }}"
                    id="{{ $item['target'] }}-tab"
                    data-bs-toggle="tab"
                    href="#{{ $item['target'] }}"
                    role="tab"
                    aria-selected="{{ !empty($item['active']) ? 'true' : 'false' }}">

                    @if(!empty($item['icon']))
                        <i class="{{ $item['icon'] }}"></i>
                    @endif

                    {{ $item['label'] ?? ucfirst($item['target']) }}

                </a>
            </li>

        @endforeach

    </ul>
</div>