@foreach($tabs as $key => $item)

    <div class="tab-pane {{ !empty($item['active']) ? 'active show' : '' }}"
        id="{{ $item['target'] }}"
        role="tabpanel"
        aria-labelledby="{{ $item['target'] }}-tab">

        @if(($item['layout'] ?? null) == 'table')

            @include('backend.idev.show_with_tab.tab_table', [
                'table' => $components['tables'][$item['table']]
            ])

        @else

            @include('backend.idev.show_with_tab.tab_detail')

        @endif

    </div>

@endforeach