<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PenggajianService;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Potongan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PotonganCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected PenggajianService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenggajianService();
    }

    /** @test */
    public function it_calculates_nominal_potongan_correctly()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 5000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'nama_potongan' => 'Iuran Koperasi',
            'jenis_potongan' => 'nominal',
            'nilai' => 100000,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        $this->assertEquals(100000, $result['total']);
    }

    /** @test */
    public function it_calculates_persentase_potongan_correctly()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 10000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'nama_potongan' => 'BPJS Kesehatan',
            'jenis_potongan' => 'persentase',
            'nilai' => 5,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        // BR-04: Potongan persentase dari gaji pokok
        $expected = 10000000 * 0.05;
        $this->assertEquals($expected, $result['total']);
    }

    /** @test */
    public function it_only_includes_active_potongan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 5000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 100000,
            'status_aktif' => true,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 200000,
            'status_aktif' => false, // Inactive
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        $this->assertEquals(100000, $result['total']);
    }

    /** @test */
    public function it_calculates_multiple_mixed_potongan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 8000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 150000,
            'status_aktif' => true,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'persentase',
            'nilai' => 3,
            'status_aktif' => true,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 50000,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        $expectedTotal = 150000 + (8000000 * 0.03) + 50000;
        $this->assertEquals($expectedTotal, $result['total']);
        $this->assertCount(3, $result['details']);
    }

    /** @test */
    public function it_returns_zero_when_no_active_potongan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 5000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        $this->assertEquals(0, $result['total']);
        $this->assertEmpty($result['details']);
    }

    /** @test */
    public function it_creates_snapshot_details_for_each_potongan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 6000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        $potongan1 = Potongan::factory()->create([
            'nama_potongan' => 'BPJS',
            'jenis_potongan' => 'nominal',
            'nilai' => 100000,
            'status_aktif' => true,
        ]);

        $potongan2 = Potongan::factory()->create([
            'nama_potongan' => 'PPH21',
            'jenis_potongan' => 'persentase',
            'nilai' => 5,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculatePotongan($karyawan);

        $this->assertCount(2, $result['details']);
        
        $detail1 = $result['details'][0];
        $this->assertEquals($potongan1->id, $detail1['potongan_id']);
        $this->assertEquals('BPJS', $detail1['nama_potongan']);
        $this->assertEquals(100000, $detail1['nilai_potongan']);
        
        $detail2 = $result['details'][1];
        $this->assertEquals($potongan2->id, $detail2['potongan_id']);
        $this->assertEquals('PPH21', $detail2['nama_potongan']);
        $this->assertEquals(300000, $detail2['nilai_potongan']); // 5% of 6M
    }
}
