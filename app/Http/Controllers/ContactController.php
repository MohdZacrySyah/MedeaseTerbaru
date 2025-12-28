<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required',
            'message' => 'required|string',
        ]);

        $data = $request->all();
        
        // Email tujuan (sesuai yang ada di welcome.blade.php)
        $adminEmail = 'officialmedease@gmail.com'; 

        try {
            // Kirim Email
            Mail::send([], [], function ($message) use ($data, $adminEmail) {
                $message->to($adminEmail)
                    ->subject('Pesan Baru MedEase dari: ' . $data['name'])
                    ->html("
                        <h3>Pesan Baru dari Website</h3>
                        <p><strong>Nama:</strong> {$data['name']}</p>
                        <p><strong>Email:</strong> {$data['email']}</p>
                        <p><strong>No HP:</strong> {$data['phone']}</p>
                        <p><strong>Pesan:</strong><br>{$data['message']}</p>
                    ");
            });

            return response()->json(['status' => 'success', 'message' => 'Email terkirim']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}