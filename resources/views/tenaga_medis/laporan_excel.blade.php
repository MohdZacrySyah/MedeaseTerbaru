<table>
    <thead>
        <tr>
            <th>ID Pasien</th>
            <th>Nama Pasien</th>
            <th>Layanan</th>
            <th>Tanggal Pemeriksaan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kunjunganData as $data)
            <tr>
                <td>{{ $data->pasien_id }}</td>
                <td>{{ $data->nama_pasien }}</td>
                <td>{{ $data->layanan }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->format('Y-m-d H:i:s') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>