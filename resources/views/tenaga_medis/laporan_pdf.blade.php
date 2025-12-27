<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pemeriksaan Saya</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; }
        .header p { margin: 0; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pemeriksaan</h1>
        <p>Oleh: {{ auth()->guard('tenaga_medis')->user()->name }}</p>
        <p>Periode: {{ $periode }}</p>
    </div>

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
            @forelse ($kunjunganData as $data)
                <tr>
                    <td>{{ $data->pasien_id }}</td>
                    <td>{{ $data->nama_pasien }}</td>
                    <td>{{ $data->layanan }}</td>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align: center;">Tidak ada data untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 20px; font-size: 9px; text-align: right;">
        <p>Dicetak pada: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm:ss') }}</p>
    </div>
</body>
</html>