<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - {{ $karyawan->nama_lengkap }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; line-height: 1.4; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #2563eb; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #1e40af; margin-bottom: 5px; }
        .header .subtitle { font-size: 10px; color: #666; }
        .info-section { margin-bottom: 15px; }
        .info-grid { display: table; width: 100%; margin-bottom: 10px; }
        .info-row { display: table-row; }
        .info-label { display: table-cell; width: 30%; padding: 4px 8px; font-weight: bold; background: #f3f4f6; }
        .info-value { display: table-cell; width: 70%; padding: 4px 8px; border-bottom: 1px solid #e5e7eb; }
        .title { font-size: 14px; font-weight: bold; margin: 15px 0 10px; color: #1e40af; border-bottom: 2px solid #dbeafe; padding-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        table th { background: #2563eb; color: white; padding: 8px; text-align: left; font-size: 11px; }
        table td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row { background: #f9fafb; font-weight: bold; }
        .gaji-bersih { background: #dbeafe; font-size: 13px; font-weight: bold; }
        .footer { margin-top: 30px; }
        .signature-section { display: table; width: 100%; margin-top: 40px; }
        .signature-box { display: table-cell; width: 50%; text-align: center; padding: 10px; }
        .signature-line { border-top: 1px solid #333; margin-top: 60px; display: inline-block; width: 200px; }
        .qr-placeholder { width: 80px; height: 80px; border: 2px dashed #cbd5e1; display: inline-block; margin: 10px 0; }
        .note { font-size: 9px; color: #6b7280; margin-top: 20px; padding: 10px; background: #f9fafb; border-left: 3px solid #fbbf24; }
        .badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .badge-final { background: #dbeafe; color: #1e40af; }
        .badge-dibayar { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <!-- Header Perusahaan -->
    <div class="header">
        <h1>{{ $perusahaan['nama'] }}</h1>
        <div class="subtitle">
            {{ $perusahaan['alamat'] }}<br>
            Telp: {{ $perusahaan['telepon'] }} | Email: {{ $perusahaan['email'] }}
        </div>
    </div>

    <!-- Judul Dokumen -->
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="font-size: 16px; color: #1e40af;">SLIP GAJI KARYAWAN</h2>
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            Periode: <strong>{{ $periode->locale('id')->isoFormat('MMMM YYYY') }}</strong>
            @if($penggajian->status === 'final')
                <span class="badge badge-final">FINAL</span>
            @elseif($penggajian->status === 'dibayar')
                <span class="badge badge-dibayar">DIBAYAR</span>
            @endif
        </div>
    </div>

    <!-- Data Karyawan -->
    <div class="title">DATA KARYAWAN</div>
    <div class="info-grid">
        <div class="info-row">
            <div class="info-label">NIK</div>
            <div class="info-value">{{ $karyawan->nik }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Nama Lengkap</div>
            <div class="info-value">{{ $karyawan->nama_lengkap }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jabatan</div>
            <div class="info-value">{{ $jabatan->nama_jabatan }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status Karyawan</div>
            <div class="info-value">{{ ucfirst($karyawan->status) }}</div>
        </div>
    </div>

    <!-- Komponen Gaji -->
    <div class="title">RINCIAN GAJI</div>
    <table>
        <thead>
            <tr>
                <th>Komponen</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan</td>
                <td class="text-right">{{ number_format($penggajian->tunjangan, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Total Pendapatan</strong></td>
                <td class="text-right">
                    <strong>{{ number_format($penggajian->gaji_pokok + $penggajian->tunjangan, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Detail Potongan -->
    @if($potongans->count() > 0)
    <div class="title">POTONGAN</div>
    <table>
        <thead>
            <tr>
                <th>Jenis Potongan</th>
                <th class="text-center">Perhitungan</th>
                <th class="text-right">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($potongans as $potongan)
            <tr>
                <td>{{ $potongan->nama_potongan }}</td>
                <td class="text-center">
                    @if($potongan->jenis_potongan === 'nominal')
                        Nominal
                    @else
                        {{ number_format($potongan->nilai, 2) }}%
                    @endif
                </td>
                <td class="text-right">
                    @if($potongan->jenis_potongan === 'nominal')
                        {{ number_format($potongan->nilai, 0, ',', '.') }}
                    @else
                        {{ number_format($penggajian->gaji_pokok * $potongan->nilai / 100, 0, ',', '.') }}
                    @endif
                </td>
            </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2"><strong>Total Potongan</strong></td>
                <td class="text-right"><strong>{{ number_format($penggajian->total_potongan, 0, ',', '.') }}</strong></td>
            </tr>
        </tbody>
    </table>
    @else
    <div class="title">POTONGAN</div>
    <div style="padding: 15px; text-align: center; color: #6b7280; background: #f9fafb; border: 1px dashed #cbd5e1;">
        Tidak ada potongan
    </div>
    @endif

    <!-- Gaji Bersih -->
    <table style="margin-top: 20px;">
        <tbody>
            <tr class="gaji-bersih">
                <td style="font-size: 14px;"><strong>GAJI BERSIH (TAKE HOME PAY)</strong></td>
                <td class="text-right" style="font-size: 14px;">
                    <strong>Rp {{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</strong>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Catatan -->
    @if($penggajian->catatan)
    <div class="note">
        <strong>Catatan:</strong> {{ $penggajian->catatan }}
    </div>
    @endif

    <!-- Footer dengan Tanda Tangan -->
    <div class="footer">
        <div class="signature-section">
            <div class="signature-box">
                <div style="margin-bottom: 10px;">QR Code Verifikasi</div>
                <div class="qr-placeholder"></div>
                <div style="font-size: 9px; color: #6b7280;">Scan untuk verifikasi</div>
            </div>
            <div class="signature-box">
                <div style="margin-bottom: 10px;">Jakarta, {{ $tanggal_cetak->locale('id')->isoFormat('D MMMM YYYY') }}</div>
                <div style="margin-bottom: 5px;">HRD Manager</div>
                <div class="signature-line"></div>
                <div style="margin-top: 5px; font-weight: bold;">Nama HRD</div>
            </div>
        </div>
    </div>

    <!-- Info Cetak -->
    <div style="margin-top: 30px; padding-top: 15px; border-top: 1px solid #e5e7eb; text-align: center; font-size: 9px; color: #9ca3af;">
        Dokumen ini dicetak secara otomatis pada {{ $tanggal_cetak->locale('id')->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') }}<br>
        Slip gaji ini sah dan tidak memerlukan tanda tangan basah
    </div>
</body>
</html>
