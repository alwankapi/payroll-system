<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; font-size: 18px; }
        .header p { margin: 3px 0; font-size: 10px; color: #666; }
        .info { margin-bottom: 15px; }
        .info table { width: 100%; }
        .info td { padding: 3px 0; }
        .summary { margin: 20px 0; }
        .summary table { width: 100%; border-collapse: collapse; }
        .summary th { background: #4F46E5; color: white; padding: 8px; text-align: left; font-size: 10px; }
        .summary td { padding: 8px; border-bottom: 1px solid #ddd; }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .data-table th { background: #1F2937; color: white; padding: 8px; text-align: left; font-size: 9px; }
        .data-table td { padding: 6px 8px; border-bottom: 1px solid #ddd; font-size: 9px; }
        .data-table tr:nth-child(even) { background: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 3px; font-size: 8px; font-weight: bold; display: inline-block; }
        .badge-draft { background: #E5E7EB; color: #1F2937; }
        .badge-final { background: #DBEAFE; color: #1E40AF; }
        .badge-dibayar { background: #D1FAE5; color: #065F46; }
        .footer { margin-top: 30px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ config('app.company_name', 'PT. SISTEM PENGGAJIAN') }}</h2>
        <p>{{ config('app.company_address', 'Jl. Contoh No. 123, Jakarta') }}</p>
        <p>Telp: {{ config('app.company_phone', '021-1234567') }} | Email: {{ config('app.company_email', 'info@perusahaan.com') }}</p>
        <h3 style="margin-top: 15px;">LAPORAN PENGGAJIAN</h3>
    </div>

    <div class="info">
        <table>
            <tr>
                <td width="15%"><strong>Periode Bulan</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $filters['bulan'] }}</td>
                <td width="15%"><strong>Tahun</strong></td>
                <td width="2%">:</td>
                <td width="33%">{{ $filters['tahun'] }}</td>
            </tr>
            <tr>
                <td><strong>Jabatan</strong></td>
                <td>:</td>
                <td>{{ $filters['jabatan'] }}</td>
                <td><strong>Status</strong></td>
                <td>:</td>
                <td>{{ $filters['status'] }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>:</td>
                <td colspan="4">{{ date('d F Y, H:i') }} WIB</td>
            </tr>
        </table>
    </div>

    <div class="summary">
        <h4 style="margin-bottom: 10px;">Ringkasan</h4>
        <table>
            <tr>
                <th>Total Gaji Pokok</th>
                <th>Total Tunjangan</th>
                <th>Total Potongan</th>
                <th>Total Gaji Bersih</th>
                <th>Jumlah Karyawan</th>
                <th>Total Transaksi</th>
            </tr>
            <tr>
                <td>Rp {{ number_format($summary['total_gaji_pokok'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($summary['total_tunjangan'], 0, ',', '.') }}</td>
                <td>Rp {{ number_format($summary['total_potongan'], 0, ',', '.') }}</td>
                <td><strong>Rp {{ number_format($summary['total_gaji_bersih'], 0, ',', '.') }}</strong></td>
                <td class="text-center">{{ $summary['jumlah_karyawan'] }} Orang</td>
                <td class="text-center">{{ $summary['jumlah_transaksi'] }} Data</td>
            </tr>
        </table>
    </div>

    <div>
        <h4 style="margin-bottom: 10px;">Detail Penggajian</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th width="4%">No</th>
                    <th width="12%">NIK</th>
                    <th width="18%">Nama Karyawan</th>
                    <th width="14%">Jabatan</th>
                    <th width="10%">Periode</th>
                    <th width="11%" class="text-right">Gaji Pokok</th>
                    <th width="10%" class="text-right">Tunjangan</th>
                    <th width="10%" class="text-right">Potongan</th>
                    <th width="11%" class="text-right">Gaji Bersih</th>
                </tr>
            </thead>
            <tbody>
                @foreach($penggajians as $index => $penggajian)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $penggajian->karyawan->nik }}</td>
                    <td>{{ $penggajian->karyawan->nama_lengkap }}</td>
                    <td>{{ $penggajian->karyawan->jabatan->nama_jabatan }}</td>
                    <td>{{ \Carbon\Carbon::parse($penggajian->periode)->format('M Y') }}</td>
                    <td class="text-right">{{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($penggajian->tunjangan, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($penggajian->total_potongan, 0, ',', '.') }}</td>
                    <td class="text-right"><strong>{{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</strong></td>
                </tr>
                @endforeach
                @if($penggajians->count() == 0)
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px;">Tidak ada data</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh Sistem Penggajian</p>
        <p>© {{ date('Y') }} {{ config('app.company_name', 'PT. SISTEM PENGGAJIAN') }}. All rights reserved.</p>
    </div>
</body>
</html>
