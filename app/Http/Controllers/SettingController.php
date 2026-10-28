<?php
namespace App\Http\Controllers;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Mengambil semua pengaturan dan mengubahnya jadi format yang mudah dibaca view
        $settings = Setting::all()->keyBy('key');
        $tahapanList = Setting::getKmstTahapan();
        return view('admin.settings.index', compact('settings', 'tahapanList'));
    }

    public function update(Request $request)
    {
        // JIKA YANG DIKIRIM ADALAH FORM "SISTEM & PORTAL"
        if ($request->form_type === 'tampilan') {
            $kmstStatus = $request->has('kmst_event_active') ? '1' : '0';
            $bannerStatus = $request->has('maintenance_banner') ? '1' : '0';

            \App\Models\Setting::updateOrCreate(['key' => 'kmst_event_active'], ['value' => $kmstStatus]);
            \App\Models\Setting::updateOrCreate(['key' => 'maintenance_banner'], ['value' => $bannerStatus]);

            return back()->with('success', 'Status portal dan banner berhasil diperbarui!');
        }

        // JIKA YANG DIKIRIM ADALAH "FORM REGISTRASI"
        if ($request->form_type === 'aturan_form') {
            if ($request->has('max_anggota_ss')) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => 'max_anggota_ss'], 
                    ['value' => $request->input('max_anggota_ss')]
                );
            }

            if ($request->has('max_anggota_gkm')) {
                \App\Models\Setting::updateOrCreate(
                    ['key' => 'max_anggota_gkm'], 
                    ['value' => $request->input('max_anggota_gkm')]
                );
            }

            return back()->with('success', 'Aturan batas anggota form berhasil disimpan!');
        }

        // JIKA YANG DIKIRIM ADALAH FORM "TAHAPAN KMST"
        if ($request->form_type === 'tahapan_kmst') {
            // Aksi 1: Cepat aktifkan satu tahap tertentu
            if ($request->input('action') === 'activate_stage') {
                $targetId = $request->input('target_id');
                Setting::activateKmstTahap($targetId);
                return redirect()->route('settings.index', ['tab' => 'tahapan'])->with('success', "Status berhasil diperbarui! Tahap {$targetId} kini aktif.");
            }

            // Aksi 2: Simpan seluruh daftar tahapan (Tambah / Edit / Hapus)
            if ($request->has('tahapan_data')) {
                $raw = $request->input('tahapan_data');
                $tahapan = is_string($raw) ? json_decode($raw, true) : $raw;
                if (is_array($tahapan) && count($tahapan) > 0) {
                    // Validasi dan normalisasi tiap tahap
                    $normalized = [];
                    foreach ($tahapan as $idx => $t) {
                        $normalized[] = [
                            'id'         => $t['id'] ?? ($idx + 1),
                            'nomor'      => (string)($t['nomor'] ?? ($idx + 1)),
                            'judul'      => $t['judul'] ?? 'Tahapan ' . ($idx + 1),
                            'deskripsi'  => $t['deskripsi'] ?? '',
                            'badge'      => $t['badge'] ?? '',
                            'info_bawah' => $t['info_bawah'] ?? '',
                            'is_active'  => !empty($t['is_active']),
                            'icon_type'  => $t['icon_type'] ?? 'number',
                        ];
                    }
                    Setting::setKmstTahapan($normalized);
                    return redirect()->route('settings.index', ['tab' => 'tahapan'])->with('success', 'Seluruh konfigurasi tahapan KMST berhasil disimpan!');
                }
            }

            return redirect()->route('settings.index', ['tab' => 'tahapan'])->with('error', 'Data tahapan tidak valid.');
        }

        return back();
    }

    /**
     * Memperbarui file template dokumen resmi KMST.
     */
    public function updateTemplates(Request $request)
    {
        $request->validate([
            'template_ss'  => 'nullable|file|mimes:doc,docx,pdf|max:30720',
            'template_gkm' => 'nullable|file|mimes:doc,docx,pdf|max:30720',
            'template_pkm' => 'nullable|file|mimes:doc,docx,pdf|max:30720',
            'template_bi'  => 'nullable|file|mimes:doc,docx,pdf|max:30720',
        ]);

        $categories = ['ss', 'gkm', 'pkm', 'bi'];
        $tahun = date('Y');

        foreach ($categories as $cat) {
            $inputName = "template_{$cat}";

            if ($request->hasFile($inputName)) {
                $file = $request->file($inputName);
                
                // Hapus file lama jika ada
                $oldSetting = Setting::where('key', $inputName)->first();
                if ($oldSetting && $oldSetting->value) {
                    Storage::disk('public')->delete($oldSetting->value);
                }

                // Buat nama file rapi, contoh: Template_SS_2026_abc123.docx
                $fileName = "Template_" . strtoupper($cat) . "_{$tahun}_" . uniqid() . "." . $file->getClientOriginalExtension();
                $path = $file->storeAs('templates', $fileName, 'public');

                Setting::updateOrCreate(
                    ['key' => $inputName],
                    ['value' => $path]
                );
            }
        }

        return back()->with('success', 'Template dokumen resmi berhasil diperbarui!');
    }
}