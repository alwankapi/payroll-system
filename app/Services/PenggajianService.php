<?php

namespace App\Services;

use App\Models\Penggajian;
use App\Models\PenggajianDetail;
use App\Models\Karyawan;
use App\Models\Potongan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * PenggajianService
 * 
 * Service layer untuk menangani semua business logic terkait penggajian.
 * Implementasi Business Rules sesuai dokumentasi PRD.
 */
class PenggajianService
{
    /**
     * Create penggajian baru untuk satu karyawan.
     * 
     * BR-03: Gaji Bersih = Gaji Pokok + Tunjangan Jabatan − Total Potongan
     * BR-05: Satu karyawan hanya satu data penggajian per periode
     * BR-10: Snapshot data master (tidak berubah walau master diubah)
     * 
     * @param array $data
     * @return Penggajian
     * @throws Exception
     */
    public function createPenggajian(array $data): Penggajian
    {
        // Validasi duplikasi periode
        $exists = Penggajian::where('karyawan_id', $data['karyawan_id'])
            ->where('periode', $data['periode'])
            ->exists();

        if ($exists) {
            throw new Exception('Data penggajian untuk karyawan dan periode ini sudah ada.');
        }

        // Load karyawan dengan relasi jabatan
        $karyawan = Karyawan::with('jabatan')->findOrFail($data['karyawan_id']);

        // Calculate salary jika tidak ada data manual
        if (!isset($data['gaji_pokok']) || !isset($data['tunjangan'])) {
            $salaryData = $this->calculateSalary($karyawan, $data['periode']);
            $data = array_merge($salaryData, $data);
        }

        // Create penggajian record
        $penggajian = Penggajian::create([
            'karyawan_id' => $data['karyawan_id'],
            'periode' => $data['periode'],
            'gaji_pokok' => $data['gaji_pokok'],
            'tunjangan' => $data['tunjangan'],
            'total_potongan' => $data['total_potongan'],
            'gaji_bersih' => $data['gaji_bersih'],
            'status' => $data['status'] ?? 'draft',
            'tanggal_bayar' => $data['tanggal_bayar'] ?? null,
        ]);

        // Create detail potongan (snapshot)
        if (isset($data['potongan_details']) && is_array($data['potongan_details'])) {
            foreach ($data['potongan_details'] as $detail) {
                PenggajianDetail::create([
                    'penggajian_id' => $penggajian->id,
                    'potongan_id' => $detail['potongan_id'],
                    'nama_potongan' => $detail['nama_potongan'],
                    'nilai_potongan' => $detail['nilai_potongan'],
                ]);
            }
        }

        return $penggajian->fresh(['karyawan', 'details']);
    }

    /**
     * Update penggajian yang sudah ada.
     * 
     * BR-06: Penggajian Final/Dibayar tidak dapat diubah
     * 
     * @param Penggajian $penggajian
     * @param array $data
     * @return Penggajian
     * @throws Exception
     */
    public function updatePenggajian(Penggajian $penggajian, array $data): Penggajian
    {
        // Validasi status terkunci
        if ($penggajian->is_terkunci) {
            throw new Exception('Penggajian dengan status ' . $penggajian->status . ' tidak dapat diubah.');
        }

        // Validasi duplikasi periode jika periode diubah
        if (isset($data['periode']) && $data['periode'] != $penggajian->periode) {
            $exists = Penggajian::where('karyawan_id', $data['karyawan_id'])
                ->where('periode', $data['periode'])
                ->where('id', '!=', $penggajian->id)
                ->exists();

            if ($exists) {
                throw new Exception('Data penggajian untuk karyawan dan periode ini sudah ada.');
            }
        }

        // Update penggajian
        $penggajian->update([
            'karyawan_id' => $data['karyawan_id'] ?? $penggajian->karyawan_id,
            'periode' => $data['periode'] ?? $penggajian->periode,
            'gaji_pokok' => $data['gaji_pokok'] ?? $penggajian->gaji_pokok,
            'tunjangan' => $data['tunjangan'] ?? $penggajian->tunjangan,
            'total_potongan' => $data['total_potongan'] ?? $penggajian->total_potongan,
            'gaji_bersih' => $data['gaji_bersih'] ?? $penggajian->gaji_bersih,
            'status' => $data['status'] ?? $penggajian->status,
            'tanggal_bayar' => $data['tanggal_bayar'] ?? $penggajian->tanggal_bayar,
        ]);

        // Update detail potongan jika ada
        if (isset($data['potongan_details']) && is_array($data['potongan_details'])) {
            // Hapus detail lama
            $penggajian->details()->delete();

            // Buat detail baru
            foreach ($data['potongan_details'] as $detail) {
                PenggajianDetail::create([
                    'penggajian_id' => $penggajian->id,
                    'potongan_id' => $detail['potongan_id'],
                    'nama_potongan' => $detail['nama_potongan'],
                    'nilai_potongan' => $detail['nilai_potongan'],
                ]);
            }
        }

        return $penggajian->fresh(['karyawan', 'details']);
    }

