<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notif;
use Illuminate\Support\Facades\Auth;

class NotifController extends Controller
{
    // Menampilkan semua notifikasi untuk pengguna yang login
    public function index()
    {
        $notifikasis = Auth::user()->notifs;
        return response()->json($notifikasis);
    }
    
    // Menandai satu notifikasi sebagai sudah dibaca
    public function markAsRead(Notif $notif)
    {
        if ($notif->karyawan_id === Auth::id() && is_null($notif->read_at)) {
            $notif->update(['read_at' => now()]);
            return response()->json(['message' => 'Notifikasi sudah dibaca.']);
        }

        return response()->json(['message' => 'Notifikasi tidak ditemukan atau sudah dibaca.'], 404);
    }
}
