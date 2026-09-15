<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DaftarAplikasiPerangkatDaerah;
use App\Models\UnitKerja;
use App\Models\WebsitePerangkatDaerah;

class PdfExportController extends Controller
{
    /**
     * Download PDF Daftar Aplikasi Perangkat Daerah (hanya OPD).
     */
    public function daftarAplikasiOpd(Request $request)
    {
        $unitKerjaId = $request->query('unit_kerja_id');
        $unitKerja   = $unitKerjaId ? UnitKerja::find($unitKerjaId) : null;

        $query = DaftarAplikasiPerangkatDaerah::with('unitKerja')
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'))
            ->orderBy('unit_kerja_id');

        if ($unitKerjaId) {
            $query->where('unit_kerja_id', $unitKerjaId);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('pdf.daftar-aplikasi-opd', compact('data', 'unitKerja'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'         => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'     => false,
            ]);

        $filename = $unitKerja
            ? 'daftar-aplikasi-' . str($unitKerja->nama_opd)->slug() . '.pdf'
            : 'daftar-aplikasi-perangkat-daerah.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download PDF Daftar Website Perangkat Daerah (hanya OPD).
     */
    public function daftarWebsiteOpd(Request $request)
    {
        $unitKerjaId = $request->query('unit_kerja_id');
        $unitKerja   = $unitKerjaId ? UnitKerja::find($unitKerjaId) : null;

        $query = WebsitePerangkatDaerah::with('unitKerja')
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'opd'))
            ->orderBy('unit_kerja_id');

        if ($unitKerjaId) {
            $query->where('unit_kerja_id', $unitKerjaId);
        }

        $data = $query->get();

        $pdf = Pdf::loadView('pdf.daftar-website-opd', compact('data', 'unitKerja'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'         => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'     => false,
            ]);

        $filename = $unitKerja
            ? 'daftar-website-' . str($unitKerja->nama_opd)->slug() . '.pdf'
            : 'daftar-website-perangkat-daerah.pdf';

        return $pdf->download($filename);
    }
}
