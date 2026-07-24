<?php

namespace App\Services;

use App\Models\Penggajian;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;

class SlipGajiService
{
    /**
     * Generate slip gaji PDF untuk penggajian tertentu
     */
    public function generateSlipGaji(Penggajian $penggajian)
    {
        try {
            // Load penggajian dengan relasi yang diperlukan
            $penggajian->load([
                'karyawan.jabatan',
                'karyawan.user',
                'potongans'
            ]);

            // Siapkan data untuk PDF
            $data = $this->prepareSlipGajiData($penggajian);

            // Generate PDF dengan orientasi portrait
            $pdf = Pdf::loadView('pdf.slip-gaji', $data)
                ->setPaper('a4', 'portrait')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'defaultFont' => 'sans-serif'
                ]);

            return $pdf;
        } catch (\Exception $e) {
            Log::error('Error generating slip gaji PDF: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Siapkan data untuk slip gaji
     */
    protected function prepareSlipGajiData(Penggajian $penggajian): array
    {
        return [
            'penggajian' => $penggajian,
            'karyawan' => $penggajian->karyawan,
            'jabatan' => $penggajian->karyawan->jabatan,
            'potongans' => $penggajian->potongans,
            'periode' => \Carbon\Carbon::parse($penggajian->periode),
            'tanggal_cetak' => now(),
            'perusahaan' => $this->getDataPerusahaan(),
        ];
    }

    /**
     * Get data perusahaan (bisa diambil dari config atau database)
     */
    protected function getDataPerusahaan(): array
    {
        return [
            'nama' => config('app.company_name', 'PT. SISTEM PENGGAJIAN'),
            'alamat' => config('app.company_address', 'Jl. Contoh No. 123, Jakarta'),
            'telepon' => config('app.company_phone', '021-12345678'),
            'email' => config('app.company_email', 'hrd@perusahaan.com'),
        ];
    }

    /**
     * Download slip gaji sebagai PDF
     */
    public function downloadSlipGaji(Penggajian $penggajian)
    {
        $pdf = $this->generateSlipGaji($penggajian);
        
        $fileName = $this->generateFileName($penggajian);
        
        return $pdf->download($fileName);
    }

    /**
     * Stream slip gaji ke browser
     */
    public function streamSlipGaji(Penggajian $penggajian)
    {
        $pdf = $this->generateSlipGaji($penggajian);
        
        $fileName = $this->generateFileName($penggajian);
        
        return $pdf->stream($fileName);
    }

    /**
     * Generate nama file untuk slip gaji
     */
    protected function generateFileName(Penggajian $penggajian): string
    {
        $periode = \Carbon\Carbon::parse($penggajian->periode)->format('Y-m');
        $nik = $penggajian->karyawan->nik;
        
        return "slip-gaji-{$nik}-{$periode}.pdf";
    }

    /**
     * Validasi apakah penggajian bisa dicetak
     */
    public function canPrintSlipGaji(Penggajian $penggajian): bool
    {
        // Hanya penggajian dengan status 'final' atau 'dibayar' yang bisa dicetak
        return in_array($penggajian->status, ['final', 'dibayar']);
    }
}
