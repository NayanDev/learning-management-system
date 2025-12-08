@php
$prefix_repeatable = (isset($repeatable))? true : false;
$select_id = (isset($field['name']))?$field['name']:'id_'.$key;
$select_name = (isset($field['name']))?$field['name']:'name_'.$key;
$preffix_method = (isset($method))? $method."_": "";
@endphp
<div class="{{(isset($field['class']))?$field['class']:'form-group'}}">
    <label>{{(isset($field['label']))?$field['label']:'Label '.$key}}
        @if(isset($field['required']) && $field['required'])
        <small class="text-danger">*</small>
        @endif
    </label>
    <select 
        id="{{$preffix_method}}{{$select_id}}" 
        name="{{$select_name}}" 
        class="form-control idev-form support-live-select2 @if($prefix_repeatable) field-repeatable @endif"
        @if(isset($field['data-target'])) 
        data-target="{{$field['data-target']}}"
        @endif>
        <option value="">-- Select Employee --</option>
        @foreach($field['options'] as $key => $opt)
        <option value="{{$opt['value']}}" 
            @if($opt['value'] == $field['value'] || $opt['value'] == request($select_name)) selected @endif
            data-email="{{$opt['email'] ?? ''}}"
            data-name="{{$opt['name'] ?? ''}}"
            data-company="{{$opt['company'] ?? ''}}"
            data-divisi="{{$opt['divisi'] ?? ''}}"
            data-unit-kerja="{{$opt['unit_kerja'] ?? ''}}"
            data-status="{{$opt['status'] ?? ''}}"
            data-jk="{{$opt['jk'] ?? ''}}"
            data-telp="{{$opt['telp'] ?? ''}}"
        >{{$opt['text']}}</option>
        @endforeach
    </select>
</div>

@if(isset($field['filter']) || isset($field['autofill']))
@push('scripts')
<script>
$(document).ready(function() {
    // Filter functionality
    @if(isset($field['filter']))
    var currentUrl = "{{url()->current()}}";
    $('#{{$select_id}}').on('change', function() {
        if (currentUrl.includes("?")) {
            currentUrl += "&{{$select_name}}="+$(this).val();
        } else {
            currentUrl += "?{{$select_name}}="+$(this).val();
        }
        window.location.replace(currentUrl);
    });
    @endif

    // Autofill functionality
    @if(isset($field['autofill']) && $field['autofill'])
    $('#{{$preffix_method}}{{$select_id}}').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        
        // Get all data attributes
        var formData = {
            name: selectedOption.data('name'),
            email: selectedOption.data('email'),
            company: selectedOption.data('company'),
            divisi: selectedOption.data('divisi'),
            unit_kerja: selectedOption.data('unit-kerja'),
            status: selectedOption.data('status'),
            jk: selectedOption.data('jk'),
            telp: selectedOption.data('telp')
        };

        // Debug log
        console.log('Selected employee data:', formData);

        // Loop through all fields and set values
        Object.keys(formData).forEach(function(field) {
            var value = formData[field] || '';
            var targetField = $('#{{$preffix_method}}' + field);
            
            if(targetField.length > 0) {
                targetField.val(value);
                
                // Trigger change event for select2 fields
                if(targetField.hasClass('support-live-select2')) {
                    targetField.trigger('change');
                }
            }
        });
    });

    // Initialize select2
    $('.support-live-select2').select2({
        width: '100%',
        allowClear: true,
        placeholder: 'Select an option'
    });

    // Trigger change if option is pre-selected
    if ($('#{{$preffix_method}}{{$select_id}}').val()) {
        $('#{{$preffix_method}}{{$select_id}}').trigger('change');
    }
    @endif
});
</script>
@endpush
@endif