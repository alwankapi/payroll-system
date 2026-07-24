<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Potongan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PotonganTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_potongan_index()
    {
        $response = $this->actingAs($this->user)->get(route('potongan.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('potongan.index');
    }

    /** @test */
    public function authenticated_user_can_create_potongan_nominal()
    {
        $data = [
            'nama_potongan' => 'Iuran Koperasi',
            'jenis_potongan' => 'nominal',
            'nilai' => 100000,
            'status_aktif' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('potongan.store'), $data);
        
        $response->assertRedirect(route('potongan.index'));
        $this->assertDatabaseHas('potongans', $data);
    }

    /** @test */
    public function authenticated_user_can_create_potongan_persentase()
    {
        $data = [
            'nama_potongan' => 'BPJS',
            'jenis_potongan' => 'persentase',
            'nilai' => 5,
            'status_aktif' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('potongan.store'), $data);
        
        $response->assertRedirect(route('potongan.index'));
        $this->assertDatabaseHas('potongans', $data);
    }

    /** @test */
    public function create_potongan_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('potongan.store'), []);
        
        $response->assertSessionHasErrors(['nama_potongan', 'jenis_potongan', 'nilai']);
    }

    /** @test */
    public function jenis_potongan_must_be_valid()
    {
        $data = [
            'nama_potongan' => 'Test',
            'jenis_potongan' => 'invalid',
            'nilai' => 100000,
        ];

        $response = $this->actingAs($this->user)->post(route('potongan.store'), $data);
        
        $response->assertSessionHasErrors('jenis_potongan');
    }

    /** @test */
    public function nilai_must_be_positive()
    {
        $data = [
            'nama_potongan' => 'Test',
            'jenis_potongan' => 'nominal',
            'nilai' => -100000,
        ];

        $response = $this->actingAs($this->user)->post(route('potongan.store'), $data);
        
        $response->assertSessionHasErrors('nilai');
    }

    /** @test */
    public function persentase_nilai_cannot_exceed_100()
    {
        $data = [
            'nama_potongan' => 'Test',
            'jenis_potongan' => 'persentase',
            'nilai' => 150,
        ];

        $response = $this->actingAs($this->user)->post(route('potongan.store'), $data);
        
        $response->assertSessionHasErrors('nilai');
    }

    /** @test */
    public function authenticated_user_can_view_potongan_detail()
    {
        $potongan = Potongan::factory()->create();

        $response = $this->actingAs($this->user)->get(route('potongan.show', $potongan));
        
        $response->assertStatus(200);
        $response->assertViewIs('potongan.show');
    }

    /** @test */
    public function authenticated_user_can_update_potongan()
    {
        $potongan = Potongan::factory()->create([
            'nama_potongan' => 'Old Name',
            'nilai' => 50000,
        ]);

        $updateData = [
            'nama_potongan' => 'New Name',
            'jenis_potongan' => $potongan->jenis_potongan,
            'nilai' => 75000,
            'status_aktif' => true,
        ];

        $response = $this->actingAs($this->user)->put(route('potongan.update', $potongan), $updateData);
        
        $response->assertRedirect(route('potongan.index'));
        $this->assertDatabaseHas('potongans', ['nama_potongan' => 'New Name']);
    }

    /** @test */
    public function authenticated_user_can_delete_potongan()
    {
        $potongan = Potongan::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('potongan.destroy', $potongan));
        
        $response->assertRedirect(route('potongan.index'));
        $this->assertDatabaseMissing('potongans', ['id' => $potongan->id]);
    }

    /** @test */
    public function potongan_index_can_filter_by_jenis()
    {
        Potongan::factory()->create(['jenis_potongan' => 'nominal']);
        Potongan::factory()->create(['jenis_potongan' => 'persentase']);

        $response = $this->actingAs($this->user)->get(route('potongan.index', ['jenis' => 'nominal']));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function potongan_index_can_filter_by_status()
    {
        Potongan::factory()->create(['status_aktif' => true]);
        Potongan::factory()->create(['status_aktif' => false]);

        $response = $this->actingAs($this->user)->get(route('potongan.index', ['status' => '1']));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function potongan_index_can_be_searched()
    {
        Potongan::factory()->create(['nama_potongan' => 'BPJS Kesehatan']);
        Potongan::factory()->create(['nama_potongan' => 'Iuran Koperasi']);

        $response = $this->actingAs($this->user)->get(route('potongan.index', ['search' => 'BPJS']));
        
        $response->assertStatus(200);
        $response->assertSee('BPJS');
    }
}
