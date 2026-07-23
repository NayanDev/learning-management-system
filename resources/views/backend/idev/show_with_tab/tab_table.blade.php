<div class="card border">

    <div class="card-header">
        <h5>{{ $item['title'] }}</h5>
        <div class="card-header-right">
            @include('backend.idev.show_with_tab.tab_table_button')
            @include('backend.idev.show_with_tab.tab_table_modal')
        </div>
    </div>

    <div class="card-body">
        <table 
            id="{{ $item['table']['id'] }}"
            class="table table-bordered table-striped">

            <thead>
                <tr>
                    @foreach($item['table']['columns'] as $column)
                        <th>
                            {{ $column }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>

        </table>
    </div>
</div>