<div class="modal fade"
    id="{{ $modal['id'] }}"
    tabindex="-1"
    role="dialog"
    aria-labelledby="{{ $modal['id'] }}Label"
    aria-hidden="true">

    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $modal['id'] }}Label">
                    {{ $modal['title'] ?? '' }}
                </h5>
                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
                </button>
            </div>

            <form id="{{ $modal['form'] ?? '' }}">
                <div class="modal-body">
                    @include('backend.idev.show_with_tab.tab_table_modal_form', [
                        'form' => $components['forms'][$modal['form']]
                    ])
                </div>

                <div class="modal-footer">

                    @foreach(($modal['buttons'] ?? []) as $buttonKey)

                        @php
                            $button = $components['buttons'][$buttonKey] ?? null;
                        @endphp

                        @continue(!$button)

                        <button
                            type="button"
                            class="{{ $button['class'] }}"

                            @if(isset($button['dismiss']))
                                data-bs-dismiss="{{ $button['dismiss'] }}"
                            @endif>

                            {{ $button['label'] }}

                        </button>

                    @endforeach

                </div>
            </form>
        </div>
    </div>

</div>