# Struktur Project Laravel
## Sistem Informasi Penggajian Berbasis Web

| | |
|---|---|
| **Nama Proyek** | Sistem Penggajian (Payroll Web System) |
| **Versi Dokumen** | 1.0 |
| **Tanggal** | 23 Juli 2026 |
| **Disusun oleh** | Laravel Architect |
| **Referensi** | PRD v1.0, Analisis Sistem v1.0, Rancangan Database v1.0, Diagram UML v1.0 |
| **Framework** | Laravel 12, PHP 8.3 |

Struktur mengikuti skeleton Laravel 11/12 (tanpa `Kernel.php` — middleware, routing, dan exception handling dipusatkan di `bootstrap/app.php`).

---

## Daftar Isi
1. Folder
2. Routes
3. Controllers
4. Models
5. Middleware
6. Policies
7. Services
8. Repository (opsional)

---

## 1. Folder

```
sistem-penggajian/
├── app/
│   ├── Console/
│   ├── Exceptions/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/                  (bawaan Breeze)
│   │   │   ├── DashboardController.php
│   │   │   ├── JabatanController.php
│   │   │   ├── KaryawanController.php
│   │   │   ├── PotonganController.php
│   │   │   ├── PenggajianController.php
│   │   │   ├── SlipGajiController.php
│   │   │   ├── LaporanController.php
│   │   │   └── ProfilController.php
│   │   ├── Middleware/
│   │   │   └── CheckRole.php
│   │   └── Requests/
│   │       ├── StoreKaryawanRequest.php
│   │       ├── UpdateKaryawanRequest.php
│   │       └── ...                    (Request per modul)
│   ├── Models/
│   │   ├── User.php
│   │   ├── Jabatan.php
│   │   ├── Karyawan.php
│   │   ├── Potongan.php
│   │   ├── Penggajian.php
│   │   └── PenggajianDetail.php
│   ├── Policies/
│   │   ├── KaryawanPolicy.php
│   │   ├── JabatanPolicy.php
│   │   ├── PotonganPolicy.php
│   │   └── PenggajianPolicy.php
│   ├── Services/
│   │   ├── PenggajianService.php
│   │   └── SlipGajiService.php
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   ├── app.php                        (registrasi middleware, exceptions, routing)
│   └── providers.php
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       ├── layouts/
│       ├── auth/                      (bawaan Breeze)
│       ├── dashboard/
│       ├── jabatan/
│       ├── karyawan/
│       ├── potongan/
│       ├── penggajian/
│       ├── laporan/
│       └── profil/
├── routes/
│   ├── web.php
│   └── console.php
└── tests/
    ├── Feature/
    └── Unit/
```

## 2. Routes

`routes/web.php` — dikelompokkan per middleware `auth` dan `role:admin`, pakai `Route::resource` untuk CRUD standar.

```php
use App\Http\Controllers\{
    DashboardController, JabatanController, KaryawanController,
    PotonganController, PenggajianController, SlipGajiController,
    LaporanController, ProfilController
};

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin')->group(function () {
        Route::resource('jabatan', JabatanController::class);
        Route::resource('karyawan', KaryawanController::class);
        Route::resource('potongan', PotonganController::class);
        Route::resource('penggajian', PenggajianController::class);

        Route::post('penggajian/generate', [PenggajianController::class, 'generate'])
            ->name('penggajian.generate');
        Route::patch('penggajian/{penggajian}/status', [PenggajianController::class, 'updateStatus'])
            ->name('penggajian.status');

        Route::get('laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('laporan/export', [LaporanController::class, 'export'])->name('laporan.export');
    });

    // Diakses Admin & Karyawan — otorisasi detail ditangani PenggajianPolicy
    Route::get('slip-gaji/{penggajian}', [SlipGajiController::class, 'show'])->name('slip-gaji.show');
    Route::get('slip-gaji/{penggajian}/download', [SlipGajiController::class, 'download'])->name('slip-gaji.download');

    Route::get('profil', [ProfilController::class, 'edit'])->name('profil.edit');
    Route::patch('profil', [ProfilController::class, 'update'])->name('profil.update');
});

require __DIR__.'/auth.php'; // bawaan Breeze
```

## 3. Controllers

| Controller | Tanggung Jawab |
|---|---|
| DashboardController | Ringkasan sesuai role (Admin/Karyawan) |
| JabatanController | CRUD Jabatan |
| KaryawanController | CRUD Karyawan |
| PotonganController | CRUD Potongan |
| PenggajianController | CRUD Penggajian + `generate()` + `updateStatus()` |
| SlipGajiController | `show()` & `download()` slip gaji PDF |
| LaporanController | `index()` & `export()` laporan penggajian |
| ProfilController | `edit()` & `update()` profil pengguna |

Controller tetap tipis — validasi lewat Form Request, logika bisnis didelegasikan ke Service, otorisasi lewat Policy:

```php
class PenggajianController extends Controller
{
    public function __construct(private PenggajianService $penggajianService) {}

    public function generate(Request $request)
    {
        $this->authorize('create', Penggajian::class);

        $request->validate(['periode' => 'required|date_format:Y-m']);

        $this->penggajianService->generateUntukPeriode($request->periode);

        return back()->with('success', 'Penggajian berhasil digenerate.');
    }
}
```

## 4. Models

