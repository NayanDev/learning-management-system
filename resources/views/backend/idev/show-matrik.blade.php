<div class="card">
    <div class='card-body'>
        <div class="row">

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="25%">Nama</th>
                            <th width="10%">Jabatan</th>
                            <th width="10%">Divisi</th>
                            <th width="10%">Pendidikan</th>
                            <th>Nama Pelatihan</th>
                            </tr>
                    </thead>
                    <tbody>
                        @foreach ($matrik as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name ?? '-' }}</td>
                                <td>{{ $item->unit_kerja ?? '-' }}</td>
                                <td>{{ $item->divisi ?? '-' }}</td>
                                <td>{{ $item->qualification ?? '-' }}</td>
                                <td>
                                    {{ $item->attendances
                                    ->pluck('participant.event.workshop.name')
                                    ->filter()
                                    ->implode(', ') ?? '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>