    /**
     * Delete penggajian.
     * 
     * BR-24: Hanya status Draft yang dapat dihapus
     * 
     * @param Penggajian $penggajian
     * @return bool
     * @throws Exception
     */
    public function deletePenggajian(Penggajian $penggajian): bool
    {
        // Validasi status
        if ($penggajian->is_terkunci) {
            throw new Exception('Penggajian dengan status ' . $penggajian->status . ' tidak dapat dihapus.');
        }

        // Hapus detail dan penggajian (cascade)
        return $penggajian->delete();
    }

    /**
     * Generate penggajian massal untuk seluruh karyawan aktif dalam satu periode.
     * 
     * FR-28: Bulk generate untuk seluruh karyawan aktif
     * BR-08: Karyawan nonaktif tidak diikutkan
     * BR-05: Cegah duplikasi
     * 
     * @param string $periode Format: Y-m-d (tanggal 1 bulan berjalan)
     * @return array ['created' => int, 'skipped' => int]
     * @throws Exception
     */
    public function generateBulkPenggajian(string $periode): array
    {
        $created = 0;
        $skipped = 0;

        // Ambil semua karyawan dengan relasi jabatan
        $karyawans = Karyawan::with('jabatan')
            ->whereIn('status_karyawan', ['tetap', 'kontrak', 'magang'])
            ->get();

        foreach ($karyawans as $karyawan) {
            try {
                // Cek apakah sudah ada penggajian untuk periode ini
                $exists = Penggajian::where('karyawan_id', $karyawan->id)
                    ->where('periode', $periode)
                    ->exists();

                if ($exists) {
                    $skipped++;
                    continue;
                }

                // Calculate salary
                $salaryData = $this->calculateSalary($karyawan, $periode);

                // Create penggajian
                $this->createPenggajian([
                    'karyawan_id' => $karyawan->id,
                    'periode' => $periode,
                    'gaji_pokok' => $salaryData['gaji_pokok'],
                    'tunjangan' => $salaryData['tunjangan'],
                    'total_potongan' => $salaryData['total_potongan'],
                    'gaji_bersih' => $salaryData['gaji_bersih'],
                    'status' => 'draft',
                    'potongan_details' => $salaryData['potongan_details'],
                ]);

                $created++;
            } catch (Exception $e) {
                // Log error tapi lanjutkan ke karyawan berikutnya
                Log::error('Error generating penggajian for karyawan ' . $karyawan->id . ': ' . $e->getMessage());
                $skipped++;
            }
        }

        return [
            'created' => $created,
            'skipped' => $skipped,
        ];
    }

