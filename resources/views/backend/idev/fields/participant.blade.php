<div class="{{(isset($field['class']))?$field['class']:'form-group'}}">
    <label>{{(isset($field['label']))?$field['label']:'Label '.$key}}</label>
    <div class="field-bulktable">
        <div class="row">
            <div class="col-md-6">
                <span class="total-data-{{$field['name']}}"></span>
            </div>
            <div class="col-md-3">
                <span class="total-checked-{{$field['name']}} fw-bold">0 Checked</span>
            </div>
            <div class="col-md-3">
                <input type="text" placeholder="search..." class="form-control form-control-sm search-{{$field['name']}}">
            </div>
        </div>
        <table class="table idev-table table-responsive ajx-table-{{$field['name']}}">
            <thead>
                <tr>
                    <th>
                        # <!--input type="checkbox" class="check-all-{{$field['name']}}" value="flagall" -->
                    </th>
                    @foreach($field['table_headers'] as $header)
                    <th style="white-space: nowrap;">{{$header}}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <div class="paginate-{{$field['name']}}"></div>
        <input type="hidden" name="{{$field['name']}}" class="json-{{$field['name']}}" value="[]">
    </div>
</div>

@push('scripts')
<script>
    var ajaxUrl = "{{$field['ajaxUrl']}}"
    var primaryKey = "{{$field['key']}}"
    var stateKey = []
    $(document).ready(function() {
        getTableContent(ajaxUrl)

        $(".search-{{$field['name']}}").keyup(delay(function(e) {
            var dInput = this.value;
            if (dInput.length > 3 || dInput.length == 0) {
                getTableContent(ajaxUrl + "?search=" + dInput)
            }
        }, 500))
    });

    function getTableContent(ajaxUrl) {
        $.get(ajaxUrl, function(response) {
            var headers = response.header;
            var bodies = response.body;
            var mHtml = "";
            var intCurrentData = 0;
            
            // Buat body tabel
            $.each(bodies.data, function(index, item) {
                mHtml += "<tr>";
                mHtml += "<td><input type='checkbox' class='check-{{$field['name']}}' " +
                         "data-company='" + (item.company || '') + "' " +
                         "data-nik='" + (item.nik || '') + "' " +
                         "data-nama='" + (item.nama || '') + "' " +
                         "data-divisi='" + (item.divisi || '') + "' " +
                         "data-unit_kerja='" + (item.unit_kerja || '') + "' " +
                         "data-status='" + (item.status || '') + "' " +
                         "data-jk='" + (item.jk || '') + "' " +
                         "data-email='" + (item.email || '') + "' " +
                         "data-telp='" + (item.telp || '') + "' " +
                         "value='" + (item.nama || '') + "'></td>";
                mHtml += "<td>" + (item.nama || '') + "</td>";
                mHtml += "</tr>";
                intCurrentData++;
            });

            var paginateLink = ""
            $.each(bodies.links, function(index, link) {
                if (link.url != null && link.label != "&laquo; Previous" && link.label != "Next &raquo;") {
                    var linkActive = link.active ? "btn-primary" : "btn-outline-primary"
                    paginateLink += "<button data-url='" + link.url + "' class='btn btn-sm btn-paginate-{{$field['name']}} " + linkActive + "' type='button'>" + link.label + "</button>"
                }
            })

            $(".paginate-{{$field['name']}}").html(paginateLink)
            $(".ajx-table-{{$field['name']}} tbody").html(mHtml)

            $(".btn-paginate-{{$field['name']}}").click(function() {
                getTableContent($(this).data('url'))
            })

            $(".ajx-table-{{$field['name']}} tbody").html(mHtml);

            // Event handler untuk checkbox
            $(".check-{{$field['name']}}").change(function() {
                var checkbox = $(this);
                var participantData = {
                    company: checkbox.data('company') || '',
                    nik: checkbox.data('nik') || '',
                    nama: checkbox.data('nama') || '',
                    divisi: checkbox.data('divisi') || '',
                    unit_kerja: checkbox.data('unit_kerja') || '',
                    status: checkbox.data('status') || '',
                    jk: checkbox.data('jk') || '',
                    email: checkbox.data('email') || '',
                    telp: checkbox.data('telp') || ''
                };
                
                console.log('Checkbox changed, Participant:', participantData); // Debug

                if ($(this).prop('checked')) {
                    // Cek apakah participant sudah ada berdasarkan nik
                    var exists = stateKey.some(item => item.nik === participantData.nik);
                    if (!exists) {
                        stateKey.push(participantData);
                    }
                } else {
                    stateKey = stateKey.filter(item => item.nik !== participantData.nik);
                }

                console.log('Current stateKey:', stateKey); // Debug
                $(".json-{{$field['name']}}").val(JSON.stringify(stateKey));
                $(".total-checked-{{$field['name']}}").text(stateKey.length + " Checked");
            });

            // Check all handler
            $(".check-all-{{$field['name']}}").change(function() {
                var isChecked = $(this).prop('checked');
                stateKey = [];
                
                if (isChecked) {
                    $(".check-{{$field['name']}}").each(function() {
                        var checkbox = $(this);
                        var participantData = {
                            company: checkbox.data('company') || '',
                            nik: checkbox.data('nik') || '',
                            nama: checkbox.data('nama') || '',
                            divisi: checkbox.data('divisi') || '',
                            unit_kerja: checkbox.data('unit_kerja') || '',
                            status: checkbox.data('status') || '',
                            jk: checkbox.data('jk') || '',
                            email: checkbox.data('email') || '',
                            telp: checkbox.data('telp') || ''
                        };
                        
                        // Cek apakah participant sudah ada berdasarkan nik
                        var exists = stateKey.some(item => item.nik === participantData.nik);
                        if (!exists) {
                            stateKey.push(participantData);
                        }
                        $(this).prop('checked', true);
                    });
                } else {
                    $(".check-{{$field['name']}}").prop('checked', false);
                }

                $(".json-{{$field['name']}}").val(JSON.stringify(stateKey));
                $(".total-checked-{{$field['name']}}").text(stateKey.length + " Checked");
            });

            $(".total-data-{{$field['name']}}").text("Total : " + intCurrentData + "/" + bodies.total + " Data (s)");
        });
    }

    function removeStateKey(arr, elementToRemove) {
        return arr.filter(function(item) {
            return item !== elementToRemove;
        });
    }
</script>
@endpush