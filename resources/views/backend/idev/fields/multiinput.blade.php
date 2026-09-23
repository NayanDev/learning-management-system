@php
    $prefix_repeatable = isset($repeatable) ? true : false;
    $preffix_method = isset($field['method']) ? $field['method'] . '_' : ''; // gunakan $field['method']
@endphp
<div class="{{ isset($field['class']) ? $field['class'] : 'form-group' }}">
    <label>{{ isset($field['label']) ? $field['label'] : 'Label ' . $key }}</label>
    <div class="{{ $preffix_method }}repeatable-sections">
        @php
            $enable_action = $field['enable_action'];
        @endphp
        <div id="{{ $preffix_method }}repeatable-0" class="row {{ $preffix_method }}field-sections">
            @foreach ($field['html_fields'] as $key2 => $child_fields)
                @php
                    $child = $child_fields;
                    $repeatable = true;
                    $child['name'] = $child['name'] . '[]';
                @endphp
                @if (View::exists('backend.idev.fields.' . $child['type']))
                    @include('backend.idev.fields.' . $child['type'], ['field' => $child])
                @else
                    @include('easyadmin::backend.idev.fields.' . $child['type'], ['field' => $child])
                @endif
            @endforeach

            @if ($enable_action)
                <div class="col-md-1 remove-section">
                    <button type='button' class='btn btn-sm btn-circle btn-danger my-4 text-white'
                        onclick='remove("{{ $preffix_method }}",0)'>
                        <i class='ti ti-minus'></i>
                    </button>
                </div>
            @endif
        </div>
    </div>

    @if ($enable_action)
        <div class="row">
            <div class="col-md-4">
                {{-- Passing 'this' agar add() bisa scope ke container yang benar --}}
                <button type="button" class="btn btn-sm btn-secondary my-2 text-white"
                    onclick="add('{{ $preffix_method }}', this)">
                    <i class="fa fa-plus"></i> +
                </button>
            </div>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        /**
         * MULTIINPUT REPEATABLE
         *
         * add():
         *  - Debounce 300ms per method
         *  - Scope container berdasarkan tombol
         *  - Template selalu row pertama
         *  - ID unik
         *  - Remove button menunjuk ke row sendiri
         *
         * remove():
         *  - Hapus row berdasarkan ID
         *  - Minimal 1 row
         *
         * General Auto Fill:
         *  - General otomatis menjadi 4 row
         *  - Nilai:
         *      1. Organisasi Perusahaan
         *      2. Peraturan Perusahaan
         *      3. CPOB Dasar
         *      4. Regulasi Pemerintah
         *
         * IMPORTANT:
         *  - Hanya input[name="general[]"] yang diisi.
         *  - training_id TIDAK disentuh.
         */

        // Guard: inisialisasi sekali meskipun file di-include beberapa kali
        if (typeof window._multiAddLock === 'undefined') {
            window._multiAddLock = {};
        }


        /**
         * ============================================================
         * ADD
         * ============================================================
         */
        function add(preffixMethod, btn) {

            // Debounce: cegah double-click
            if (window._multiAddLock[preffixMethod]) return;

            window._multiAddLock[preffixMethod] = true;

            setTimeout(function() {
                delete window._multiAddLock[preffixMethod];
            }, 300);


            // Scope container ke offcanvas/form yang sama
            var $container;

            if (btn) {
                $container = $(btn)
                    .closest('.offcanvas-body, .offcanvas, form')
                    .find('.' + preffixMethod + 'repeatable-sections')
                    .first();
            }


            // Fallback
            if (!$container || !$container.length) {
                $container = $('.' + preffixMethod + 'repeatable-sections').first();
            }

            if (!$container.length) return;


            // Template = row pertama
            var $firstRow = $container
                .children('.' + preffixMethod + 'field-sections')
                .first();

            if (!$firstRow.length) return;


            var $clone = $firstRow.clone();


            // ========================================================
            // KOSONGKAN CLONE
            //
            // Jangan menyentuh training_id karena training_id
            // bisa berada di luar repeatable atau berada di dalam form.
            // ========================================================

            $clone.find(
                'input:not([type="radio"]):not([type="checkbox"]):not([name="training_id"]), textarea, select'
            ).val('');

            $clone.find(
                'input[type="radio"]:not([name="training_id"]), ' +
                'input[type="checkbox"]:not([name="training_id"])'
            ).prop('checked', false);


            // ========================================================
            // ID UNIK
            // ========================================================

            var uniqueId =
                Date.now() + '_' + Math.floor(Math.random() * 9999);

            $clone.attr(
                'id',
                preffixMethod + 'repeatable-' + uniqueId
            );


            // ========================================================
            // REMOVE BUTTON
            // ========================================================

            var htmlRemove =
                "<button type='button' " +
                "class='btn btn-sm btn-circle btn-danger my-4 text-white' " +
                "onclick='remove(\"" +
                preffixMethod +
                "\",\"" +
                uniqueId +
                "\")'>" +
                "<i class=\"ti ti-minus\"></i>" +
                "</button>";

            $clone.find('.remove-section').html(htmlRemove);


            // Tambahkan row
            $container.append($clone);
        }


        /**
         * ============================================================
         * REMOVE
         * ============================================================
         */
        function remove(preffixMethod, index) {

            var $row = $(
                '#' + preffixMethod + 'repeatable-' + index
            );

            if (!$row.length) return;


            // Cari container row tersebut
            var $container = $row.closest(
                '.' + preffixMethod + 'repeatable-sections'
            );


            // Minimal 1 row
            if (
                $container.children(
                    '.' + preffixMethod + 'field-sections'
                ).length <= 1
            ) {
                return;
            }


            $row.remove();
        }


        /**
         * ============================================================
         * RESET CREATE OFFCANVAS
         * ============================================================
         */
        @if ($enable_action)

            $(document).on(
                'show.bs.offcanvas',
                '[id^="createForm-"]',
                function() {

                    var $offcanvas = $(this);

                    var $container = $offcanvas.find(
                        '.{{ $preffix_method }}repeatable-sections'
                    );

                    if (!$container.length) return;


                    // ====================================================
                    // Hapus semua row tambahan
                    // ====================================================

                    $container
                        .children(
                            '.{{ $preffix_method }}field-sections'
                        )
                        .not(':first')
                        .remove();


                    // ====================================================
                    // Kosongkan row pertama
                    //
                    // training_id TIDAK disentuh
                    // ====================================================

                    $container.find(
                        'input:not([type="radio"]):not([type="checkbox"]):not([name="training_id"]), ' +
                        'textarea:not([name="training_id"]), ' +
                        'select:not([name="training_id"])'
                    ).val('');


                    $container.find(
                        'input[type="radio"]:not([name="training_id"]), ' +
                        'input[type="checkbox"]:not([name="training_id"])'
                    ).prop('checked', false);
                });


            /**
             * ============================================================
             * GENERAL AUTO FILL
             * ============================================================
             *
             * Hanya dijalankan untuk multiinput dengan prefix general_
             *
             * Row:
             * 0 = Organisasi Perusahaan
             * 1 = Peraturan Perusahaan
             * 2 = CPOB Dasar
             * 3 = Regulasi Pemerintah
             *
             * training_id tidak disentuh.
             */
            $(document).on(
                'shown.bs.offcanvas',
                '[id^="createForm-"]',
                function() {

                    var $offcanvas = $(this);

                    var prefix = 'general_';

                    var items = [
                        'Organisasi Perusahaan',
                        'Peraturan Perusahaan',
                        'CPOB Dasar',
                        'Regulasi Pemerintah'
                    ];


                    // Cari container GENERAL saja
                    var $container = $offcanvas.find(
                        '.' + prefix + 'repeatable-sections'
                    );

                    if (!$container.length) return;


                    // Cari tombol + GENERAL
                    var $addButton = $offcanvas
                        .find('button')
                        .filter(function() {

                            var onclick = $(this).attr('onclick');

                            return onclick &&
                                onclick.indexOf(
                                    "add('" + prefix
                                ) !== -1;
                        })
                        .first();


                    if (!$addButton.length) return;


                    // ====================================================
                    // Pastikan row pertama tersedia
                    // ====================================================

                    var $rows = $container.children(
                        '.' + prefix + 'field-sections'
                    );

                    if (!$rows.length) return;

                    // ====================================================
                    // Fungsi isi GENERAL
                    //
                    // HANYA general[]
                    // ====================================================

                    function fillGeneralRow($row, value) {
                        $row.find(
                            'input[name="general[]"], ' +
                            'textarea[name="general[]"], ' +
                            'select[name="general[]"]'
                        ).first().val(value);
                    }

                    // ====================================================
                    // Isi row pertama
                    // ====================================================
                    fillGeneralRow(
                        $rows.eq(0),
                        items[0]
                    );

                    // ====================================================
                    // Tambahkan row berikutnya
                    // ====================================================
                    var index = 1;

                    function addNextGeneral() {

                        if (index >= items.length) {
                            return;
                        }

                        // Klik tombol +
                        $addButton.trigger('click');

                        // Tunggu add() selesai
                        setTimeout(function() {
                            var $newRows = $container.children(
                                '.' + prefix + 'field-sections'
                            );
                            var $newRow = $newRows.last();
                            if (!$newRow.length) return;

                            // Isi HANYA general[]
                            fillGeneralRow(
                                $newRow,
                                items[index]
                            );
                            index++;

                            // Tunggu debounce add() 300ms
                            setTimeout(
                                addNextGeneral,
                                350
                            );
                        }, 50);
                    }

                    // Mulai tambah row kedua
                    setTimeout(
                        addNextGeneral,
                        350
                    );
                });
        @endif
    </script>
@endpush
