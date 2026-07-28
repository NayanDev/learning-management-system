<div class="row">
    {{-- {{ dd($components) }} --}}
    <!-- [ sample-page ] start -->
    <div class="col-sm-12">
        <div class="card">
            @include('backend.idev.show_with_tab.tab_header')

            <div class="card-body">
                <div class="tab-content">

                    @include('backend.idev.show_with_tab.tab_switch')

                </div>
            </div>
        </div>
    </div>
    <!-- [ sample-page ] end -->
    @include(
        'backend.idev.show_with_tab.render_modals',
        [
            'components' => $components
        ]
    )
</div>