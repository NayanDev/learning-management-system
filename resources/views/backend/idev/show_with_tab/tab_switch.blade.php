@foreach($tab as $key => $item)

<div class="tab-pane {{ $key == 0 ? 'active show' : '' }}" id="{{ $item['target'] }}" role="tabpanel" aria-labelledby="{{ $item['target'] }}-tab">

    @if($item['layout'] == 'table')
        @include('backend.idev.show_with_tab.tab_table')
    @else
        @include('backend.idev.show_with_tab.tab_detail')
    @endif

</div>

@endforeach