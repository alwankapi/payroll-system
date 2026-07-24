<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PenggajianService;
use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Potongan;
use App\Models\PenggajianDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Exception;

class PenggajianServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PenggajianService $service;
    protected Jabatan $jabatan;
    protected Karyawan $karyawan;
    protected Potongan $potongan;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->service = new PenggajianService();
        
        // Create test data
        $this->jabatan = Jabatan::factory()->create([
            'nama_jabatan' => 'Manager',
            'gaji_pokok' => 10000000,
            'tunjangan_jabatan' => 2000000,
        ]);
        
        $this->karyawan = Karyawan::factory()->create([
            'jabatan_id' => $this->jabatan->id,
            'status_karyawan' => 'aktif',
        ]);
        
        $this->potongan = Potongan::factory()->create([
            'nama_potongan' => 'BPJS',
            'jenis_potongan' => 'persentase',
            'nilai' => 5,
            'status_aktif' => true,
        ]);
    }

    /** @test */
    public function it_can_create_penggajian()
    {
        $data = [
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'gaji_pokok' => 10000000,
            'tunjangan' => 2000000,
            'total_potongan' => 500000,
            'gaji_bersih' => 11500000,
            'status' => 'draft',
        ];

        $penggajian = $this->service->createPenggajian($data);

        $this->assertInstanceOf(Penggajian::class, $penggajian);
        $this->assertEquals($data['karyawan_id'], $penggajian->karyawan_id);
        $this->assertEquals($data['periode'], $penggajian->periode);
        $this->assertEquals($data['gaji_bersih'], $penggajian->gaji_bersih);
    }

    /** @test */
    public function it_prevents_duplicate_penggajian_for_same_periode()
    {
        $data = [
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'gaji_pokok' => 10000000,
            'tunjangan' => 2000000,
            'total_potongan' => 500000,
            'gaji_bersih' => 11500000,
        ];

        $this->service->createPenggajian($data);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Data penggajian untuk karyawan dan periode ini sudah ada');
        
        $this->service->createPenggajian($data);
    }

    /** @test */
    public function it_can_calculate_salary_correctly()
    {
        $result = $this->service->calculateSalary($this->karyawan, '2026-01-01');

        $this->assertArrayHasKey('gaji_pokok', $result);
        $this->assertArrayHasKey('tunjangan', $result);
        $this->assertArrayHasKey('total_potongan', $result);
        $this->assertArrayHasKey('gaji_bersih', $result);
        $this->assertArrayHasKey('potongan_details', $result);

        $this->assertEquals(10000000, $result['gaji_pokok']);
        $this->assertEquals(2000000, $result['tunjangan']);
        
        // BR-03: Gaji Bersih = Gaji Pokok + Tunjangan - Total Potongan
        $expected = $result['gaji_pokok'] + $result['tunjangan'] - $result['total_potongan'];
        $this->assertEquals($expected, $result['gaji_bersih']);
    }

    /** @test */
    public function it_calculates_potongan_correctly()
    {
        $result = $this->service->calculatePotongan($this->karyawan);

        $this->assertArrayHasKey('total', $result);
        $this->assertArrayHasKey('details', $result);
        $this->assertIsArray($result['details']);
        
        // BR-04: Potongan persentase dari gaji pokok
        $expectedPotongan = 10000000 * 0.05; // 5% dari 10 juta
        $this->assertEquals($expectedPotongan, $result['total']);
    }

    /** @test */
    public function it_can_update_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'status' => 'draft',
        ]);

        $updated = $this->service->updatePenggajian($penggajian, [
            'gaji_pokok' => 12000000,
            'gaji_bersih' => 13500000,
        ]);

        $this->assertEquals(12000000, $updated->gaji_pokok);
        $this->assertEquals(13500000, $updated->gaji_bersih);
    }

    /** @test */
    public function it_cannot_update_final_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'status' => 'final',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak dapat diubah');
        
        $this->service->updatePenggajian($penggajian, [
            'gaji_pokok' => 12000000,
        ]);
    }

    /** @test */
    public function it_can_delete_draft_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'status' => 'draft',
        ]);

        $result = $this->service->deletePenggajian($penggajian);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('penggajians', ['id' => $penggajian->id]);
    }

    /** @test */
    public function it_cannot_delete_final_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'status' => 'final',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak dapat dihapus');
        
        $this->service->deletePenggajian($penggajian);
    }

    /** @test */
    public function it_can_update_status_correctly()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'status' => 'draft',
        ]);

        $updated = $this->service->updateStatus($penggajian, 'final');
        $this->assertEquals('final', $updated->status);
    }

    /** @test */
    public function it_validates_status_workflow()
    {
        $penggajian = Penggajian::factory()->create([
            'status' => 'draft',
        ]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak diizinkan');
        
        // Cannot go from draft directly to dibayar
        $this->service->updateStatus($penggajian, 'dibayar');
    }
}
