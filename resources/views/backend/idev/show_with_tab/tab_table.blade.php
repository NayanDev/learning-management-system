<div class="card border">

    <div class="card-header">
        <h5>
            {{ $item['label'] ?? ucfirst($item['target']) }}
        </h5>
        <div class="card-header-right">
            @include('backend.idev.show_with_tab.tab_table_button')
            @if(isset($table['modal']) && isset($components['modals'][$table['modal']]))
                @include('backend.idev.show_with_tab.tab_table_modal', [
                    'modal' => $components['modals'][$table['modal']]
                ])
            @endif
        </div>
    </div>

    <div class="card-body">
        <table
            id="{{ $table['id'] }}"
            class="table table-bordered table-striped"
            data-url="{{ $table['url'] ?? '' }}">
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

</div>