@php 
$prefix_repeatable = (isset($repeatable))? true : false;
$preffix_method = (isset($field['method']))? $field['method']."_": ""; // gunakan $field['method']
@endphp
<div class="{{(isset($field['class']))?$field['class']:'form-group'}}">
    <label>{{(isset($field['label']))?$field['label']:'Label '.$key}}</label>
    <div class="{{$preffix_method}}repeatable-sections">
        @php 
        $enable_action = $field['enable_action'];
        @endphp
        <div id="{{$preffix_method}}repeatable-0" class="row {{$preffix_method}}field-sections">
            @foreach($field['html_fields'] as $key2 => $child_fields)
            @php
                $child = $child_fields; $repeatable = true; $child['name'] = $child['name'].'[]';
            @endphp
            @if (View::exists('backend.idev.fields.'.$child['type']))
                @include('backend.idev.fields.'.$child['type'], ['field' => $child])
            @else
                @include('easyadmin::backend.idev.fields.'.$child['type'], ['field' => $child])
            @endif
            @endforeach

            @if($enable_action)
            <div class="col-md-1 remove-section">
                <button type='button' class='btn btn-sm btn-circle btn-danger my-4 text-white' onclick='remove("{{$preffix_method}}",0)'>
                    <i class='ti ti-minus'></i>
                </button>
            </div>
            @endif
        </div>
    </div>

    @if($enable_action)
    <div class="row">
        <div class="col-md-4">
            <button type="button" class="btn btn-sm btn-secondary my-2 text-white" onclick="add('{{$preffix_method}}')">
                <i class="fa fa-plus"></i> +
            </button>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
    function add(preffixMethod) {
        var epochSeconds = Math.floor(Date.now()/1000);

        // target hanya container yang benar (direct child saja)
        var $container = $('.'+preffixMethod+'repeatable-sections');
        var $lastRow = $container.children('.'+preffixMethod+'field-sections').last();

        $lastRow.attr('id', preffixMethod+'repeatable-'+epochSeconds);

        var $clone = $lastRow.clone();
        $clone.find('input:not([type="radio"]):not([type="checkbox"]), textarea, select').val('');
        $clone.find('input[type="radio"], input[type="checkbox"]').prop('checked', false);

        $clone.appendTo($container);

        var htmlRemove = "<button type='button' class='btn btn-sm btn-circle btn-danger my-4 text-white' onclick='remove(\""+preffixMethod+"\","+epochSeconds+")'><i class=\"ti ti-minus\"></i></button>";
        $clone.find('.remove-section').html(htmlRemove);
    }

    function remove(preffixMethod, index) {
        $("#"+preffixMethod+"repeatable-"+index).remove();
    }
</script>
@endpush