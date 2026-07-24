@foreach($form['fields'] as $field)

    <div class="mb-3">
        <label class="form-label">
            {{ $field['label'] }}
        </label>

        <input
            type="{{ $field['type'] ?? 'text' }}"
            class="form-control"
            id="{{ $field['name'] }}"
            name="{{ $field['name'] }}"
            placeholder="{{ $field['placeholder'] ?? '' }}">
    </div>

@endforeach