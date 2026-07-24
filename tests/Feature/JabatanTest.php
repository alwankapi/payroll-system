<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jabatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JabatanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_view_jabatan_index()
    {
        $response = $this->actingAs($this->user)->get(route('jabatan.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('jabatan.index');
    }

    /** @test */
    public function guest_cannot_view_jabatan_index()
    {
        $response = $this->get(route('jabatan.index'));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function authenticated_user_can_view_create_form()
    {
        $response = $this->actingAs($this->user)->get(route('jabatan.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('jabatan.create');
    }

    /** @test */
    public function authenticated_user_can_create_jabatan()
    {
        $data = [
            'nama_jabatan' => 'Manager IT',
            'gaji_pokok' => 15000000,
            'tunjangan_jabatan' => 3000000,
        ];

        $response = $this->actingAs($this->user)->post(route('jabatan.store'), $data);
        
        $response->assertRedirect(route('jabatan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jabatans', $data);
    }

    /** @test */
    public function create_jabatan_validates_required_fields()
    {
        $response = $this->actingAs($this->user)->post(route('jabatan.store'), []);
        
        $response->assertSessionHasErrors(['nama_jabatan', 'gaji_pokok', 'tunjangan_jabatan']);
    }

    /** @test */
    public function create_jabatan_validates_numeric_fields()
    {
        $data = [
            'nama_jabatan' => 'Manager',
            'gaji_pokok' => 'not_a_number',
            'tunjangan_jabatan' => 'not_a_number',
        ];

        $response = $this->actingAs($this->user)->post(route('jabatan.store'), $data);
        
        $response->assertSessionHasErrors(['gaji_pokok', 'tunjangan_jabatan']);
    }

    /** @test */
    public function authenticated_user_can_view_jabatan_detail()
    {
        $jabatan = Jabatan::factory()->create();

        $response = $this->actingAs($this->user)->get(route('jabatan.show', $jabatan));
        
        $response->assertStatus(200);
        $response->assertViewIs('jabatan.show');
        $response->assertViewHas('jabatan', $jabatan);
    }

    /** @test */
    public function authenticated_user_can_view_edit_form()
    {
        $jabatan = Jabatan::factory()->create();

        $response = $this->actingAs($this->user)->get(route('jabatan.edit', $jabatan));
        
        $response->assertStatus(200);
        $response->assertViewIs('jabatan.edit');
        $response->assertViewHas('jabatan', $jabatan);
    }

    /** @test */
    public function authenticated_user_can_update_jabatan()
    {
        $jabatan = Jabatan::factory()->create([
            'nama_jabatan' => 'Staff',
            'gaji_pokok' => 5000000,
        ]);

        $updateData = [
            'nama_jabatan' => 'Senior Staff',
            'gaji_pokok' => 7000000,
            'tunjangan_jabatan' => 1500000,
        ];

        $response = $this->actingAs($this->user)->put(route('jabatan.update', $jabatan), $updateData);
        
        $response->assertRedirect(route('jabatan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jabatans', $updateData);
    }

    /** @test */
    public function update_jabatan_validates_required_fields()
    {
        $jabatan = Jabatan::factory()->create();

        $response = $this->actingAs($this->user)->put(route('jabatan.update', $jabatan), []);
        
        $response->assertSessionHasErrors(['nama_jabatan', 'gaji_pokok', 'tunjangan_jabatan']);
    }

    /** @test */
    public function authenticated_user_can_delete_jabatan()
    {
        $jabatan = Jabatan::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('jabatan.destroy', $jabatan));
        
        $response->assertRedirect(route('jabatan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('jabatans', ['id' => $jabatan->id]);
    }

    /** @test */
    public function jabatan_index_displays_pagination()
    {
        Jabatan::factory()->count(15)->create();

        $response = $this->actingAs($this->user)->get(route('jabatan.index'));
        
        $response->assertStatus(200);
        $response->assertViewHas('jabatans');
    }

    /** @test */
    public function jabatan_index_can_be_searched()
    {
        Jabatan::factory()->create(['nama_jabatan' => 'Manager IT']);
        Jabatan::factory()->create(['nama_jabatan' => 'Staff Finance']);

        $response = $this->actingAs($this->user)->get(route('jabatan.index', ['search' => 'Manager']));
        
        $response->assertStatus(200);
        $response->assertSee('Manager IT');
        $response->assertDontSee('Staff Finance');
    }

    /** @test */
    public function jabatan_index_can_be_sorted()
    {
        Jabatan::factory()->create(['nama_jabatan' => 'B Manager', 'gaji_pokok' => 8000000]);
        Jabatan::factory()->create(['nama_jabatan' => 'A Staff', 'gaji_pokok' => 5000000]);

        $response = $this->actingAs($this->user)->get(route('jabatan.index', [
            'sort' => 'nama_jabatan',
            'direction' => 'asc'
        ]));
        
        $response->assertStatus(200);
    }

    /** @test */
    public function gaji_pokok_must_be_positive()
    {
        $data = [
            'nama_jabatan' => 'Manager',
            'gaji_pokok' => -1000000,
            'tunjangan_jabatan' => 500000,
        ];

        $response = $this->actingAs($this->user)->post(route('jabatan.store'), $data);
        
        $response->assertSessionHasErrors('gaji_pokok');
    }

    /** @test */
    public function tunjangan_jabatan_must_be_positive()
    {
        $data = [
            'nama_jabatan' => 'Manager',
            'gaji_pokok' => 5000000,
            'tunjangan_jabatan' => -500000,
        ];

        $response = $this->actingAs($this->user)->post(route('jabatan.store'), $data);
        
        $response->assertSessionHasErrors('tunjangan_jabatan');
    }
}
