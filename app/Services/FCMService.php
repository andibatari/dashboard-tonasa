<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;

class FCMService
{
    public static function sendPushNotification($fcmToken, $title, $body, $link = null, $recipientSap = null, $tipe = 'general')
    {
        Log::info("DEBUG FCM: Mencoba kirim notif ke token: " . $fcmToken);
        try {
            $credentialsPath = storage_path('app/firebase_credentials.json');
            
            if (!file_exists($credentialsPath)) {
                Log::error("FCMService: File kredensial tidak ditemukan.");
                return false;
            }

            $json = json_decode(file_get_contents($credentialsPath), true);
            $projectId = $json['project_id'];

            $channelId = ($tipe === 'panggilan_poli') ? 'channel_panggilan_poli_v6' : 'channel_pengumuman_v1';

            // Base Payload (Data Only)
            $messagePayload = [
                'token' => $fcmToken,
                'data' => [
                    'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    'action_link' => $link ?? '', 
                    'recipient_sap' => $recipientSap ?? 'ALL',
                    'tipe' => $tipe,
                    'title' => $title, 
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                ],
            ];

            // 🌟 LOGIKA CERDAS YANG DIPERBAIKI:
            // Hanya 'silent_update' yang dikirim sebagai Data-Only (Tanpa Spanduk).
            // Panggilan Poli WAJIB pakai spanduk native agar bisa berbunyi di background!
            if ($tipe !== 'silent_update') {
                $messagePayload['notification'] = [
                    'title' => $title,
                    'body' => $body,
                ];

                if ($tipe === 'panggilan_poli') {
                    $messagePayload['android']['notification'] = [
                        'channel_id' => $channelId,
                        'sound'      => 'ding_dong', // Panggil nama file tanpa ekstensi (.wav)
                    ];
                } else {
                    $messagePayload['android']['notification'] = [
                        'channel_id' => $channelId,
                        'sound'      => 'default',
                    ];
                }
            }

            $response = Http::withToken(self::getAccessToken($credentialsPath))
                ->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
                    'message' => $messagePayload
                ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error("FCMService Exception: " . $e->getMessage());
            return false;
        }
    }

    private static function getAccessToken($credentialsPath)
    {
        $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];
        $credentials = new ServiceAccountCredentials($scopes, $credentialsPath);
        return $credentials->fetchAuthToken()['access_token'];
    }
}