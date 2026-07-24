<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\PenggajianService;
use App\Models\Karyawan;
use App\Models\Jabatan;
use App\Models\Potongan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SalaryCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected PenggajianService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PenggajianService();
    }

    /** @test */
    public function it_calculates_salary_with_gaji_pokok_and_tunjangan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 5000000,
            'tunjangan_jabatan' => 1000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        $this->assertEquals(5000000, $result['gaji_pokok']);
        $this->assertEquals(1000000, $result['tunjangan']);
    }

    /** @test */
    public function it_calculates_gaji_bersih_correctly()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 10000000,
            'tunjangan_jabatan' => 2000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 500000,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        // BR-03: Gaji Bersih = Gaji Pokok + Tunjangan - Total Potongan
        $expected = 10000000 + 2000000 - 500000;
        $this->assertEquals($expected, $result['gaji_bersih']);
    }

    /** @test */
    public function it_ensures_gaji_bersih_is_not_negative()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 1000000,
            'tunjangan_jabatan' => 500000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        // Create potongan that exceeds gaji
        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 2000000,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        $this->assertGreaterThanOrEqual(0, $result['gaji_bersih']);
    }

    /** @test */
    public function it_includes_potongan_details_in_calculation()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 5000000,
            'tunjangan_jabatan' => 1000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'nama_potongan' => 'BPJS Kesehatan',
            'jenis_potongan' => 'nominal',
            'nilai' => 100000,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        $this->assertArrayHasKey('potongan_details', $result);
        $this->assertIsArray($result['potongan_details']);
        $this->assertNotEmpty($result['potongan_details']);
        
        $detail = $result['potongan_details'][0];
        $this->assertArrayHasKey('potongan_id', $detail);
        $this->assertArrayHasKey('nama_potongan', $detail);
        $this->assertArrayHasKey('nilai_potongan', $detail);
    }

    /** @test */
    public function it_calculates_with_zero_tunjangan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 3000000,
            'tunjangan_jabatan' => 0,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        $this->assertEquals(0, $result['tunjangan']);
        $this->assertEquals(3000000, $result['gaji_pokok']);
    }

    /** @test */
    public function it_calculates_with_multiple_potongan()
    {
        $jabatan = Jabatan::factory()->create([
            'gaji_pokok' => 8000000,
            'tunjangan_jabatan' => 2000000,
        ]);

        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $jabatan->id,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'nominal',
            'nilai' => 200000,
            'status_aktif' => true,
        ]);

        Potongan::factory()->create([
            'jenis_potongan' => 'persentase',
            'nilai' => 5,
            'status_aktif' => true,
        ]);

        $result = $this->service->calculateSalary($karyawan, '2026-01-01');

        $expectedPotongan = 200000 + (8000000 * 0.05);
        $this->assertEquals($expectedPotongan, $result['total_potongan']);
        
        $expectedGajiBersih = 8000000 + 2000000 - $expectedPotongan;
        $this->assertEquals($expectedGajiBersih, $result['gaji_bersih']);
    }
}
