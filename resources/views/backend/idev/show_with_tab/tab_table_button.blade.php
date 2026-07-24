@if(isset($table['buttons']))
    @foreach($table['buttons'] as $buttonKey)

        @php
            $button = $components['buttons'][$buttonKey];
        @endphp

        <button
            type="button"
            class="{{ $button['class'] }}"
            @if(isset($button['modal']))
                data-bs-toggle="modal"
                data-bs-target="#{{ $button['modal'] }}"
            @endif>

            {{-- @if(isset($button['icon']))
                <i class="{{ $button['icon'] }}"></i>
            @endif --}}

            {{ $button['label'] }}

        </button>

    @endforeach
@endif