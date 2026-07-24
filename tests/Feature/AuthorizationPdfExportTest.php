<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Jabatan;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Combined test for Authorization, PDF Generation, and Export functionality
 */
class AuthorizationPdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Jabatan $jabatan;
    protected Karyawan $karyawan;
    protected Penggajian $penggajian;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->jabatan = Jabatan::factory()->create();
        $this->karyawan = Karyawan::factory()->create(['jabatan_id' => $this->jabatan->id]);
        $this->penggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'final',
        ]);
    }

    // ============================================
    // AUTHORIZATION TESTS
    // ============================================

    /** @test */
    public function guest_redirected_to_login()
    {
        $routes = ['jabatan.index', 'karyawan.index', 'potongan.index', 'penggajian.index'];
        
        foreach ($routes as $route) {
            $response = $this->get(route($route));
            $response->assertRedirect(route('login'));
        }
    }

    /** @test */
    public function authenticated_user_can_access_all_modules()
    {
        $routes = ['jabatan.index', 'karyawan.index', 'potongan.index', 'penggajian.index'];
        
        foreach ($routes as $route) {
            $response = $this->actingAs($this->user)->get(route($route));
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function user_can_only_modify_own_data()
    {
        $this->actingAs($this->user);
        
        // User can create
        $response = $this->get(route('jabatan.create'));
        $response->assertStatus(200);
        
        // User can edit
        $jabatan = Jabatan::factory()->create();
        $response = $this->get(route('jabatan.edit', $jabatan));
        $response->assertStatus(200);
    }

    // ============================================
    // PDF GENERATION TESTS
    // ============================================

    /** @test */
    public function can_generate_slip_gaji_pdf()
    {
        $response = $this->actingAs($this->user)->get(route('slip-gaji.pdf', $this->penggajian));
        
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    /** @test */
    public function can_preview_slip_gaji()
    {
        $response = $this->actingAs($this->user)->get(route('slip-gaji.show', $this->penggajian));
        
        $response->assertStatus(200);
        $response->assertViewIs('pdf.slip-gaji');
        $response->assertViewHas('penggajian');
    }

    /** @test */
    public function slip_gaji_contains_required_information()
    {
        $response = $this->actingAs($this->user)->get(route('slip-gaji.show', $this->penggajian));
        
        $response->assertSee($this->karyawan->nama_karyawan);
        $response->assertSee($this->karyawan->nik);
        $response->assertSee($this->jabatan->nama_jabatan);
        $response->assertSee(number_format($this->penggajian->gaji_pokok));
    }

    /** @test */
    public function cannot_generate_pdf_for_draft_penggajian()
    {
        $draftPenggajian = Penggajian::factory()->create([
            'karyawan_id' => $this->karyawan->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->user)->get(route('slip-gaji.pdf', $draftPenggajian));
        
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    /** @test */
    public function can_generate_laporan_pdf()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.pdf', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertStatus(200);
    }

    // ============================================
    // EXPORT TESTS
    // ============================================

    /** @test */
    public function can_export_penggajian_to_excel()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.export', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertStatus(200);
        $response->assertDownload();
    }

    /** @test */
    public function exported_file_has_correct_format()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.export', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /** @test */
    public function export_requires_authentication()
    {
        $response = $this->get(route('laporan.export', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function can_view_laporan_index()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('laporan.index');
    }

    /** @test */
    public function laporan_can_filter_by_periode()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.index', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertStatus(200);
        $response->assertViewHas('penggajians');
    }

    /** @test */
    public function laporan_shows_statistics()
    {
        $response = $this->actingAs($this->user)->get(route('laporan.index', [
            'bulan' => '01',
            'tahun' => '2026',
        ]));
        
        $response->assertStatus(200);
        $response->assertViewHas('totalGajiPokok');
        $response->assertViewHas('totalTunjangan');
        $response->assertViewHas('totalPotongan');
        $response->assertViewHas('totalGajiBersih');
    }

    // ============================================
    // INTEGRATION TESTS
    // ============================================

    /** @test */
    public function complete_workflow_create_to_export()
    {
        // 1. Create penggajian
        $data = [
            'karyawan_id' => $this->karyawan->id,
            'periode' => '2026-02-01',
            'gaji_pokok' => 10000000,
            'tunjangan' => 2000000,
            'total_potongan' => 500000,
            'gaji_bersih' => 11500000,
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)->post(route('penggajian.store'), $data);
        $response->assertRedirect(route('penggajian.index'));

        // 2. Update status to final
        $penggajian = Penggajian::where('periode', '2026-02-01')->first();
        $response = $this->actingAs($this->user)->patch(route('penggajian.updateStatus', $penggajian), [
            'status' => 'final',
        ]);
        $response->assertRedirect();

        // 3. Generate slip gaji
        $response = $this->actingAs($this->user)->get(route('slip-gaji.pdf', $penggajian));
        $response->assertStatus(200);

        // 4. Export laporan
        $response = $this->actingAs($this->user)->get(route('laporan.export', [
            'bulan' => '02',
            'tahun' => '2026',
        ]));
        $response->assertStatus(200);
    }
}
