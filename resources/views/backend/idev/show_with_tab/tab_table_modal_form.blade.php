@foreach($modal['form'] as $field)
    <div class="mb-3">
        <label class="form-label">
            {{ $field['label'] }}
        </label>

        <input
            type="{{ $field['type'] }}"
            class="form-control"
            id="{{ $field['name'] }}"
            name="{{ $field['name'] }}"
            placeholder="{{ $field['placeholder'] }}">
    </div>
@endforeach