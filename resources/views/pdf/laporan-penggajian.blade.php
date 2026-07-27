<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penggajian</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'DejaVu Sans', Arial, sans-serif; 
            font-size: 9px; 
            margin: 15px; 
            color: #333;
            line-height: 1.4;
        }
        
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 2px solid #333; 
            padding-bottom: 10px; 
        }
        
        .header h2 { 
            margin: 3px 0; 
            font-size: 16px; 
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .header p { 
            margin: 2px 0; 
            font-size: 8px; 
            color: #666; 
        }
        
        .header h3 { 
            margin-top: 8px; 
            font-size: 14px; 
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .info { 
            margin-bottom: 12px; 
            font-size: 8px;
        }
        
        .info table { 
            width: 100%; 
            border-collapse: collapse;
        }
        
        .info td { 
            padding: 2px 0; 
            vertical-align: top;
        }
        
        .info strong { 
            font-weight: bold; 
        }
        
        .summary { 
            margin: 15px 0; 
            page-break-inside: avoid;
        }
        
        .summary h4 {
            margin-bottom: 5px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        
        .summary-item {
            display: table-cell;
            width: 33.33%;
            padding: 6px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            vertical-align: top;
        }
        
        .summary-label {
            font-size: 7px;
            color: #666;
            margin-bottom: 2px;
            text-transform: uppercase;
        }
        
        .summary-value {
            font-size: 10px;
            font-weight: bold;
            color: #333;
        }
        
        .detail-section h4 {
            margin-bottom: 8px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 8px;
            font-size: 7px;
        }
        
        .data-table thead {
            background: #2d3748;
            color: white;
        }
        
        .data-table th { 
            padding: 5px 4px; 
            text-align: left; 
            font-weight: bold;
            border: 1px solid #1a202c;
        }
        
        .data-table td { 
            padding: 4px; 
            border: 1px solid #e2e8f0; 
            vertical-align: top;
        }
        
        .data-table tbody tr:nth-child(even) { 
            background: #f7fafc; 
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .potongan-list {
            font-size: 6px;
            line-height: 1.3;
        }
        
        .potongan-item {
            margin-bottom: 2px;
            padding: 2px;
            background: #f7fafc;
            border-left: 2px solid #4299e1;
        }
        
        .potongan-name {
            font-weight: bold;
            color: #2d3748;
        }
        
        .potongan-value {
            color: #e53e3e;
        }
        
        .total-potongan {
            margin-top: 3px;
            padding-top: 2px;
            border-top: 1px solid #cbd5e0;
            font-weight: bold;
            font-size: 7px;
        }
        
        .no-data {
            color: #a0aec0;
            font-style: italic;
            font-size: 7px;
        }
        
        .footer { 
            margin-top: 20px; 
            text-align: center; 
            font-size: 7px; 
            color: #666; 
            border-top: 1px solid #e2e8f0; 
            padding-top: 8px;
            page-break-inside: avoid;
        }
        
        .footer p {
            margin: 2px 0;
        }
        
        /* Print optimization */
        @media print {
            body { margin: 10px; }
            .page-break { page-break-after: always; }
        }
        
        /* Prevent widows and orphans */
        p, li, .data-table tr {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h2>PT. Sistem Penggajian Indonesia</h2>
        <p>Jl. Merdeka No. 123, Jakarta Pusat 10110</p>
        <p>Telp: (021) 1234-5678 | Email: info@sistempenggajian.com | www.sistempenggajian.com</p>
        <h3>Laporan Penggajian</h3>
    </div>

    <!-- Info Filter -->
    <div class="info">
        <table>
            <tr>
                <td width="18%"><strong>Periode Bulan</strong></td>
                <td width="2%">:</td>
                <td width="30%">{{ $filters['bulan'] }}</td>
                <td width="18%"><strong>Tahun</strong></td>
                <td width="2%">:</td>
                <td width="30%">{{ $filters['tahun'] }}</td>
            </tr>
            <tr>
                <td><strong>Filter Jabatan</strong></td>
                <td>:</td>
                <td>{{ $filters['jabatan'] }}</td>
                <td><strong>Status</strong></td>
                <td>:</td>
                <td>{{ $filters['status'] }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>:</td>
                <td colspan="4">{{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM YYYY HH:mm') }} WIB</td>
            </tr>
        </table>
    </div>

    <!-- Summary -->
    <div class="summary">
        <h4>Ringkasan Laporan</h4>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Gaji Pokok</div>
                <div class="summary-value">Rp {{ number_format($summary['total_gaji_pokok'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Tunjangan</div>
                <div class="summary-value">Rp {{ number_format($summary['total_tunjangan'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Potongan</div>
                <div class="summary-value">Rp {{ number_format($summary['total_potongan'], 0, ',', '.') }}</div>
            </div>
        </div>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Gaji Bersih</div>
                <div class="summary-value" style="color: #2d3748; font-size: 12px;">Rp {{ number_format($summary['total_gaji_bersih'], 0, ',', '.') }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Jumlah Karyawan</div>
                <div class="summary-value">{{ $summary['jumlah_karyawan'] }} Orang</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Transaksi</div>
                <div class="summary-value">{{ $summary['jumlah_transaksi'] }} Data</div>
            </div>
        </div>
    </div>

    <!-- Detail Table -->
    <div class="detail-section">
        <h4>Detail Penggajian Karyawan</h4>
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 3%;">No</th>
                    <th style="width: 9%;">NIK</th>
                    <th style="width: 18%;">Nama Karyawan</th>
                    <th style="width: 12%;">Jabatan</th>
                    <th style="width: 7%;">Periode</th>
                    <th style="width: 11%;" class="text-right">Gaji Pokok</th>
                    <th style="width: 10%;" class="text-right">Tunjangan</th>
                    <th style="width: 18%;">Potongan</th>
                    <th style="width: 12%;" class="text-right">Gaji Bersih</th>
                </tr>
            </thead>
            <tbody>
                @forelse($penggajians as $index => $penggajian)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $penggajian->karyawan->nik }}</td>
                    <td>{{ $penggajian->karyawan->nama_lengkap }}</td>
                    <td>{{ $penggajian->karyawan->jabatan->nama_jabatan }}</td>
                    <td class="text-center">{{ \Carbon\Carbon::parse($penggajian->periode)->format('M Y') }}</td>
                    <td class="text-right">{{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
                    <td class="text-right">{{ number_format($penggajian->tunjangan, 0, ',', '.') }}</td>
                    <td>
                        @if($penggajian->details && $penggajian->details->count() > 0)
                            <div class="potongan-list">
                                @foreach($penggajian->details as $detail)
                                    <div class="potongan-item">
                                        <span class="potongan-name">{{ $detail->nama_potongan }}</span>
                                        @if($detail->potongan)
                                            <span style="color: #718096;">({{ ucfirst($detail->potongan->jenis_potongan) }})</span>
                                        @endif
                                        <span class="potongan-value"> - Rp {{ number_format($detail->nilai_potongan, 0, ',', '.') }}</span>
                                    </div>
                                @endforeach
                                <div class="total-potongan">
                                    Total: Rp {{ number_format($penggajian->total_potongan, 0, ',', '.') }}
                                </div>
                            </div>
                        @else
                            <span class="no-data">Tidak ada potongan</span>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight: bold;">{{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 20px; color: #a0aec0;">
                        Tidak ada data penggajian sesuai dengan filter yang dipilih
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p><strong>PT. Sistem Penggajian Indonesia</strong></p>
        <p>Dokumen ini digenerate secara otomatis oleh Sistem Penggajian</p>
        <p>© {{ date('Y') }} PT. Sistem Penggajian Indonesia. All rights reserved.</p>
    </div>
</body>
</html>
