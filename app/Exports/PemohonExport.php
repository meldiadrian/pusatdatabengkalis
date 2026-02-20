<?php

namespace App\Exports;

use App\Models\Pemohon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PemohonExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Pemohon::select(
            'instansi_pemohon',
            'data_diminta',
            'tujuan_penggunaan',
            'opd_tujuan',
            'upload_surat',
            'created_at'
        )->get();
    }

    public function headings(): array
    {
        return [
            'Instansi Pemohon',
            'Data Yang Dibutuhkan',
            'Tujuan Penggunaan',
            'Instansi Tujuan',
            'File Upload',
            'Tanggal Dibuat',
        ];
    }
}
