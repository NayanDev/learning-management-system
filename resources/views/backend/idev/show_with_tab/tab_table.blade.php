<div class="card border">
    <div class="card-header">
        <h5>
            {{ $item['label'] ?? ucfirst($item['target']) }}
        </h5>
        <div class="card-header-right">
            @include('backend.idev.show_with_tab.tab_table_button')
        </div>
    </div>

    <div class="card-body">
        <table
            id="{{ $table['id'] }}"
            class="table table-bordered table-striped"
            data-url="{{ $table['url'] ?? '' }}"
            data-actions='{{ json_encode($table["actions"] ?? []) }}'>
            <thead></thead>
            <tbody></tbody>
        </table>
        {{-- @include('backend.idev.show_with_tab.tab_table_modal_show') --}}
    </div>

</div>