<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // <-- Pastikan ini adalah model User/Pasien Anda
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle permintaan login dari API.
     */
    public function login(Request $request)
    {
        // 1. Validasi input (email dan password Wajib ada)
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Coba autentikasi user
        $user = User::where('email', $request->email)->first();

        // 3. Cek jika user ada DAN password-nya benar
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // Jika gagal, kirim pesan error
            return response()->json([
                'message' => 'Email atau password salah'
            ], 401); // 401 = Unauthorized
        }

        // 4. Jika berhasil, Hapus token lama (jika ada) dan buat token baru
        $user->tokens()->delete(); // Opsional: hapus token lama agar 1 user 1 device
        $token = $user->createToken('auth_token')->plainTextToken;

        // 5. Kirim respons sukses beserta token
        return response()->json([
            'message' => 'Login berhasil',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user // Mengirim data user (bisa Anda sesuaikan)
        ], 200);
    }

    /**
     * Handle permintaan data profil (contoh rute terproteksi).
     */
    public function profil(Request $request)
    {
        // Karena rute ini dilindungi 'auth:sanctum',
        // kita bisa langsung mendapatkan user yang sedang login
        // dari $request->user()

        return response()->json([
            'data' => $request->user()
        ], 200);
    }

    /**
     * Handle permintaan logout.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang dipakai untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil'
        ], 200);
    }
}