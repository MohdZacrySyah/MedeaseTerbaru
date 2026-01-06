<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\Pendaftaran;
use Carbon\Carbon;

class PendaftaranDibatalkanNotification extends Notification
{
    use Queueable;

    protected $pendaftaran;
    protected $alasan;

    public function __construct($pendaftaran, $alasan)
    {
        $this->pendaftaran = $pendaftaran;
        $this->alasan = $alasan;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        // Ambil data relasi
        $dokter = $this->pendaftaran->jadwalPraktek->tenagaMedis->name ?? 'Dokter';
        $layanan = $this->pendaftaran->nama_layanan;
        $tanggal = Carbon::parse($this->pendaftaran->tanggal_kunjungan);

        return [
            // Field standar untuk tampilan list
            'title' => 'Pendaftaran Dibatalkan',
            'message' => 'Antrian Anda dibatalkan oleh Admin. Alasan: ' . $this->alasan,
            'type' => 'pembatalan', // Tipe khusus untuk logika blade
            
            // Data detail untuk Modal & Tampilan
            'dokter_name' => $dokter,
            'layanan' => $layanan,
            'date' => $tanggal->toDateTimeString(), // Format standar DB
            'no_antrian' => $this->pendaftaran->no_antrian,
            
            // Flag penting untuk styling MERAH
            'is_cancellation' => true,
            'alasan' => $this->alasan
        ];
    }
}