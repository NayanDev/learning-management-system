<div class="modal fade"
     id="{{ $modal['id'] }}"
     tabindex="-1"
     data-bs-backdrop="{{ $modal['backdrop'] ?? 'static' }}"
     data-bs-keyboard="{{ ($modal['keyboard'] ?? false) ? 'true' : 'false' }}">

    <div class="modal-dialog {{ $modal['dialog'] ?? 'modal-xl modal-dialog-centered' }}">

        <div class="modal-content">

            {{-- Header --}}
            <div class="modal-header">

                <h5 class="modal-title">
                    {{ $modal['title'] }}
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>

            </div>

            {{-- Body --}}
            <div class="modal-body {{ $modal['body_class'] ?? 'p-0' }}">

                @switch($modal['viewer'] ?? 'html')

                    {{-- PDF --}}
                    @case('pdf')

                        <iframe
                            src="{{ $modal['src'] }}"
                            width="100%"
                            height="{{ $modal['height'] ?? '700' }}"
                            style="border:none;">
                        </iframe>

                    @break

                    {{-- Image --}}
                    @case('image')

                        <img
                            src="{{ $modal['src'] }}"
                            class="img-fluid w-100">

                    @break

                    {{-- HTML --}}
                    @default

                        {!! $modal['content'] ?? '' !!}

                @endswitch

            </div>

        </div>

    </div>

</div>