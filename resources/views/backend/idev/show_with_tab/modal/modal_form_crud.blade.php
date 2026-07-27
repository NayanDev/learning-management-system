{{--
    ┌──────────────────────────────────────────────────────────────────────────┐
    │  Reusable CRUD Form Modal  (create + update, single modal)               │
    │                                                                          │
    │  Variables injected by render_modals.blade.php:                          │
    │    $modal   – modal config array                                         │
    │    $form    – form config array (key, fields)                            │
    │    $buttons – all page buttons keyed by button key                       │
    │                                                                          │
    │  JS API (called from DataTable actions or other code):                   │
    │    openCrudModal('#jobdesc-form', { mode: 'create' });                   │
    │    openCrudModal('#jobdesc-form', { mode: 'edit', id: 5,                 │
    │        data: { name: 'foo', section_id: 2 } });                          │
    └──────────────────────────────────────────────────────────────────────────┘
--}}

@php
    $modalId   = $modal['id']    ?? $modal['key'];
    $baseTitle = $modal['title'] ?? 'Form';
    $baseUrl   = $modal['url']   ?? '#';
    // Proses URL: jika dimulai dengan '/' atau relatif, konversi ke URL absolut
    $baseUrl   = ($baseUrl && $baseUrl !== '#') ? url($baseUrl) : $baseUrl;
    $dialog    = $modal['dialog']   ?? 'modal-dialog-centered';
    $backdrop  = $modal['backdrop'] ?? 'static';
    $keyboard  = ($modal['keyboard'] ?? false) ? 'true' : 'false';
    $saveLabel = $buttons['save']['label'] ?? 'Save';
@endphp

<div class="modal fade"
     id="{{ $modalId }}"
     tabindex="-1"
     data-bs-backdrop="{{ $backdrop }}"
     data-bs-keyboard="{{ $keyboard }}"
     data-base-url="{{ $baseUrl }}"
     data-base-title="{{ $baseTitle }}"
     aria-labelledby="{{ $modalId }}Label"
     aria-hidden="true">

    <div class="modal-dialog {{ $dialog }}">
        <div class="modal-content">

            {{-- ── Header ───────────────────────────────────────────────── --}}
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modalId }}Label">{{ $baseTitle }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- ── Form ────────────────────────────────────────────────── --}}
            <form id="{{ $modalId }}-form" action="{{ $baseUrl }}" enctype="multipart/form-data" novalidate>
                @csrf

                {{-- Spoofed HTTP method (POST for create, POST+_method=PUT for update) --}}
                <input type="text" name="_method"    id="{{ $modalId }}-_method"    value="POST">
                {{-- section_id injected by openCrudModal() from data-section-id on the table --}}
                <input type="text" name="section_id" id="{{ $modalId }}-section_id" value="">

                <div class="modal-body">

                    @foreach(($form['fields'] ?? []) as $field)
                        <div class="mb-3">
                            <label class="form-label fw-semibold"
                                    for="{{ $modalId }}-{{ $field['name'] }}">
                                {{ $field['label'] }}
                                @if($field['type'] !== 'file')<span class="text-danger">*</span>@endif
                            </label>
                            <input
                                type="{{ $field['type'] ?? 'text' }}"
                                class="form-control"
                                id="{{ $modalId }}-{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                placeholder="{{ $field['placeholder'] ?? 'Enter ' . $field['label'] }}"
                                @if($field['type'] !== 'file') required @endif
                                @if($field['type'] === 'file') accept=".pdf" @endif
                            >
                            {{-- File: show current filename on edit --}}
                            @if($field['type'] === 'file')
                                <small class="text-muted" id="{{ $modalId }}-{{ $field['name'] }}-hint"></small>
                            @endif
                        </div>
                    @endforeach

                </div>

                <div class="modal-footer">

                    @foreach(($modal['buttons'] ?? []) as $buttonKey)
                        @php $btn = $buttons[$buttonKey] ?? null; @endphp
                        @continue(!$btn)

                        @if(($btn['type'] ?? '') === 'submit')
                            <button type="submit"
                                    class="{{ $btn['class'] }}"
                                    id="{{ $modalId }}-btn-{{ $buttonKey }}">
                                @if(!empty($btn['icon']))<i class="{{ $btn['icon'] }}"></i> @endif
                                <span id="{{ $modalId }}-btn-label">{{ $btn['label'] }}</span>
                            </button>
                        @else
                            <button type="button"
                                    class="{{ $btn['class'] }}"
                                    id="{{ $modalId }}-btn-{{ $buttonKey }}"
                                    @if(isset($btn['dismiss'])) data-bs-dismiss="{{ $btn['dismiss'] }}" @endif>
                                @if(!empty($btn['icon']))<i class="{{ $btn['icon'] }}"></i> @endif
                                {{ $btn['label'] }}
                            </button>
                        @endif

                    @endforeach

                </div>
            </form>

        </div>
    </div>
</div>

