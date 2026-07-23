@if(isset($item['buttons']))
    @foreach($item['buttons'] as $button)
        <button 
            type="button"
            class="{{ $button['class'] }}"
            data-bs-toggle="modal"
            data-bs-target="{{ $button['modal'] }}">
            {{ $button['label'] }}
        </button>
    @endforeach
@endif