```php
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};

class Karyawan extends Model
{
    protected $fillable = [
        'user_id', 'jabatan_id', 'nik', 'nama_lengkap',
        'alamat', 'no_telepon', 'tanggal_masuk', 'no_rekening', 'status_karyawan',
    ];

    protected $casts = ['tanggal_masuk' => 'date'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function jabatan(): BelongsTo { return $this->belongsTo(Jabatan::class); }
    public function penggajians(): HasMany { return $this->hasMany(Penggajian::class); }
}
```

```php
class Penggajian extends Model
{
    protected $fillable = [
        'karyawan_id', 'periode', 'gaji_pokok', 'tunjangan',
        'total_potongan', 'gaji_bersih', 'status', 'tanggal_bayar',
    ];

    protected $casts = [
        'periode' => 'date',
        'tanggal_bayar' => 'date',
        'gaji_bersih' => 'decimal:2',
    ];

    public function karyawan(): BelongsTo { return $this->belongsTo(Karyawan::class); }
    public function details(): HasMany { return $this->hasMany(PenggajianDetail::class); }
}
```

| Model Lain | Relasi Kunci |
|---|---|
| User | `hasOne(Karyawan::class)` |
| Jabatan | `hasMany(Karyawan::class)` |
| Potongan | `hasMany(PenggajianDetail::class)` |
| PenggajianDetail | `belongsTo(Penggajian::class)`, `belongsTo(Potongan::class)` |

## 5. Middleware

`app/Http/Middleware/CheckRole.php` — middleware parametrik untuk pembatasan role:

```php
class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if ($request->user()?->role !== $role) {
            abort(403);
        }

        return $next($request);
    }
}
```

Didaftarkan sebagai alias di `bootstrap/app.php` (bukan `Kernel.php`, sesuai skeleton Laravel 11/12):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

## 6. Policies

Otomatis dikenali Laravel (auto-discovery) selama model ada di `App\Models` dan policy di `App\Policies` dengan akhiran `Policy`. Aturan bisnis dari dokumen sebelumnya langsung dipetakan ke sini:

```php
class PenggajianPolicy
{
    public function view(User $user, Penggajian $penggajian): bool
    {
        return $user->role === 'admin' || $penggajian->karyawan->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    public function update(User $user, Penggajian $penggajian): bool
    {
        return $user->role === 'admin' && $penggajian->status !== 'final'; // BR-06
    }

    public function delete(User $user, Penggajian $penggajian): bool
    {
        return $user->role === 'admin' && $penggajian->status === 'draft'; // BR-06
    }
}
```

KaryawanPolicy, JabatanPolicy, dan PotonganPolicy mengikuti pola sama: `create/update/delete` khusus `role === 'admin'` (BR-01), `view` untuk data milik sendiri bila karyawan (BR-02).

## 7. Services

`PenggajianService` menampung logika inti — perhitungan otomatis & pencegahan duplikasi (BR-05):

```php
class PenggajianService
{
    public function generateUntukPeriode(string $periode): void
    {
        $karyawanAktif = Karyawan::where('status_karyawan', 'aktif')->get();

        foreach ($karyawanAktif as $karyawan) {
            if (Penggajian::where('karyawan_id', $karyawan->id)->where('periode', $periode)->exists()) {
                continue; // cegah duplikasi
            }

            $gajiPokok = $karyawan->jabatan->gaji_pokok;
            $tunjangan = $karyawan->jabatan->tunjangan_jabatan;
            $totalPotongan = $this->hitungTotalPotongan($gajiPokok);

            $penggajian = Penggajian::create([
                'karyawan_id' => $karyawan->id,
                'periode' => $periode,
                'gaji_pokok' => $gajiPokok,
                'tunjangan' => $tunjangan,
                'total_potongan' => $totalPotongan,
                'gaji_bersih' => $gajiPokok + $tunjangan - $totalPotongan,
                'status' => 'draft',
            ]);

            $this->simpanRincianPotongan($penggajian, $gajiPokok);
        }
    }

    private function hitungTotalPotongan(float $gajiPokok): float
    {
        return Potongan::where('status_aktif', true)->get()
            ->sum(fn ($p) => $p->jenis_potongan === 'persentase' ? $gajiPokok * $p->nilai / 100 : $p->nilai);
    }
}
```

`SlipGajiService` membungkus pembuatan PDF (mis. `barryvdh/laravel-dompdf`), dipanggil dari `SlipGajiController`.

## 8. Repository (opsional)

Untuk skala proyek ini, Eloquent sudah cukup berperan sebagai abstraksi data (mirip Active Record) — menambah Repository di atasnya menambah lapisan tanpa manfaat besar selama hanya satu sumber data (MySQL) dan tidak ada rencana ganti ORM/datasource. Repository lebih berguna kalau perlu mock berat di unit test atau banyak sumber data berbeda.

Kalau tetap dipakai (mis. demi menunjukkan pola ke asesor), cukup terapkan di modul yang query-nya kompleks (Karyawan/Penggajian) — bukan seluruh modul:

```php
interface KaryawanRepositoryInterface
{
    public function getAktif(): Collection;
    public function findById(int $id): ?Karyawan;
}

class KaryawanRepository implements KaryawanRepositoryInterface
{
    public function getAktif(): Collection
    {
        return Karyawan::where('status_karyawan', 'aktif')->get();
    }

    public function findById(int $id): ?Karyawan
    {
        return Karyawan::find($id);
    }
}
```

Bind di `AppServiceProvider`:

```php
public function register(): void
{
    $this->app->bind(KaryawanRepositoryInterface::class, KaryawanRepository::class);
}
```
