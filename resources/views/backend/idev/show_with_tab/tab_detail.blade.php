<div class="col-lg-12 col-xxl-12">
    <div class="card border">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-borderless">
                    <tbody>

                        @foreach($detail->toArray() as $key => $value)
                            @if(!str_contains($key, 'btn_'))
                            <tr>
                                <td>
                                    <b class="text-header">
                                        {{ ucwords(str_replace('_', ' ', $key)) }}
                                    </b>
                                </td>
                                <td>:</td>
                                <td>
                                    @if($key == 'view_image')
                                        <img src="{{ $value }}"
                                            class="img-thumbnail img-responsive"
                                            width="120px"
                                            alt="">
                                    @else
                                        {{ $value ?? '-' }}
                                    @endif
                                </td>
                            </tr>
                            @endif
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>