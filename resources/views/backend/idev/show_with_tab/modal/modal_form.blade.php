@foreach($modals as $modal)

@if(($modal['type'] ?? null) == 'form')


<div 
    class="modal fade"
    id="{{ $modal['name'] }}"
>

<div class="modal-dialog {{ $modal['size'] ?? '' }}">

<div class="modal-content">


<div class="modal-header">

<h5>
{{ $modal['title'] }}
</h5>

<button 
class="btn-close"
data-bs-dismiss="modal">
</button>

</div>


<div class="modal-body">


@include(
    'backend.idev.form.render',
    [
        'form'=>$forms[$modal['form']],
        'mode'=>$modal['mode']
    ]
)


</div>


<div class="modal-footer">


@if(in_array('close',$modal['buttons']))

<button 
class="btn btn-danger"
data-bs-dismiss="modal">
Close
</button>

@endif



@if($modal['mode']=='create')

<button 
class="btn btn-success">
Save
</button>

@endif



@if($modal['mode']=='update')

<button 
class="btn btn-warning">
Update
</button>

@endif


</div>


</div>
</div>
</div>


@endif


@endforeach