    /**
     * Calculate salary untuk seorang karyawan pada periode tertentu.
     * 
     * BR-03: Gaji Bersih = Gaji Pokok + Tunjangan − Total Potongan
     * BR-04: Potongan persentase dari gaji pokok, nominal tetap
     * BR-10: Snapshot nilai saat penggajian dibuat
     * 
     * @param Karyawan $karyawan
     * @param string $periode
     * @return array
     */
    public function calculateSalary(Karyawan $karyawan, string $periode): array
    {
        // Ambil gaji pokok dan tunjangan dari jabatan (snapshot)
        $gajiPokok = (float) $karyawan->jabatan->gaji_pokok;
        $tunjangan = (float) $karyawan->jabatan->tunjangan_jabatan;

        // Calculate potongan
        $potonganData = $this->calculatePotongan($karyawan);

        $totalPotongan = $potonganData['total'];
        $potonganDetails = $potonganData['details'];

        // Calculate gaji bersih
        $gajiBersih = $gajiPokok + $tunjangan - $totalPotongan;

        // Ensure gaji bersih tidak negatif
        $gajiBersih = max(0, $gajiBersih);

        return [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'total_potongan' => $totalPotongan,
            'gaji_bersih' => $gajiBersih,
            'potongan_details' => $potonganDetails,
        ];
    }

    /**
     * Calculate total potongan untuk seorang karyawan.
     * 
     * BR-04: Potongan persentase dihitung dari gaji pokok, nominal tetap
     * BR-10: Snapshot nilai potongan saat penggajian dibuat
     * 
     * @param Karyawan $karyawan
     * @return array ['total' => float, 'details' => array]
     */
    public function calculatePotongan(Karyawan $karyawan): array
    {
        $gajiPokok = (float) $karyawan->jabatan->gaji_pokok;
        $totalPotongan = 0;
        $details = [];

        // Ambil semua potongan aktif
        $potongans = Potongan::where('status_aktif', true)->get();

        foreach ($potongans as $potongan) {
            // Hitung nilai potongan menggunakan method di model
            $nilaiPotongan = $potongan->hitungPotongan($gajiPokok);

            $totalPotongan += $nilaiPotongan;

            // Simpan detail untuk snapshot
            $details[] = [
                'potongan_id' => $potongan->id,
                'nama_potongan' => $potongan->nama_potongan,
                'nilai_potongan' => $nilaiPotongan,
            ];
        }

        return [
            'total' => $totalPotongan,
            'details' => $details,
        ];
    }

    /**
     * Update status penggajian dengan validasi workflow.
     * 
     * Status workflow: draft -> final -> dibayar
     * BR-06: Validasi perubahan status
     * 
     * @param Penggajian $penggajian
     * @param string $status
     * @return Penggajian
     * @throws Exception
     */
    public function updateStatus(Penggajian $penggajian, string $status): Penggajian
    {
        // Validasi status value - hanya 3 status resmi
        $validStatuses = ['draft', 'final', 'dibayar'];
        if (!in_array($status, $validStatuses)) {
            throw new Exception('Status tidak valid. Harus salah satu dari: draft, final, dibayar');
        }

        // Validasi workflow status
        $currentStatus = $penggajian->status;

        // Jika status sama, tidak perlu update
        if ($currentStatus === $status) {
            return $penggajian;
        }

        // Validasi perubahan status yang diizinkan
        // draft = data masih dapat diedit
        // final = data telah dikunci
        // dibayar = gaji telah dibayarkan
        $allowedTransitions = [
            'draft' => ['final'],
            'final' => ['dibayar', 'draft'], // bisa rollback ke draft atau lanjut dibayar
            'dibayar' => [], // final state, tidak bisa diubah
        ];

        if (!isset($allowedTransitions[$currentStatus]) || 
            !in_array($status, $allowedTransitions[$currentStatus])) {
            $allowed = empty($allowedTransitions[$currentStatus]) 
                ? 'tidak ada (status final)' 
                : implode(', ', $allowedTransitions[$currentStatus]);
            throw new Exception(
                "Perubahan status dari '{$currentStatus}' ke '{$status}' tidak diizinkan. " .
                "Transisi yang diizinkan: {$allowed}"
            );
        }

        // Validasi tanggal bayar untuk status dibayar
        if ($status === 'dibayar' && empty($penggajian->tanggal_bayar)) {
            $penggajian->tanggal_bayar = now()->format('Y-m-d');
        }

        // Update status
        $penggajian->update([
            'status' => $status,
            'tanggal_bayar' => $penggajian->tanggal_bayar,
        ]);

        return $penggajian->fresh();
    }
}
