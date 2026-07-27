@if(isset($table['buttons']))
    @foreach($table['buttons'] as $buttonKey)

        @php
            $button = $components['buttons'][$buttonKey] ?? null;
        @endphp

        @continue(!$button)

        {{--
            Langsung memanggil openCrudModal() via onclick agar selalu berfungsi
            meskipun tombol dirender setelah DOMContentLoaded (misalnya saat tab dibuka).
            Tidak bergantung pada event listener yang di-wire saat DOMContentLoaded.
        --}}
        <button
            type="button"
            class="{{ $button['class'] }}"
            @if(isset($button['modal']))
                onclick="(function(){
                    if (typeof window.openCrudModal === 'function') {
                        window.openCrudModal('#{{ $button['modal'] }}', { mode: 'create' });
                    } else {
                        console.error('openCrudModal belum tersedia.');
                    }
                })()"
            @endif>

            @if(isset($button['icon']))
                <i class="{{ $button['icon'] }}"></i>
            @endif

            {{ $button['label'] }}

        </button>

    @endforeach
@endif