<div class="col-lg-12 col-xxl-12">

    <div class="card border">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>
                        @if($detail)
                            @foreach($detail->toArray() as $key => $value)
                                @if(!str_contains($key, 'btn_'))
                                    <tr>
                                        <td width="25%">
                                            <b class="text-header">
                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                            </b>
                                        </td>
                                        <td width="5%">
                                            :
                                        </td>
                                        <td>
                                            @if($key == 'view_image' && !empty($value))
                                                <img src="{{ $value }}"
                                                    class="img-thumbnail img-responsive"
                                                    width="120px"
                                                    alt="{{ $key }}">
                                            @elseif(is_array($value))
                                                {{ json_encode($value) }}
                                            @else
                                                {{ $value ?? '-' }}
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        @else
                            <tr>
                                <td colspan="3" class="text-center">
                                    Data tidak ditemukan
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>