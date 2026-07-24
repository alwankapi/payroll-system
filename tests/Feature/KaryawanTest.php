<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class KaryawanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Jabatan $jabatan;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->jabatan = Jabatan::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_karyawan_index()
    {
        $response = $this->actingAs($this->user)->get(route('karyawan.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('karyawan.index');
    }

    /** @test */
    public function guest_cannot_view_karyawan_index()
    {
        $response = $this->get(route('karyawan.index'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_create_karyawan()
    {
        $data = [
            'nik' => '123456789',
            'nama_karyawan' => 'John Doe',
            'email' => 'john@example.com',
            'no_telepon' => '081234567890',
            'alamat' => 'Jakarta',
            'tanggal_masuk' => '2026-01-01',
            'jabatan_id' => $this->jabatan->id,
            'status_karyawan' => 'aktif',
        ];

        $response = $this->actingAs($this->user)->post(route('karyawan.store'), $data);
        
        $response->assertRedirect(route('karyawan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('karyawans', ['nik' => '123456789']);
    }

    /** @test */
    public function create_karyawan_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('karyawan.store'), []);
        
        $response->assertSessionHasErrors(['nik', 'nama_karyawan', 'jabatan_id']);
    }

    /** @test */
    public function create_karyawan_validates_unique_nik()
    {
        Karyawan::factory()->create(['nik' => '123456789']);

        $data = [
            'nik' => '123456789',
            'nama_karyawan' => 'John Doe',
            'jabatan_id' => $this->jabatan->id,
        ];

        $response = $this->actingAs($this->user)->post(route('karyawan.store'), $data);
        
        $response->assertSessionHasErrors('nik');
    }

    /** @test */
    public function create_karyawan_validates_email_format()
    {
        $data = [
            'nik' => '123456789',
            'nama_karyawan' => 'John Doe',
            'email' => 'invalid-email',
            'jabatan_id' => $this->jabatan->id,
        ];

        $response = $this->actingAs($this->user)->post(route('karyawan.store'), $data);
        
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function authenticated_user_can_view_karyawan_detail()
    {
        $karyawan = Karyawan::factory()->create(['jabatan_id' => $this->jabatan->id]);

        $response = $this->actingAs($this->user)->get(route('karyawan.show', $karyawan));
        
        $response->assertStatus(200);
        $response->assertViewIs('karyawan.show');
        $response->assertViewHas('karyawan', $karyawan);
    }

    /** @test */
    public function authenticated_user_can_update_karyawan()
    {
        $karyawan = Karyawan::factory()->create([
            'jabatan_id' => $this->jabatan->id,
            'nama_karyawan' => 'Old Name',
        ]);

        $updateData = [
            'nik' => $karyawan->nik,
            'nama_karyawan' => 'New Name',
            'jabatan_id' => $this->jabatan->id,
            'status_karyawan' => 'aktif',
        ];

        $response = $this->actingAs($this->user)->put(route('karyawan.update', $karyawan), $updateData);
        
        $response->assertRedirect(route('karyawan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('karyawans', ['nama_karyawan' => 'New Name']);
    }

    /** @test */
    public function authenticated_user_can_delete_karyawan()
    {
        $karyawan = Karyawan::factory()->create(['jabatan_id' => $this->jabatan->id]);

        $response = $this->actingAs($this->user)->delete(route('karyawan.destroy', $karyawan));
        
        $response->assertRedirect(route('karyawan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('karyawans', ['id' => $karyawan->id]);
    }

    /** @test */
    public function karyawan_index_can_be_searched_by_nik()
    {
        Karyawan::factory()->create(['nik' => '111', 'jabatan_id' => $this->jabatan->id]);
        Karyawan::factory()->create(['nik' => '222', 'jabatan_id' => $this->jabatan->id]);

        $response = $this->actingAs($this->user)->get(route('karyawan.index', ['search' => '111']));
        
        $response->assertStatus(200);
        $response->assertSee('111');
    }

    /** @test */
    public function karyawan_index_can_filter_by_status()
    {
        Karyawan::factory()->create(['status_karyawan' => 'aktif', 'jabatan_id' => $this->jabatan->id]);
        Karyawan::factory()->create(['status_karyawan' => 'nonaktif', 'jabatan_id' => $this->jabatan->id]);

        $response = $this->actingAs($this->user)->get(route('karyawan.index', ['status' => 'aktif']));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function karyawan_index_can_filter_by_jabatan()
    {
        $jabatan2 = Jabatan::factory()->create();
        Karyawan::factory()->create(['jabatan_id' => $this->jabatan->id]);
        Karyawan::factory()->create(['jabatan_id' => $jabatan2->id]);

        $response = $this->actingAs($this->user)->get(route('karyawan.index', ['jabatan' => $this->jabatan->id]));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function status_karyawan_must_be_valid_enum()
    {
        $data = [
            'nik' => '123456789',
            'nama_karyawan' => 'John Doe',
            'jabatan_id' => $this->jabatan->id,
            'status_karyawan' => 'invalid_status',
        ];

        $response = $this->actingAs($this->user)->post(route('karyawan.store'), $data);
        
        $response->assertSessionHasErrors('status_karyawan');
    }

    /** @test */
    public function tanggal_masuk_cannot_be_future_date()
    {
        $data = [
            'nik' => '123456789',
            'nama_karyawan' => 'John Doe',
            'jabatan_id' => $this->jabatan->id,
            'tanggal_masuk' => now()->addDays(10)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)->post(route('karyawan.store'), $data);
        
        $response->assertSessionHasErrors('tanggal_masuk');
    }
}
