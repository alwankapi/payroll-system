<?php

namespace App\Exports;

use App\Models\Penggajian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanPenggajianExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * Get collection data
     */
    public function collection()
    {
        $query = Penggajian::with(['karyawan.jabatan', 'details']);

        if (!empty($this->filters['bulan'])) {
            $query->whereMonth('periode', $this->filters['bulan']);
        }
        if (!empty($this->filters['tahun'])) {
            $query->whereYear('periode', $this->filters['tahun']);
        }
        if (!empty($this->filters['jabatan_id'])) {
            $query->whereHas('karyawan', function ($q) {
                $q->where('jabatan_id', $this->filters['jabatan_id']);
            });
        }
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('periode', 'desc')->get();
    }

    /**
     * Define headings
     */
    public function headings(): array
    {
        return [
            'No',
            'NIK',
            'Nama Karyawan',
            'Jabatan',
            'Periode',
            'Gaji Pokok',
            'Tunjangan',
            'Total Potongan',
            'Gaji Bersih',
            'Status',
        ];
    }

    /**
     * Map data
     */
    public function map($penggajian): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $penggajian->karyawan->nik,
            $penggajian->karyawan->nama_lengkap,
            $penggajian->karyawan->jabatan->nama_jabatan,
            \Carbon\Carbon::parse($penggajian->periode)->format('F Y'),
            $penggajian->gaji_pokok,
            $penggajian->tunjangan,
            $penggajian->total_potongan,
            $penggajian->gaji_bersih,
            ucfirst($penggajian->status),
        ];
    }

    /**
     * Sheet title
     */
    public function title(): string
    {
        return 'Laporan Penggajian';
    }

    /**
     * Styling
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
