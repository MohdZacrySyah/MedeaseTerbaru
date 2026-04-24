<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TenagaMedis; 
use Illuminate\Http\Request;
use App\Models\User; 
use App\Models\Pemeriksaan; 

class TenagaMedisController extends Controller
{
    /**
     * Menampilkan daftar akun tenaga medis yang aktif.
     */
    public function index()
    {
        // Dengan SoftDeletes, all() hanya mengambil data dokter yang belum dihapus (deleted_at = null)
        $tenagaMedis = TenagaMedis::all(); 
        return view('admin.tenaga_medis.index', compact('tenagaMedis'));
    }

    /**
     * Menampilkan form untuk membuat akun baru.
     * (Opsional jika sudah pakai modal)
     */
    public function create()
    {
        return view('admin.tenaga_medis.create');
    }

    /**
     * Menyimpan akun baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenaga_medis', 
            'password' => 'required|string|min:8|confirmed',
        ]);

        TenagaMedis::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('admin.tenaga-medis.index')->with('success', 'Akun tenaga medis berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit data.
     * (Opsional jika sudah pakai modal)
     */
    public function edit($id)
    {
        $akun = TenagaMedis::findOrFail($id); 
        return view('admin.tenaga_medis.edit', compact('akun'));
    }

    /**
     * Memperbarui data di database.
     */
    public function update(Request $request, $id)
    {
        $akun = TenagaMedis::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:tenaga_medis,email,' . $akun->id, 
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $akun->name = $request->name;
        $akun->email = $request->email;

        if ($request->filled('password')) {
            $akun->password = bcrypt($request->password);
        }

        $akun->save();

        return redirect()->route('admin.tenaga-medis.index')->with('success', 'Akun tenaga medis berhasil diperbarui.');
    }

    /**
     * Menghapus (Soft Delete) akun dari database.
     * Jadwal dan Pemeriksaan pasien dijamin aman!
     */
    public function destroy($id)
    {
        try {
            $akun = TenagaMedis::findOrFail($id); 

            // Karena menggunakan SoftDeletes, data dokter TIDAK BENAR-BENAR HAPUS.
            // Hanya disembunyikan (deleted_at terisi), sehingga data rekam medis pasien TETAP AMAN.
            $akun->delete();

            return redirect()->route('admin.tenaga-medis.index')
                ->with('success', 'Akun tenaga medis berhasil dinonaktifkan (Soft Delete). Data rekam medis pasien tetap aman.');

        } catch (\Exception $e) {
            return redirect()->route('admin.tenaga-medis.index')
                ->with('error', 'Gagal menonaktifkan tenaga medis: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan riwayat pasien khusus
     */
    public function riwayatPasien(User $user) 
    {
        if ($user->role !== 'pasien') {
            abort(404); 
        }

        // Karena tenaga medis mungkin sudah di-soft-delete, kita perlu load data mereka
        // menggunakan withTrashed() agar nama dokter tetap tampil di riwayat pasien
        $riwayats = Pemeriksaan::where('pasien_id', $user->id)
                               ->with(['tenagaMedis' => function($query) {
                                   $query->withTrashed(); // Memunculkan nama dokter yang sudah dihapus
                               }, 'pendaftaran']) 
                               ->latest('created_at')
                               ->get();

        return view('tenaga_medis.pasien.riwayat', compact('user', 'riwayats'));
    }
}