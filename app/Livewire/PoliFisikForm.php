<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\JadwalPoli;
use App\Models\Karyawan;
use App\Models\PesertaMcu;
use App\Models\Dokter;
use App\Models\FisikResult;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PoliFisikForm extends Component
{
    public $jadwalId; 
    public $poliData; 
    public $patient;
    public $fisikResult;
    public $listDokter;
    public $dokterId;
    public $isKaryawan = false;
    public $instansiPasien;
    public $dataFisik;
    public $kesimpulan;
    public $keterangan;

    protected $defaultOption = 'Dalam batas normal';
    protected $defaultRefleks = '+2';
    protected $defaultPajanan = 'Tidak';

    protected $rules = [
        'dokterId' => 'required|exists:dokters,id',
        
        // Validasi Anamnesa Baru
        'dataFisik.anamnesa.keluhan_utama' => 'nullable|string',
        'dataFisik.anamnesa.riwayat_kesehatan' => 'nullable|string',
        'dataFisik.anamnesa.riwayat_kesehatan_lainnya' => 'nullable|string',
        'dataFisik.anamnesa.riwayat_penyakit_keluarga' => 'nullable|string',
        'dataFisik.anamnesa.riwayat_penyakit_keluarga_lainnya' => 'nullable|string',
        'dataFisik.anamnesa.merokok' => 'nullable|string',
        'dataFisik.anamnesa.merokok_jumlah' => 'nullable|string',
        'dataFisik.anamnesa.olahraga' => 'nullable|string',

        'dataFisik.tanda_vital.tinggi_badan' => 'required|numeric|min:10',
        'dataFisik.tanda_vital.berat_badan' => 'required|numeric|min:5',
        'dataFisik.tanda_vital.tekanan_darah_sistol' => 'required|integer|min:50|max:300',
        'dataFisik.tanda_vital.tekanan_darah_diastol' => 'required|integer|min:30|max:200',
        'dataFisik.tanda_vital.nadi' => 'required|integer|min:40|max:180',
        'dataFisik.tanda_vital.pernafasan' => 'required|integer|min:5|max:40',
        'dataFisik.tanda_vital.suhu' => 'required|numeric|min:35|max:42',
        'dataFisik.tanda_vital.spo2' => 'required|numeric|min:90|max:100',

        'dataFisik.kepala.anemi' => 'required|string', 'dataFisik.kepala.ikterus' => 'required|string',
        'dataFisik.kepala.dyspnoe' => 'required|string', 'dataFisik.kepala.cyanosis' => 'required|string',
        'dataFisik.kepala.refleks_pupil' => 'required|string', 'dataFisik.kepala.tonsil_kanan' => 'required|string',
        'dataFisik.kepala.tonsil_kiri' => 'required|string', 'dataFisik.kepala.serumen' => 'required|string',
        'dataFisik.kepala.membran_timpani' => 'required|string',
        'dataFisik.leher.jvp' => 'required|string', 'dataFisik.leher.tiroid' => 'required|string',
        'dataFisik.leher.kelenjar_getah_bening' => 'required|string',
          
        'dataFisik.dada.bunyi_jantung_1_a' => 'required|string', 'dataFisik.dada.bunyi_jantung_1_b' => 'required|string',
        'dataFisik.dada.bunyi_jantung_2_a' => 'required|string', 'dataFisik.dada.bunyi_jantung_2_b' => 'required|string',
        'dataFisik.paru.bunyi_nafas' => 'required|string', 'dataFisik.paru.bunyi_nafas_tambahan' => 'required|string',

        'dataFisik.abdomen.peristaltik' => 'required|string', 'dataFisik.abdomen.nyeri_tekan' => 'required|string',
        'dataFisik.abdomen.massa' => 'required|string', 'dataFisik.abdomen.hati' => 'required|string',
        'dataFisik.abdomen.limpa' => 'required|string',
        'dataFisik.ekstremitas.ekstremitas' => 'required|string',
        'dataFisik.ekstremitas.refleks_fisiologis_kanan' => 'required|string',
        'dataFisik.ekstremitas.refleks_fisiologis_kanan_lainnya' => 'nullable|string', 
        'dataFisik.ekstremitas.refleks_fisiologis_kiri' => 'required|string',
        'dataFisik.ekstremitas.refleks_fisiologis_kiri_lainnya' => 'nullable|string',
        'dataFisik.ekstremitas.refleks_patologis_kanan' => 'required|string', 'dataFisik.ekstremitas.refleks_patologis_kiri' => 'required|string',

        // Pajanan Rules... (Sama seperti sebelumnya)
        'dataFisik.pajanan.fisik.kebisingan' => 'required|string', 'dataFisik.pajanan.fisik.suhu_panas' => 'required|string',
        'dataFisik.pajanan.fisik.suhu_dingin' => 'required|string', 'dataFisik.pajanan.fisik.radiasi_non_pengion' => 'required|string',
        'dataFisik.pajanan.fisik.radiasi_pengion' => 'required|string', 'dataFisik.pajanan.fisik.getaran_lokal' => 'required|string',
        'dataFisik.pajanan.fisik.getaran_seluruh_tubuh' => 'required|string', 'dataFisik.pajanan.fisik.ketinggian' => 'required|string',
        'dataFisik.pajanan.fisik.lain_fisik' => 'required|string',
        'dataFisik.pajanan.kimia.debu_anorganik' => 'required|string', 'dataFisik.pajanan.kimia.debu_organic' => 'required|string',
        'dataFisik.pajanan.kimia.asap' => 'required|string', 'dataFisik.pajanan.kimia.logam_berat' => 'required|string',
        'dataFisik.pajanan.kimia.iritan_asam' => 'required|string', 'dataFisik.pajanan.kimia.iritan_basa' => 'required|string',
        'dataFisik.pajanan.kimia.cairan_pembersih' => 'required|string', 'dataFisik.pajanan.kimia.pestisida' => 'required|string',
        'dataFisik.pajanan.kimia.uap_logam' => 'required|string', 'dataFisik.pajanan.kimia.lain_kimia' => 'required|string',
        'dataFisik.pajanan.biologi.bakteri' => 'required|string', 'dataFisik.pajanan.biologi.darah' => 'required|string',
        'dataFisik.pajanan.biologi.nyamuk' => 'required|string', 'dataFisik.pajanan.biologi.limbah' => 'required|string',
        'dataFisik.pajanan.biologi.lain_biologi' => 'required|string',
        'dataFisik.pajanan.psikologi.beban_kerja' => 'required|string', 'dataFisik.pajanan.psikologi.pekerjaan_tidak_sesuai' => 'required|string',
        'dataFisik.pajanan.psikologi.ketidakjelasan_tugas' => 'required|string', 'dataFisik.pajanan.psikologi.hambatan_jenjang_karir' => 'required|string',
        'dataFisik.pajanan.psikologi.bekerja_giliran' => 'required|string', 'dataFisik.pajanan.psikologi.konflik_teman_sekerja' => 'required|string',
        'dataFisik.pajanan.psikologi.konflik_keluarga' => 'required|string', 'dataFisik.pajanan.psikologi.lain_psikologi' => 'required|string',
        'dataFisik.pajanan.ergonomis.gerakan_berulang' => 'required|string', 'dataFisik.pajanan.ergonomis.angkat_berat' => 'required|string',
        'dataFisik.pajanan.ergonomis.duduk_lama' => 'required|string', 'dataFisik.pajanan.ergonomis.berdiri_lama' => 'required|string',
        'dataFisik.pajanan.ergonomis.posisi_tidak_ergonomis' => 'required|string', 'dataFisik.pajanan.ergonomis.pencahayaan_tidak_sesuai' => 'required|string',
        'dataFisik.pajanan.ergonomis.bekerja_layar_monitor' => 'required|string', 'dataFisik.pajanan.ergonomis.lain_ergonomis' => 'required|string',

        'kesimpulan' => 'nullable|string',
        'keterangan' => 'nullable|string',
    ];

    public function mount() 
    {
        try { $this->listDokter = Dokter::all(); } catch (\Exception $e) { $this->listDokter = collect([]); }

        $jadwalMcu = $this->poliData->jadwalMcu ?? null;
        $karyawanId = $jadwalMcu->karyawan_id ?? null;
        $pesertaMcuId = $jadwalMcu->peserta_mcus_id ?? null;

        if ($karyawanId) {
            $this->patient = Karyawan::with('unitKerja')->find($karyawanId);
            $this->isKaryawan = true;
        } elseif ($pesertaMcuId) {
            $this->patient = PesertaMcu::find($pesertaMcuId);
            $this->isKaryawan = false;
        } else {
            $this->patient = (object)['nama_lengkap' => 'Pasien Tidak Ditemukan', 'tanggal_lahir' => Carbon::now()->toDateString(), 'jenis_kelamin' => 'N/A', 'tinggi_badan' => 165, 'berat_badan' => 60,];
            $this->isKaryawan = false;
        }

        if ($this->patient && isset($this->patient->tanggal_lahir)) {
            if ($this->isKaryawan) {
                $unitKerja = $this->patient->unitKerja->nama_unit_kerja ?? 'Unit Kerja Tidak Diketahui';
                $this->instansiPasien = "PT SEMEN TONASA (Unit: {$unitKerja})";
                $this->patient->nama_lengkap = $this->patient->nama_karyawan ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_karyawan ?? 'N/A';
                $this->patient->no_sap = $this->patient->id ?? 'N/A';
            } else {
                $perusahaanAsal = $this->patient->perusahaan_asal ?? $jadwalMcu->perusahaan_asal ?? null;
                $this->instansiPasien = $perusahaanAsal ?? 'NON-KARYAWAN/UMUM';
                $this->patient->nama_lengkap = $this->patient->nama_lengkap ?? $jadwalMcu->nama_pasien ?? 'N/A';
                $this->patient->nik_pasien = $this->patient->nik_pasien ?? 'N/A';
                $this->patient->no_sap = 'N/A';
            }
            $this->patient->tanggal_lahir = $this->patient->tanggal_lahir ?? Carbon::now()->toDateString();
            $this->patient->jenis_kelamin = $this->patient->jenis_kelamin ?? 'PRIA';
            $this->patient->tinggi_badan = $this->patient->tinggi_badan ?? 165;
            $this->patient->berat_badan = $this->patient->berat_badan ?? 60;
        } else {
            $this->patient = (object)['nama_lengkap' => 'Pasien Tidak Ditemukan', 'tanggal_lahir' => Carbon::now()->toDateString(), 'jenis_kelamin' => 'N/A', 'tinggi_badan' => 165, 'berat_badan' => 60,];
            $this->instansiPasien = 'N/A';
        }

        if (!($this->poliData instanceof JadwalPoli) || !isset($this->poliData->id)) {
            $this->fisikResult = new FisikResult();
        } else {
            $this->fisikResult = FisikResult::firstOrNew(['jadwal_poli_id' => $this->poliData->id]);
        }

        if ($this->fisikResult->exists) {
            $this->dataFisik = $this->fisikResult->data_fisik;
            $this->kesimpulan = $this->fisikResult->kesimpulan;
            $this->keterangan = $this->fisikResult->keterangan;
            $this->dokterId = $this->fisikResult->dokter_id;
        } else {
            $this->dataFisik = $this->getDefaultDataFisik();
            $this->dokterId = null;
        }
    }

    public function getBmiProperty()
    {
        $tb = ($this->dataFisik['tanda_vital']['tinggi_badan'] ?? 0) / 100;
        $bb = $this->dataFisik['tanda_vital']['berat_badan'] ?? 0;

        if ($tb <= 0 || $bb <= 0) return ['bmi' => 0, 'kategori' => 'N/A'];

        $bmi = $bb / ($tb * $tb);
        $kategori = 'Normal';

        if ($bmi < 18.5) $kategori = 'Underweight';
        elseif ($bmi >= 23 && $bmi < 25) $kategori = 'Overweight';
        elseif ($bmi >= 25) $kategori = 'Obese';

        return ['bmi' => number_format($bmi, 2), 'kategori' => $kategori];
    }

    protected function getDefaultDataFisik()
    {
        $tb = $this->patient->tinggi_badan ?? 165;
        $bb = $this->patient->berat_badan ?? 60;

        $data = [
            'anamnesa' => [
                'keluhan_utama' => '',
                'riwayat_kesehatan' => 'Tidak ada',
                'riwayat_kesehatan_lainnya' => '',
                'riwayat_penyakit_keluarga' => 'Tidak ada',
                'riwayat_penyakit_keluarga_lainnya' => '',
                'merokok' => 'Tidak',
                'merokok_jumlah' => '',
                'olahraga' => '',
            ],
            'tanda_vital' => [
                'tinggi_badan' => $tb, 'berat_badan' => $bb, 'tekanan_darah_sistol' => 120, 'tekanan_darah_diastol' => 80,
                'nadi' => 80, 'pernafasan' => 18, 'suhu' => 36.5, 'spo2' => 97,
            ],
            'kepala' => [
                'anemi' => 'Tidak', 'ikterus' => 'Tidak', 'dyspnoe' => 'Tidak', 'cyanosis' => 'Tidak',
                'refleks_pupil' => 'RCL +/-', 'tonsil_kanan' => 'T1', 'tonsil_kiri' => 'T1',
                'serumen' => 'Tidak Ada', 'membran_timpani' => 'Normal',
            ],
            'leher' => [
                'jvp' => $this->defaultOption, 'tiroid' => $this->defaultOption, 'kelenjar_getah_bening' => $this->defaultOption,
            ],        
            'dada' => ['bunyi_jantung_1_a' => 'Murni', 'bunyi_jantung_1_b' => 'Reguler', 'bunyi_jantung_2_a' => 'Murni', 'bunyi_jantung_2_b' => 'Reguler'],
            'paru' => ['bunyi_nafas' => 'Vesikular', 'bunyi_nafas_tambahan' => 'Tidak ada'],
            'abdomen' => [
                'peristaltik' => $this->defaultOption, 'nyeri_tekan' => 'Tidak Ada', 'massa' => 'Tidak Ada',
                'hati' => $this->defaultOption, 'limpa' => $this->defaultOption,
            ],
            'ekstremitas' => [
                'ekstremitas' => 'Dalam Batas Normal', 
                'refleks_fisiologis_kanan' => $this->defaultRefleks, 'refleks_fisiologis_kanan_lainnya' => '',
                'refleks_fisiologis_kiri' => $this->defaultRefleks, 'refleks_fisiologis_kiri_lainnya' => '',
                'refleks_patologis_kanan' => 'Tidak Ada', 'refleks_patologis_kiri' => 'Tidak Ada',
            ],
            'pajanan' => [
                'fisik' => [
                    'kebisingan' => $this->defaultPajanan, 'suhu_panas' => $this->defaultPajanan, 'suhu_dingin' => $this->defaultPajanan,
                    'radiasi_non_pengion' => $this->defaultPajanan, 'radiasi_pengion' => $this->defaultPajanan,
                    'getaran_lokal' => $this->defaultPajanan, 'getaran_seluruh_tubuh' => $this->defaultPajanan,
                    'ketinggian' => $this->defaultPajanan, 'lain_fisik' => $this->defaultPajanan,
                ],
                'kimia' => [
                    'debu_anorganik' => $this->defaultPajanan, 'debu_organic' => $this->defaultPajanan, 'asap' => $this->defaultPajanan,
                    'logam_berat' => $this->defaultPajanan, 'iritan_asam' => $this->defaultPajanan, 'iritan_basa' => $this->defaultPajanan,
                    'cairan_pembersih' => $this->defaultPajanan, 'pestisida' => $this->defaultPajanan, 'uap_logam' => $this->defaultPajanan,
                    'lain_kimia' => $this->defaultPajanan,
                ],
                'biologi' => [
                    'bakteri' => $this->defaultPajanan, 'darah' => $this->defaultPajanan, 'nyamuk' => $this->defaultPajanan,
                    'limbah' => $this->defaultPajanan, 'lain_biologi' => $this->defaultPajanan,
                ],
                'psikologi' => [
                    'beban_kerja' => $this->defaultPajanan, 'pekerjaan_tidak_sesuai' => 'Tidak', 'ketidakjelasan_tugas' => $this->defaultPajanan,
                    'hambatan_jenjang_karir' => $this->defaultPajanan, 'bekerja_giliran' => $this->defaultPajanan,
                    'konflik_teman_sekerja' => $this->defaultPajanan, 'konflik_keluarga' => $this->defaultPajanan, 'lain_psikologi' => $this->defaultPajanan,
                ],
                'ergonomis' => [
                    'gerakan_berulang' => $this->defaultPajanan, 'angkat_berat' => $this->defaultPajanan, 'duduk_lama' => $this->defaultPajanan,
                    'berdiri_lama' => $this->defaultPajanan, 'posisi_tidak_ergonomis' => $this->defaultPajanan, 'pencahayaan_tidak_sesuai' => $this->defaultPajanan,
                    'bekerja_layar_monitor' => $this->defaultPajanan, 'lain_ergonomis' => $this->defaultPajanan,
                ],
            ],
        ];

        return $data;
    }

    public function simpanHasil()
    {
        $this->validate();

        $dokter = Dokter::find($this->dokterId);
        if (!$dokter) {
            session()->flash('error', 'Gagal: Data dokter yang dipilih tidak ditemukan.');
            return;
        }

        $bmi = $this->getBmiProperty();
        $this->dataFisik['tanda_vital']['bmi'] = $bmi['bmi'];
        $this->dataFisik['tanda_vital']['kategori_bmi'] = $bmi['kategori'];

        $dataToSave = [
            'data_fisik' => $this->dataFisik,
            'kesimpulan' => $this->kesimpulan,
            'keterangan' => $this->keterangan,
            'dokter_id' => $this->dokterId,
        ];

        try {
            $this->fisikResult = FisikResult::updateOrCreate(
                ['jadwal_poli_id' => $this->poliData->id],
                $dataToSave
            );

            $patientIdentifier = $this->patient->nama_pasien ?? $this->patient->nama_karyawan ?? 'N/A';
            $safeIdentifier = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $patientIdentifier);
            
            $fileName = 'Hasil_Pemeriksaan_Poli_Fisik_' . $safeIdentifier . '_Jadwal_' . $this->poliData->id . '_' . time() . '.pdf';
            $folderPath = 'pdf_reports';
            $storagePath = $folderPath . '/' . $fileName; 

            $reportData = [
                'patient' => $this->patient,
                'fisikResult' => $this->fisikResult,
                'dokter' => $dokter,
                'instansiPasien' => $this->instansiPasien,
                'pajanan' => $this->dataFisik['pajanan'],
            ];

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdfs.poli-fisik-report', $reportData);

            $uploadSuccess = Storage::disk('public')->put($storagePath, $pdf->output());
            if (!$uploadSuccess) {
                throw new \Exception("Sistem Gagal Mengunggah PDF ke public. Pastikan konfigurasi file system valid.");
            }

            $this->fisikResult->file_path = $storagePath; 
            $this->fisikResult->save();

            $this->poliData->file_path = $storagePath;
            $this->poliData->status = 'Finished';
            $this->poliData->save();

            session()->flash('success', 'Hasil pemeriksaan fisik dan laporan PDF berhasil disimpan!');

        } catch (\Exception $e) {
            Log::error('PDF Generation GAGAL (Poli Fisik): ' . $e->getMessage());
            session()->flash('error', 'Gagal membuat/menyimpan file PDF. Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        if ($this->poliData instanceof JadwalPoli && isset($this->poliData->id)) {
            $this->fisikResult = FisikResult::where('jadwal_poli_id', $this->poliData->id)->first() ?? $this->fisikResult;
        }

        return view('livewire.poli-fisik-form', [
            'bmiData' => $this->getBmiProperty()
        ]);
    }
}