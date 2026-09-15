<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DaftarAplikasiPerangkatDaerah;
use App\Models\WebsitePerangkatDaerah;

class PdfExportController extends Controller
{
    /**
     * Download PDF Daftar Aplikasi Perangkat Daerah (hanya OPD).
     */
    public function daftarAplikasiOpd(Request $request)
    {
        $data = DaftarAplikasiPerangkatDaerah::with('unitKerja')
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'OPD'))
            ->orderBy('unit_kerja_id')
            ->get();

        $pdf = Pdf::loadView('pdf.daftar-aplikasi-opd', compact('data'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'     => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->download('daftar-aplikasi-perangkat-daerah.pdf');
    }

    /**
     * Download PDF Daftar Website Perangkat Daerah (hanya OPD).
     */
    public function daftarWebsiteOpd(Request $request)
    {
        $data = WebsitePerangkatDaerah::with('unitKerja')
            ->whereHas('unitKerja', fn($q) => $q->where('tipe', 'opd'))
            ->orderBy('unit_kerja_id')
            ->get();

        $pdf = Pdf::loadView('pdf.daftar-website-opd', compact('data'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'     => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
            ]);

        return $pdf->download('daftar-website-perangkat-daerah.pdf');
    }
}
