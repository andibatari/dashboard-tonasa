<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ==========================================
// PENGINGAT MCU OTOMATIS (WITA / Asia/Makassar)
// ==========================================
// 1. Jam 08:00 Pagi: Pengingat MCU Hari Ini bagi yang sudah terdaftar
Schedule::command('mcu:send-reminders --type=morning')
    ->dailyAt('08:00')
    ->timezone('Asia/Makassar');

// 2. Jam 20:00 Malam (8 Malam): Pengingat Puasa 10-12 Jam untuk Jadwal MCU Besok
Schedule::command('mcu:send-reminders --type=night')
    ->dailyAt('20:00')
    ->timezone('Asia/Makassar');
