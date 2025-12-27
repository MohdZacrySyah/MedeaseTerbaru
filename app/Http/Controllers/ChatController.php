<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TenagaMedis;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Menampilkan halaman utama chat.
     */
    public function index()
    {
        // PENTING: Gunakan request()->user().
        // Karena route sudah dipisah, ini akan mengembalikan Model User (jika akses route pasien)
        // atau Model TenagaMedis (jika akses route dokter).
        $user = request()->user(); 

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Tentukan Role, Type, dan Prefix Route untuk View
        if ($user instanceof \App\Models\User) {
            // Jika login sebagai Pasien
            $myRole = 'pasien';
            $myType = 'user'; 
            $routePrefix = 'chat.'; 
        } else {
            // Jika login sebagai Tenaga Medis
            $myRole = 'medis';
            $myType = 'medis'; 
            $routePrefix = 'tenaga-medis.chat.'; // Prefix route dokter
        }
        
        $myId = $user->id;

        return view('chat.index', [
            'myRole' => $myRole,
            'myType' => $myType,
            'myId' => $myId,
            'routePrefix' => $routePrefix, // <-- Kirim ke view
        ]);
    }

    /**
     * API: Mengambil Daftar Kontak (Sidebar)
     */
    public function getContacts()
    {
        $user = request()->user();
        if (!$user) return response()->json([], 401);

        if ($user instanceof \App\Models\User) {
            return $this->getContactsForPatient($user->id);
        } else {
            return $this->getContactsForDoctor($user->id);
        }
    }

    // Logic Kontak Pasien: Lihat Semua Dokter
    private function getContactsForPatient($userId)
    {
        $doctors = TenagaMedis::all();
        $contacts = $doctors->map(function ($doc) use ($userId) {
            return $this->formatContact($doc, $userId, 'user', 'medis');
        });
        return response()->json($contacts->sortByDesc('timestamp')->values());
    }

    // Logic Kontak Dokter: Hanya Pasien yang pernah chat
    private function getContactsForDoctor($doctorId)
    {
        $senderIds = Message::where('receiver_id', $doctorId)
            ->where('receiver_type', 'medis')
            ->where('sender_type', 'user')
            ->pluck('sender_id');

        $receiverIds = Message::where('sender_id', $doctorId)
            ->where('sender_type', 'medis')
            ->where('receiver_type', 'user')
            ->pluck('receiver_id');

        $patientIds = $senderIds->merge($receiverIds)->unique();
        $patients = User::whereIn('id', $patientIds)->get();

        $contacts = $patients->map(function ($patient) use ($doctorId) {
            return $this->formatContact($patient, $doctorId, 'medis', 'user');
        });

        return response()->json($contacts->sortByDesc('timestamp')->values());
    }

    // Helper Format Data Kontak
    private function formatContact($contact, $myId, $myType, $targetType)
    {
        $lastMsg = Message::where(function ($q) use ($myId, $myType, $contact, $targetType) {
            $q->where('sender_id', $myId)->where('sender_type', $myType)
              ->where('receiver_id', $contact->id)->where('receiver_type', $targetType);
        })->orWhere(function ($q) use ($myId, $myType, $contact, $targetType) {
            $q->where('sender_id', $contact->id)->where('sender_type', $targetType)
              ->where('receiver_id', $myId)->where('receiver_type', $myType);
        })->latest()->first();

        $unread = Message::where('sender_id', $contact->id)
            ->where('sender_type', $targetType)
            ->where('receiver_id', $myId)
            ->where('receiver_type', $myType)
            ->where('is_read', false)
            ->count();

        $lastMessageText = '';
        $timestamp = 0;
        $timeStr = '';

        if ($lastMsg) {
            $lastMessageText = $lastMsg->message ?: ($lastMsg->media_path ? '📎 [File]' : '');
            $timestamp = $lastMsg->created_at->timestamp;
            $timeStr = $lastMsg->created_at->diffForHumans();
        }

        return [
            'id' => $contact->id,
            'name' => $contact->name ?? $contact->nama,
            'avatar' => $contact->profile_photo_path ? asset('storage/' . $contact->profile_photo_path) : null,
            'last_message' => $lastMessageText,
            'last_time' => $timeStr,
            'timestamp' => $timestamp,
            'unread' => $unread
        ];
    }

    // API: Search Pasien (Khusus Dokter)
    public function searchPatient(Request $request)
    {
        $query = $request->get('q');
        if (!$query) return response()->json([]);

        $patients = User::where('name', 'like', "%{$query}%")
            ->select('id', 'name', 'profile_photo_path')
            ->limit(5)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'avatar' => $p->profile_photo_path ? asset('storage/' . $p->profile_photo_path) : null,
                ];
            });

        return response()->json($patients);
    }

    // API: Get Messages
    public function getMessages(Request $request, $partnerId)
    {
        $user = request()->user();
        if (!$user) return response()->json([], 401);

        if ($user instanceof \App\Models\User) {
            $myType = 'user';
            $targetType = 'medis';
        } else {
            $myType = 'medis';
            $targetType = 'user';
        }
        $myId = $user->id;

        Message::where('sender_id', $partnerId)
            ->where('sender_type', $targetType)
            ->where('receiver_id', $myId)
            ->where('receiver_type', $myType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $lastId = $request->query('last_id', 0);

        $messages = Message::where('id', '>', $lastId)
            ->where(function ($q) use ($myId, $myType, $partnerId, $targetType) {
                $q->where(function ($sub) use ($myId, $myType, $partnerId, $targetType) {
                    $sub->where('sender_id', $myId)->where('sender_type', $myType)
                        ->where('receiver_id', $partnerId)->where('receiver_type', $targetType);
                })->orWhere(function ($sub) use ($myId, $myType, $partnerId, $targetType) {
                    $sub->where('sender_id', $partnerId)->where('sender_type', $targetType)
                        ->where('receiver_id', $myId)->where('receiver_type', $myType);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($msg) use ($myType, $myId) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'media_path' => $msg->media_path,
                    'media_type' => $msg->media_type,
                    'sender' => ($msg->sender_id == $myId && $msg->sender_type == $myType) ? 'me' : 'them',
                    'time' => $msg->created_at->format('H:i'),
                    'is_read' => $msg->is_read
                ];
            });

        return response()->json(['messages' => $messages]);
    }

    // API: Kirim Pesan
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
            'message' => 'nullable|string',
            'media' => 'nullable|file|max:10240'
        ]);

        if (!$request->message && !$request->hasFile('media')) {
            return response()->json(['status' => 'error', 'message' => 'Empty message'], 400);
        }

        $user = request()->user();
        if (!$user) return response()->json(['status' => 'error'], 401);

        if ($user instanceof \App\Models\User) {
            $senderType = 'user';
            $receiverType = 'medis';
        } else {
            $senderType = 'medis';
            $receiverType = 'user';
        }

        $mediaPath = null;
        $mediaType = null;

        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $mediaPath = $file->store('chat-media', 'public');
            $mediaType = $file->getClientMimeType();
        }

        $msg = Message::create([
            'sender_id' => $user->id,
            'sender_type' => $senderType,
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $receiverType,
            'message' => $request->message,
            'media_path' => $mediaPath,
            'media_type' => $mediaType,
            'is_read' => false
        ]);

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message_id' => $msg->id,
            'media_path' => $mediaPath,
            'media_type' => $mediaType
        ]);
    }

    // API: Mark Read Manual
    public function markRead($partnerId)
    {
        $user = request()->user();
        if (!$user) return response()->json([], 401);

        if ($user instanceof \App\Models\User) {
            $myType = 'user';
            $partnerType = 'medis';
        } else {
            $myType = 'medis';
            $partnerType = 'user';
        }

        Message::where('sender_id', $partnerId)
            ->where('sender_type', $partnerType)
            ->where('receiver_id', $user->id)
            ->where('receiver_type', $myType)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['status' => 'success']);
    }
}