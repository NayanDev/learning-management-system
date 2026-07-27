<div class="card border">
    <div class="card-header">
        <h5>
            {{ $item['label'] ?? ucfirst($item['target']) }}
        </h5>
        <div class="card-header-right">
            {{-- @include('backend.idev.show_with_tab.tab_table_button') --}}

            <button class="btn btn-danger" onclick="openJobdescModal()">
                + Jobdesc
            </button>


        </div>
    </div>

    <div class="card-body">
        @php
            $tableUrl = $table['url'] ?? '';
            if (!empty($detail->id)) {
                $tableUrl .= (str_contains($tableUrl, '?') ? '&' : '?') . 'section_id=' . $detail->id;
            }
            // Konversi ke URL absolut agar fetch dari JS selalu benar
            if ($tableUrl && !str_starts_with($tableUrl, 'http')) {
                $tableUrl = url($tableUrl);
            }

            // Konversi URL di setiap action ke absolute URL
            $actions = $table['actions'] ?? [];
            foreach ($actions as $key => $action) {
                if (!empty($action['url']) && !str_starts_with($action['url'], 'http')) {
                    $actions[$key]['url'] = url($action['url']);
                }
            }
        @endphp
        <table
            id="{{ $table['id'] }}"
            class="table table-bordered table-striped"
            data-url="{{ $tableUrl }}"
            data-section-id="{{ $detail->id ?? '' }}"
            data-actions='{{ json_encode($actions) }}'>
            <thead></thead>
            <tbody></tbody>
        </table>
    </div>

</div>