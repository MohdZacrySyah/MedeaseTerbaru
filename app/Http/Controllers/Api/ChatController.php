<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\TenagaMedis; // Model Dokter (Tabel Terpisah)
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // 1. Ambil Daftar Kontak (Tenaga Medis) untuk Pasien
    public function getContacts(Request $request)
    {
        $myId = $request->query('user_id'); // ID Pasien (Tabel Users)

        if (!$myId) {
            return response()->json(['status' => 'error', 'message' => 'User ID required'], 400);
        }

        // Ambil semua data dari tabel 'tenaga_medis'
        $doctors = TenagaMedis::all();

        // Format data & Hitung Unread/Last Message
        $contacts = $doctors->map(function($doc) use ($myId) {
            
            // Cari pesan terakhir antara Pasien (User) dan Dokter (Medis)
            $lastMsg = Message::where(function($q) use ($myId, $doc) {
                    $q->where('sender_id', $myId)
                      ->where('sender_type', 'user')
                      ->where('receiver_id', $doc->id)
                      ->where('receiver_type', 'medis');
                })
                ->orWhere(function($q) use ($myId, $doc) {
                    $q->where('sender_id', $doc->id)
                      ->where('sender_type', 'medis')
                      ->where('receiver_id', $myId)
                      ->where('receiver_type', 'user');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            // Hitung pesan belum dibaca (Pengirim: Medis -> Penerima: User)
            $unreadCount = Message::where('sender_id', $doc->id)
                ->where('sender_type', 'medis')
                ->where('receiver_id', $myId)
                ->where('receiver_type', 'user')
                ->where('is_read', false)
                ->count();

            // Tentukan URL Foto (Jika ada kolom profile_photo_path di tabel tenaga_medis)
            // Jika tidak ada, pakai default null
            $photoUrl = null;
            if (isset($doc->profile_photo_path) && $doc->profile_photo_path) {
                $photoUrl = url('storage/' . $doc->profile_photo_path);
            }

            return [
                'id' => $doc->id,
                'name' => $doc->name, // Pastikan kolom nama di tabel tenaga_medis adalah 'name'
                'photo_url' => $photoUrl,
                'last_message' => $lastMsg ? $lastMsg->message : "Halo dok, saya ingin konsultasi",
                'last_time' => $lastMsg ? $lastMsg->created_at->format('H:i') : null,
                'sort_time' => $lastMsg ? $lastMsg->created_at->timestamp : 0,
                'unread_count' => $unreadCount
            ];
        });

        // Sorting: Dokter dengan chat terbaru di atas
        $contactsSorted = $contacts->sortByDesc('sort_time')->values();

        return response()->json([
            'status' => 'success',
            'data' => $contactsSorted
        ]);
    }

    // 2. Ambil Detail Chat (Antara Pasien & Satu Dokter)
    public function getMessages(Request $request, $doctorId)
    {
        $myId = $request->query('user_id');

        // Tandai pesan dari Dokter ini sebagai 'read'
        Message::where('sender_id', $doctorId)
            ->where('sender_type', 'medis')
            ->where('receiver_id', $myId)
            ->where('receiver_type', 'user')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Ambil riwayat chat
        $messages = Message::where(function($q) use ($myId, $doctorId) {
                // Pesan Saya (User) ke Dokter (Medis)
                $q->where('sender_id', $myId)
                  ->where('sender_type', 'user')
                  ->where('receiver_id', $doctorId)
                  ->where('receiver_type', 'medis');
            })
            ->orWhere(function($q) use ($myId, $doctorId) {
                // Pesan Dokter (Medis) ke Saya (User)
                $q->where('sender_id', $doctorId)
                  ->where('sender_type', 'medis')
                  ->where('receiver_id', $myId)
                  ->where('receiver_type', 'user');
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $messages
        ]);
    }

    // 3. Kirim Pesan (Dari Pasien ke Dokter)
    public function sendMessage(Request $request)
    {
        try {
            $msg = Message::create([
                'sender_id' => $request->sender_id,     // ID Pasien
                'sender_type' => 'user',                // Tipe Pengirim: Pasien
                'receiver_id' => $request->receiver_id, // ID Dokter
                'receiver_type' => 'medis',             // Tipe Penerima: Dokter
                'message' => $request->message,
                'is_read' => false
            ]);

            return response()->json(['status' => 'success', 'data' => $msg]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}