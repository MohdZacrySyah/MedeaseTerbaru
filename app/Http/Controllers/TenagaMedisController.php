<?php

namespace App\Http\Controllers; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pendaftaran;
use App\Models\JadwalPraktek;
use App\Models\User;
use App\Models\Pemeriksaan;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class TenagaMedisController extends Controller 
{
    /**
     * 🔥 METHOD AJAX UNTUK LAPORAN
     */
    public function getLaporanDataJson(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        $filter = $request->input('filter', 'hari_ini'); 
        $tanggalDipilih = $request->input('tanggal');
        $bulanDipilih = $request->input('bulan');

        // --- 1. DATA KPI (STATISTIK) ---
        $kunjunganHariIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                       ->whereDate('created_at', Carbon::today())->count();
                                       
        $kunjunganBulanIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count();
                                        
        $semuaKunjungan = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)->count();

        // --- 2. QUERY DASAR UNTUK DATA ---
        $query = Pemeriksaan::where('pemeriksaans.tenaga_medis_id', $tenagaMedisId)
                            ->join('pendaftarans', 'pemeriksaans.pendaftaran_id', '=', 'pendaftarans.id')
                            ->join('users', 'pemeriksaans.pasien_id', '=', 'users.id')
                            ->select(
                                'pemeriksaans.created_at as tanggal_kunjungan',
                                'pendaftarans.nama_layanan as layanan',
                                'users.id as pasien_id',
                                'users.name as nama_pasien',
                                'users.profile_photo_path'
                            );

        $chartQueryBase = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId);

        // --- 3. FILTER LOGIC ---
        if ($filter == 'hari_ini') {
            $query->whereDate('pemeriksaans.created_at', Carbon::today());
            $chartQuery = $chartQueryBase->select(DB::raw('HOUR(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereDate('created_at', Carbon::today())
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
            
            $chartLabels = $chartQuery->pluck('label')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_ini') {
            $query->whereMonth('pemeriksaans.created_at', Carbon::now()->month)
                  ->whereYear('pemeriksaans.created_at', Carbon::now()->year);
            
            $chartQuery = $chartQueryBase->select(DB::raw('DATE(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereMonth('created_at', Carbon::now()->month)
                                         ->whereYear('created_at', Carbon::now()->year)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'tanggal' && $tanggalDipilih) {
            $query->whereDate('pemeriksaans.created_at', $tanggalDipilih);
            
            $chartQuery = $chartQueryBase->select(DB::raw('HOUR(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereDate('created_at', $tanggalDipilih)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_terpilih' && $bulanDipilih) {
            $carbonBulan = Carbon::parse($bulanDipilih);
            $query->whereMonth('pemeriksaans.created_at', $carbonBulan->month)
                  ->whereYear('pemeriksaans.created_at', $carbonBulan->year);
            
            $chartQuery = $chartQueryBase->select(DB::raw('DATE(created_at) as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->whereMonth('created_at', $carbonBulan->month)
                                         ->whereYear('created_at', $carbonBulan->year)
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } else { 
            $chartQuery = $chartQueryBase->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as label'), DB::raw('COUNT(*) as jumlah'))
                                         ->groupBy('label')
                                         ->orderBy('label', 'asc')
                                         ->get();
                                         
            $chartLabels = $chartQuery->pluck('label')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
            $chartData = $chartQuery->pluck('jumlah');
        }

        $kunjunganData = $query->latest('pemeriksaans.created_at')->get();

        // --- 4. FORMAT DATA JSON UNTUK TABEL ---
        $tableData = $kunjunganData->map(function($item) {
            return [
                'pasien_id' => $item->pasien_id,
                'nama_pasien' => $item->nama_pasien,
                'layanan' => $item->layanan,
                'tanggal_formatted' => Carbon::parse($item->tanggal_kunjungan)->isoFormat('DD MMM YYYY, HH:mm'),
                'profile_photo_url' => $item->profile_photo_path ? asset('storage/' . $item->profile_photo_path) : null,
            ];
        });

        // --- 5. RETURN JSON RESPONSE ---
        return response()->json([
            'stats' => [
                'hari_ini' => $kunjunganHariIni,
                'bulan_ini' => $kunjunganBulanIni,
                'total' => $semuaKunjungan
            ],
            'table_data' => $tableData, 
            'table_count' => $kunjunganData->count(),
            'chart' => [
                'labels' => $chartLabels,
                'data' => $chartData,
                'title' => ucfirst(str_replace('_', ' ', $filter)) . ($tanggalDipilih ? " ($tanggalDipilih)" : "")
            ]
        ]);
    }

    /**
     * Menampilkan dashboard khusus untuk tenaga medis.
     */
    public function dashboard()
    {
        $tenagaMedis = Auth::guard('tenaga_medis')->user();
        $tenagaMedisId = $tenagaMedis->id;
        $today = Carbon::today();
        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        $jumlahTotalPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->count();
        
        $jumlahSelesai = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                    ->whereDate('created_at', $today)
                                    ->count();
        
        $jumlahMenunggu = $jumlahTotalPasien - $jumlahSelesai;

        $jadwalHariIni = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                    ->whereJsonContains('hari', $namaHariIni)
                                    ->first();

        $pendaftaranMenunggu = Pendaftaran::with('user')
                                        ->whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->where('status', '!=', 'Selesai') 
                                        ->orderBy('no_antrian', 'asc') 
                                        ->take(5) 
                                        ->get();

        return view('tenaga_medis.dashboard', compact(
            'tenagaMedis',
            'jumlahTotalPasien',
            'jumlahSelesai',
            'jumlahMenunggu',
            'jadwalHariIni',
            'pendaftaranMenunggu'
        ));
    }

    public function getDashboardData()
    {
        $tenagaMedis = Auth::guard('tenaga_medis')->user();
        $tenagaMedisId = $tenagaMedis->id;
        $today = Carbon::today();
        Carbon::setLocale('id');
        $namaHariIni = Carbon::now()->translatedFormat('l');

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        $jumlahTotalPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->count();
        
        $jumlahSelesai = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                    ->whereDate('created_at', $today)
                                    ->count();
        
        $jumlahMenunggu = $jumlahTotalPasien - $jumlahSelesai;

        $pendaftaranMenunggu = Pendaftaran::with('user')
                                        ->whereIn('nama_layanan', $layanansDitangani)
                                        ->whereDate('jadwal_dipilih', $today)
                                        ->where('status', '!=', 'Selesai')
                                        ->orderBy('no_antrian', 'asc')
                                        ->take(5)
                                        ->get();

        $tableHtml = view('tenaga_medis.components.table_antrian', compact('pendaftaranMenunggu'))->render();

        return response()->json([
            'total_pasien' => $jumlahTotalPasien,
            'menunggu' => $jumlahMenunggu,
            'selesai' => $jumlahSelesai,
            'antrian_count' => $pendaftaranMenunggu->count(),
            'table_html' => $tableHtml
        ]);
    }

    public function getPasienData(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        if (!$tenagaMedisId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString()); 

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        if (empty($layanansDitangani)) {
            $pendaftarans = collect();
        } else {
            $query = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                            ->with(['user', 'pemeriksaanAwal']);

            $query->whereDate('jadwal_dipilih', $tanggal);

            $pendaftarans = $query->select('pendaftarans.*')
                                  ->orderByRaw("
                                      CASE 
                                          WHEN pendaftarans.status != 'Selesai' AND EXISTS (SELECT 1 FROM pemeriksaan_awals WHERE pemeriksaan_awals.pendaftaran_id = pendaftarans.id) THEN 1
                                          WHEN pendaftarans.status != 'Selesai' THEN 2
                                          WHEN pendaftarans.status = 'Selesai' THEN 3
                                          ELSE 4
                                      END ASC
                                  ")
                                  ->orderBy('no_antrian', 'asc') 
                                  ->get();
        }

        $tableHtml = view('tenaga_medis.components.table_pasien', compact('pendaftarans'))->render();

        return response()->json([
            'table_html' => $tableHtml,
            'count' => $pendaftarans->count()
        ]);
    }

    public function showLoginForm()
    {
        return view('tenaga_medis.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::guard('tenaga_medis')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('tenaga-medis.dashboard');
        }

        return back()->withErrors(['email' => 'Login gagal, email atau password salah.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::guard('tenaga_medis')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('tenaga-medis.login');
    }

    public function lihatPasien(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        if (!$tenagaMedisId) {
            return redirect()->route('tenaga-medis.login')->withErrors(['email' => 'Sesi tidak valid.']);
        }

        $tanggal = $request->input('tanggal', Carbon::today()->toDateString()); 

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();

        if (empty($layanansDitangani)) {
            $pendaftarans = collect();
        } else {
            $query = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                            ->with(['user', 'pemeriksaanAwal']);

            $query->whereDate('jadwal_dipilih', $tanggal);

            $pendaftarans = $query->select('pendaftarans.*')
                                  ->orderByRaw("
                                      CASE 
                                          WHEN pendaftarans.status != 'Selesai' AND EXISTS (SELECT 1 FROM pemeriksaan_awals WHERE pemeriksaan_awals.pendaftaran_id = pendaftarans.id) THEN 1
                                          WHEN pendaftarans.status != 'Selesai' THEN 2
                                          WHEN pendaftarans.status = 'Selesai' THEN 3
                                          ELSE 4
                                      END ASC
                                  ")
                                  ->orderBy('no_antrian', 'asc')
                                  ->get();
        }

        return view('tenaga_medis.pasien.index', compact('pendaftarans', 'tanggal'));
    }

    public function detailPasien(Pendaftaran $pendaftaran)
    {
        return view('tenaga_medis.pasien.show', compact('pendaftaran'));
    }

    public function riwayatPasien(User $user) 
    {
        if ($user->role !== 'pasien') {
            abort(404);
        }

        $riwayats = Pemeriksaan::where('pasien_id', $user->id)
                               ->with(['tenagaMedis', 'pendaftaran.pemeriksaanAwal'])
                               ->latest('created_at')
                               ->get();

        return view('tenaga_medis.pasien.riwayat', compact('user', 'riwayats'));
    }

    public function myPemeriksaanHistory(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();

        $tanggalFilter = $request->input('tanggal');
        $namaFilter = $request->input('nama');

        $query = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                            ->with(['pasien', 'pendaftaran.pemeriksaanAwal'])
                            ->latest('created_at');

        if ($tanggalFilter) {
            $query->whereDate('created_at', $tanggalFilter);
        }

        if ($namaFilter) {
            $query->whereHas('pasien', function ($q) use ($namaFilter) {
                $q->where('name', 'like', '%' . $namaFilter . '%');
            });
        }

        $riwayats = $query->get();
        return view('tenaga_medis.riwayat_pemeriksaan_saya', compact('riwayats', 'tanggalFilter', 'namaFilter'));
    }

    public function laporan(Request $request)
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        $filter = $request->input('filter', 'bulan_ini'); 
        $tanggalDipilih = $request->input('tanggal', Carbon::today()->toDateString());
        $bulanDipilih = $request->input('bulan', Carbon::now()->format('Y-m')); 

        $kunjunganHariIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                       ->whereDate('created_at', Carbon::today())->count();
        $kunjunganBulanIni = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)
                                        ->whereMonth('created_at', Carbon::now()->month)
                                        ->whereYear('created_at', Carbon::now()->year)
                                        ->count();
        $semuaKunjungan = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId)->count();

        $query = Pemeriksaan::where('pemeriksaans.tenaga_medis_id', $tenagaMedisId)
                            ->join('pendaftarans', 'pemeriksaans.pendaftaran_id', '=', 'pendaftarans.id')
                            ->join('users', 'pemeriksaans.pasien_id', '=', 'users.id')
                            ->select(
                                'pemeriksaans.created_at as tanggal_kunjungan',
                                'pendaftarans.nama_layanan as layanan',
                                'users.id as pasien_id',
                                'users.name as nama_pasien',
                                'users.profile_photo_path'
                            );

        if ($filter == 'hari_ini') {
            $query->whereDate('pemeriksaans.created_at', Carbon::today());
        } elseif ($filter == 'bulan_ini') {
            $query->whereMonth('pemeriksaans.created_at', Carbon::now()->month)
                  ->whereYear('pemeriksaans.created_at', Carbon::now()->year);
        } elseif ($filter == 'tanggal') {
            $query->whereDate('pemeriksaans.created_at', $tanggalDipilih);
        } elseif ($filter == 'bulan_terpilih') {
            $carbonBulan = Carbon::parse($bulanDipilih);
            $query->whereMonth('pemeriksaans.created_at', $carbonBulan->month)
                  ->whereYear('pemeriksaans.created_at', $carbonBulan->year);
        }

        $kunjunganData = $query->latest('pemeriksaans.created_at')->get();

        $chartLabels = [];
        $chartData = [];
        $baseChartQuery = Pemeriksaan::where('tenaga_medis_id', $tenagaMedisId);

        if ($filter == 'hari_ini' || $filter == 'tanggal') {
            $tanggal = ($filter == 'hari_ini') ? Carbon::today() : $tanggalDipilih;
            $chartQuery = $baseChartQuery->select(DB::raw('HOUR(created_at) as jam'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereDate('created_at', $tanggal)
                                     ->groupBy('jam')->orderBy('jam', 'asc')->get();
            $chartLabels = $chartQuery->pluck('jam')->map(fn($jam) => "$jam:00");
            $chartData = $chartQuery->pluck('jumlah');

        } elseif ($filter == 'bulan_ini' || $filter == 'bulan_terpilih') {
            $carbonBulan = ($filter == 'bulan_ini') ? Carbon::now() : Carbon::parse($bulanDipilih);
            $chartQuery = $baseChartQuery->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('COUNT(*) as jumlah'))
                                     ->whereMonth('created_at', $carbonBulan->month)
                                     ->whereYear('created_at', $carbonBulan->year)
                                     ->groupBy('tanggal')->orderBy('tanggal', 'asc')->get();
            $chartLabels = $chartQuery->pluck('tanggal')->map(fn($tgl) => Carbon::parse($tgl)->format('d M'));
            $chartData = $chartQuery->pluck('jumlah');

        } else { 
            $chartQuery = $baseChartQuery->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as bulan'), DB::raw('COUNT(*) as jumlah'))
                                     ->groupBy('bulan')->orderBy('bulan', 'asc')->get();
            $chartLabels = $chartQuery->pluck('bulan')->map(fn($bln) => Carbon::parse($bln)->isoFormat('MMM YYYY'));
            $chartData = $chartQuery->pluck('jumlah');
        }

        return view('tenaga_medis.laporan', compact(
            'kunjunganHariIni', 'kunjunganBulanIni', 'semuaKunjungan',
            'kunjunganData', 'chartLabels', 'chartData',
            'filter', 'tanggalDipilih', 'bulanDipilih'
        ));
    }

    public function checkNotif()
    {
        $tenagaMedisId = Auth::guard('tenaga_medis')->id();
        
        if (!$tenagaMedisId) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated'
            ]);
        }

        $layanansDitangani = JadwalPraktek::where('tenaga_medis_id', $tenagaMedisId)
                                        ->distinct()
                                        ->pluck('layanan')
                                        ->toArray();
        
        $countPasien = 0;

        if (!empty($layanansDitangani)) {
            $countPasien = Pendaftaran::whereIn('nama_layanan', $layanansDitangani)
                                    ->where('status', '!=', 'Selesai')
                                    ->whereDate('jadwal_dipilih', Carbon::today())
                                    ->count();
        }

        $unreadChatCount = Message::where('receiver_id', $tenagaMedisId)
                                  ->where('receiver_type', 'medis')
                                  ->where('is_read', false)
                                  ->count();

        return response()->json([
            'success' => true,
            'counts' => [
                'pasien' => $countPasien,
                'chat' => $unreadChatCount 
            ]
        ]);
    }
}