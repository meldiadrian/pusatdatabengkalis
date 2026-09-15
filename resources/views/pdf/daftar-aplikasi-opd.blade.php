<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aplikasi Perangkat Daerah</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #1a1a2e;
            background: #fff;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #1e3a5f;
            padding-bottom: 10px;
            margin-bottom: 14px;
        }
        .header .instansi {
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e3a5f;
        }
        .header .judul {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a5f;
            margin-top: 3px;
        }
        .header .sub-judul {
            font-size: 9px;
            color: #555;
            margin-top: 2px;
        }
        .meta {
            font-size: 8px;
            color: #666;
            margin-bottom: 10px;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        thead tr {
            background-color: #1e3a5f;
            color: #fff;
        }
        thead th {
            padding: 6px 5px;
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            border: 1px solid #1e3a5f;
            vertical-align: middle;
        }
        tbody tr:nth-child(even) {
            background-color: #f0f4fa;
        }
        tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        tbody td {
            padding: 5px 5px;
            border: 1px solid #c8d6e5;
            font-size: 8px;
            vertical-align: top;
        }
        .td-center { text-align: center; }
        .td-no { text-align: center; width: 20px; }
        .badge {
            display: inline-block;
            padding: 1px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
        }
        .badge-aktif { background: #d1fae5; color: #065f46; }
        .badge-tidak { background: #fee2e2; color: #991b1b; }
        .badge-online { background: #dbeafe; color: #1e40af; }
        .badge-offline { background: #fef3c7; color: #92400e; }
        .footer {
            margin-top: 16px;
            font-size: 8px;
            color: #555;
            border-top: 1px solid #ccc;
            padding-top: 6px;
            display: flex;
            justify-content: space-between;
        }
        .total-info {
            font-size: 8.5px;
            color: #1e3a5f;
            margin-bottom: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="instansi">Pemerintah Kabupaten Bengkalis</div>
        <div class="instansi">Dinas Komunikasi, Informatika dan Statistik</div>
        <div class="judul">Daftar Aplikasi Perangkat Daerah</div>
        <div class="sub-judul">
            @if(!empty($unitKerja))
                {{ $unitKerja->nama_opd }} &mdash;
            @endif
            Kabupaten Bengkalis - Provinsi Riau
        </div>
    </div>

    <div class="meta">
        Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB
    </div>

    <div class="total-info">
        @if(!empty($unitKerja))
            Perangkat Daerah: {{ $unitKerja->nama_opd }} &nbsp;|&nbsp;
        @endif
        Total Data: {{ $data->count() }} Aplikasi
    </div>

    <table>
        <thead>
            <tr>
                <th class="td-no">No</th>
                <th style="width:120px;">Perangkat Daerah</th>
                <th style="width:100px;">Nama Aplikasi</th>
                <th style="width:50px;">Jenis</th>
                <th style="width:100px;">Alamat Domain</th>
                <th style="width:55px;">Spesifikasi Teknis</th>
                <th style="width:40px;">Sifat</th>
                <th style="width:30px;">Th. Buat</th>
                <th style="width:45px;">Pembuat</th>
                <th style="width:40px;">Pemilik</th>
                <th style="width:40px;">Status</th>
                <th style="width:80px;">Pemanfaatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $item)
            <tr>
                <td class="td-no">{{ $index + 1 }}</td>
                <td>{{ $item->unitKerja->nama_opd ?? '-' }}</td>
                <td>{{ $item->nama_aplikasi ?? '-' }}</td>
                <td class="td-center">{{ $item->jenis_aplikasi ?? '-' }}</td>
                <td>{{ $item->alamat_domain ?? '-' }}</td>
                <td>{{ $item->spesifikasi_teknis ?? '-' }}</td>
                <td class="td-center">
                    @if($item->mode === 'online')
                        <span class="badge badge-online">Online</span>
                    @elseif($item->mode === 'offline')
                        <span class="badge badge-offline">Offline</span>
                    @else
                        {{ $item->mode ?? '-' }}
                    @endif
                </td>
                <td class="td-center">{{ $item->tahun_penganggaran ?? '-' }}</td>
                <td class="td-center">{{ $item->pembuat ?? '-' }}</td>
                <td class="td-center">{{ $item->pemilik_aplikasi ?? '-' }}</td>
                <td class="td-center">
                    @if(strtolower($item->status) === 'aktif')
                        <span class="badge badge-aktif">Aktif</span>
                    @elseif(str_contains(strtolower($item->status ?? ''), 'tidak'))
                        <span class="badge badge-tidak">Tidak Aktif</span>
                    @else
                        {{ $item->status ?? '-' }}
                    @endif
                </td>
                <td>{{ $item->dimanfaatkan_untuk_layanan ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="12" class="td-center" style="padding: 20px; color:#888;">
                    Belum ada data aplikasi perangkat daerah.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <span>Dokumen ini digenerate secara otomatis oleh sistem.</span>
        <span>Halaman 1 dari 1</span>
    </div>
</body>
</html>
