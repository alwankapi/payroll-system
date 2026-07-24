<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PenggajianTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Jabatan $jabatan;
    protected Karyawan $karyawan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->jabatan = Jabatan::factory()->create();
        $this->karyawan = Karyawan::factory()->create(['jabatan_id' => $this->jabatan->id]);
    }

    /** @test */
    public function authenticated_user_can_view_penggajian_index()
    {
        $response = $this->actingAs($this->user)->get(route('penggajian.index'));
        $response->assertStatus(200);
        $response->assertViewIs('penggajian.index');
    }

    /** @test */
    public function authenticated_user_can_create_penggajian()
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

        $response = $this->actingAs($this->user)->post(route('penggajian.store'), $data);
        $response->assertRedirect(route('penggajian.index'));
        $this->assertDatabaseHas('penggajians', ['karyawan_id' => $this->karyawan->id]);
    }

    /** @test */
    public function cannot_create_duplicate_penggajian_for_same_periode()
    {
        Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
        ]);

        $data = [
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-01-01',
            'gaji_pokok' => 10000000,
            'tunjangan' => 2000000,
            'total_potongan' => 500000,
            'gaji_bersih' => 11500000,
        ];

        $response = $this->actingAs($this->user)->post(route('penggajian.store'), $data);
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function authenticated_user_can_view_penggajian_detail()
    {
        $penggajian = Penggajian::factory()->create(['karyawan_id' => $this->karyawan->id]);
        $response = $this->actingAs($this->user)->get(route('penggajian.show', $penggajian));
        $response->assertStatus(200);
        $response->assertViewIs('penggajian.show');
    }

    /** @test */
    public function can_update_draft_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->put(route('penggajian.update', $penggajian), [
            'karyawan_id' => $this->karyawan->id,
            'periode' => $penggajian->periode,
            'gaji_pokok' => 12000000,
            'tunjangan' => 2500000,
            'total_potongan' => 600000,
            'gaji_bersih' => 13900000,
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('penggajian.index'));
    }

    /** @test */
    public function cannot_update_final_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'final',
        ]);

        $response = $this->actingAs($this->user)->put(route('penggajian.update', $penggajian), [
            'karyawan_id' => $this->karyawan->id,
            'periode' => $penggajian->periode,
            'gaji_pokok' => 12000000,
            'status' => 'final',
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function can_delete_draft_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->delete(route('penggajian.destroy', $penggajian));
        $response->assertRedirect(route('penggajian.index'));
        $this->assertDatabaseMissing('penggajians', ['id' => $penggajian->id]);
    }

    /** @test */
    public function cannot_delete_final_penggajian()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'final',
        ]);

        $response = $this->actingAs($this->user)->delete(route('penggajian.destroy', $penggajian));
        $response->assertSessionHasErrors();
    }

    /** @test */
    public function penggajian_index_can_filter_by_status()
    {
        Penggajian::factory()->create(['karyawan_id' => $this->karyawan->id, 'status' => 'draft']);
        $response = $this->actingAs($this->user)->get(route('penggajian.index', ['status' => 'draft']));
        $response->assertStatus(200);
    }

    /** @test */
    public function penggajian_index_can_filter_by_periode()
    {
        Penggajian::factory()->create(['karyawan_id' => $this->karyawan->id, 'periode' => '2026-01-01']);
        $response = $this->actingAs($this->user)->get(route('penggajian.index', ['bulan' => '01', 'tahun' => '2026']));
        $response->assertStatus(200);
    }

    /** @test */
    public function can_generate_bulk_penggajian()
    {
        $response = $this->actingAs($this->user)->post(route('penggajian.generateBulk'), [
            'periode' => '2026-01-01',
        ]);

        $response->assertRedirect(route('penggajian.index'));
        $response->assertSessionHas('success');
    }

    /** @test */
    public function can_update_status_to_final()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->patch(route('penggajian.updateStatus', $penggajian), [
            'status' => 'final',
        ]);

        $response->assertRedirect();
        $this->assertEquals('final', $penggajian->fresh()->status);
    }

    /** @test */
    public function can_update_status_to_dibayar()
    {
        $penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'final',
        ]);

        $response = $this->actingAs($this->user)->patch(route('penggajian.updateStatus', $penggajian), [
            'status' => 'dibayar',
        ]);

        $response->assertRedirect();
        $this->assertEquals('dibayar', $penggajian->fresh()->status);
    }
}
