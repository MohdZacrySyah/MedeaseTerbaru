<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

// --- ASUMSI MODEL ---
// Saya berasumsi Anda memiliki model-model ini.
// Ganti dengan nama model Anda jika berbeda.
use App\Models\Pemeriksaan; // Asumsi untuk data statistik
use App\Models\Jadwal;       // Asumsi untuk jadwal dokter
use App\Models\User;         // Model Pasien/User

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Dapatkan user yang sedang login (pasien)
        $user = $request->user();

        // 2. Ambil Statistik
        // Saya akan mengikuti logika dari file Blade Anda ($pemeriksaanTahunIni)
        // GANTI "pasien_id" dengan nama kolom Anda yang benar
        $pemeriksaanTahunIni = Pemeriksaan::where('pasien_id', $user->id)
                                ->whereYear('created_at', date('Y'))
                                ->count(); // Asumsi menghitung jumlah pemeriksaan
        
        // GANTI "pasien_id" dan "dokter_id" dengan nama kolom Anda
        $jumlahDokterDikunjungi = Pemeriksaan::where('pasien_id', $user->id)
                                    ->distinct('dokter_id')
                                    ->count('dokter_id'); // Asumsi menghitung jumlah dokter unik

        // 3. Ambil Notifikasi Hari Ini
        // GANTI ini dengan logika Anda untuk $notifikasiHariIni
        // Contoh: mencari jadwal konsultasi pasien HARI INI
        $notifikasiHariIni = Pemeriksaan::where('pasien_id', $user->id)
                                ->whereDate('tanggal_pemeriksaan', Carbon::today())
                                ->with('layanan') // Asumsi ada relasi 'layanan'
                                ->first(); 
        
        // 4. Ambil Jadwal Tenaga Medis Hari Ini
        // Ini sepertinya data umum, bukan spesifik per pasien
        $jadwalHariIni = Jadwal::where('hari', Carbon::now()->isoFormat('dddd'))
                            ->with('tenagaMedis') // Asumsi ada relasi 'tenagaMedis'
                            ->get();

        // 5. Kembalikan semua data sebagai JSON
        return response()->json([
            'user' => [
                'name' => $user->name,
                // tambahkan data user lain jika perlu
            ],
            'greeting' => $this->getGreeting(),
            'tanggal_hari_ini' => Carbon::now()->isoFormat('dddd, D MMMM YYYY'),
            'statistik' => [
                'pemeriksaan_selesai' => $pemeriksaanTahunIni,
                'dokter_dikunjungi' => $jumlahDokterDikunjungi,
            ],
            'notifikasi' => $notifikasiHariIni, // Bisa jadi null jika tidak ada
            'jadwal_tenaga_medis' => $jadwalHariIni,
        ], 200);
    }

    /**
     * Helper function untuk sapaan (greeting)
     */
    private function getGreeting()
    {
        $hour = date('H');
        if ($hour >= 5 && $hour < 11) {
            return 'Selamat Pagi';
        } else if ($hour >= 11 && $hour < 15) {
            return 'Selamat Siang';
        } else if ($hour >= 15 && $hour < 18) {
            return 'Selamat Sore';
        } else {
            return 'Selamat Malam';
        }
    }
}