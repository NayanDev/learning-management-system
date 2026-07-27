@foreach($components['modals'] as $modal)

    @switch($modal['type'] ?? null)

        @case('form')
            @include('backend.idev.show_with_tab.modal.modal_form_jobdesc', [
                'modal'   => $modal,
                'form'    => $components['forms'][$modal['form']] ?? [],
                'buttons' => $components['buttons'] ?? [],
            ])
        @break

        @case('view-pdf')
            @include('backend.idev.show_with_tab.modal.modal_view')
        @break

    @endswitch

@endforeach