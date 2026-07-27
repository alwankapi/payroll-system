<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji - {{ $penggajian->karyawan->nama_lengkap }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 5px 0; }
        .info-section { margin-bottom: 20px; }
        .info-row { display: flex; margin-bottom: 8px; }
        .info-label { width: 150px; font-weight: bold; }
        .detail-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .detail-table th, .detail-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        .detail-table th { background-color: #f5f5f5; font-weight: bold; }
        .total-row { font-size: 14px; font-weight: bold; background-color: #e8f5e9; }
        .footer { margin-top: 40px; text-align: right; }
        .signature { margin-top: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SLIP GAJI KARYAWAN</h2>
        <p>Periode: {{ $penggajian->periode->translatedFormat('F Y') }}</p>
    </div>

    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Nama</div>
            <div>: {{ $penggajian->karyawan->nama_lengkap }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">NIP</div>
            <div>: {{ $penggajian->karyawan->nik }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Jabatan</div>
            <div>: {{ $penggajian->karyawan->jabatan->nama_jabatan }}</div>
        </div>
        <div class="info-row">
            <div class="info-label">Status</div>
            <div>: {{ strtoupper($penggajian->status) }}</div>
        </div>
    </div>

    <table class="detail-table">
        <thead>
            <tr>
                <th>Keterangan</th>
                <th style="text-align: right;">Jumlah (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td style="text-align: right;">{{ number_format($penggajian->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Tunjangan</td>
                <td style="text-align: right;">{{ number_format($penggajian->tunjangan, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Potongan Alpha ({{ $penggajian->jumlah_alpha }} hari)</td>
                <td style="text-align: right; color: red;">- {{ number_format($penggajian->potongan_alpha, 0, ',', '.') }}</td>
            </tr>
            <tr class="total-row">
                <td>GAJI BERSIH</td>
                <td style="text-align: right;">{{ number_format($penggajian->gaji_bersih, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($penggajian->catatan)
    <div style="margin-top: 20px; padding: 10px; background-color: #fff3cd; border-left: 4px solid #ffc107;">
        <strong>Catatan:</strong><br>
        {{ $penggajian->catatan }}
    </div>
    @endif

    <div class="footer">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
        <div class="signature">
            <p>Hormat kami,</p>
            <br><br><br>
            <p>(_________________)</p>
            <p>HRD</p>
        </div>
    </div>
</body>
</html>
