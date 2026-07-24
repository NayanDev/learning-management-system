@foreach($components['modals'] as $modal)

    @switch($modal['type'] ?? null)

        @case('form')
            @include('modal.form')
        @break

        @case('view-pdf')
            @include('backend.idev.show_with_tab.modal.modal_view')
        @break

    @endswitch

@endforeach