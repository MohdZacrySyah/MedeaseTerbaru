<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\PendaftaranController;
use Carbon\Carbon;

class PendaftaranControllerTest extends TestCase
{
    public function test_TC01_hari_ini_daftar_sebelum_jadwal()
    {
        $controller = new PendaftaranController();
        $hariIni = Carbon::today()->format('Y-m-d');
        $waktuSekarang = $hariIni . ' 07:00:00'; 
        $jamMulai = '08:00:00'; 
        $noAntrian = 1;

        $hasil = $controller->hitungEstimasiWaktu($jamMulai, $hariIni, $noAntrian, $waktuSekarang);
        
        $this->assertEquals("08:00:00", $hasil);
    }

    public function test_TC02_hari_ini_daftar_setelah_jadwal_terlewat()
    {
        $controller = new PendaftaranController();
        $hariIni = Carbon::today()->format('Y-m-d');
        $waktuSekarang = $hariIni . ' 09:00:00'; 
        $jamMulai = '08:00:00'; 
        $noAntrian = 1;

        $hasil = $controller->hitungEstimasiWaktu($jamMulai, $hariIni, $noAntrian, $waktuSekarang);
        
        $this->assertEquals("09:00:00", $hasil);
    }

    public function test_TC03_hari_ini_antrian_ke_dua()
    {
        $controller = new PendaftaranController();
        $hariIni = Carbon::today()->format('Y-m-d');
        $waktuSekarang = $hariIni . ' 07:00:00'; 
        $jamMulai = '08:00:00'; 
        $noAntrian = 2;

        $hasil = $controller->hitungEstimasiWaktu($jamMulai, $hariIni, $noAntrian, $waktuSekarang);
        
        $this->assertEquals("08:20:00", $hasil);
    }

    public function test_TC04_daftar_untuk_besok()
    {
        $controller = new PendaftaranController();
        $besok = Carbon::tomorrow()->format('Y-m-d');
        $waktuSekarang = Carbon::today()->format('Y-m-d') . ' 15:00:00'; 
        $jamMulai = '08:00:00'; 
        $noAntrian = 3; 

        $hasil = $controller->hitungEstimasiWaktu($jamMulai, $besok, $noAntrian, $waktuSekarang);
        
        $this->assertEquals("08:40:00", $hasil);
    }

    public function test_TC05_jam_mulai_kosong()
    {
        $controller = new PendaftaranController();
        $hariIni = Carbon::today()->format('Y-m-d');
        
        $hasil = $controller->hitungEstimasiWaktu(null, $hariIni, 1, $hariIni . ' 08:00:00');
        
        $this->assertNull($hasil);
    }
}