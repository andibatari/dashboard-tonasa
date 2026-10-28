<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KaryawanResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Menyaring dan memformat data sebelum dikirimkan ke Flutter
        return [
            'id' => $this->id,
            'no_sap' => $this->no_sap,
            'nik' => $this->nik_karyawan,
            'nama_lengkap' => $this->nama_karyawan,
            'email' => $this->email,
            'no_hp' => $this->no_hp,
            
            // Format data tanggal dan lokasi
            'tanggal_lahir' => $this->tanggal_lahir ? $this->tanggal_lahir : null,
            'status_pernikahan' => $this->status_pernikahan,

            // Relasi (Pastikan ini sudah di-load di Controller, atau akan terjadi N+1)
            'departemen' => $this->whenLoaded('departemen', function () {
                return $this->departemen->nama_departemen; // Ambil nama saja
            }),
            'unit_kerja' => $this->whenLoaded('unitKerja', function () {
                return $this->unitKerja->nama_unit_kerja; // Ambil nama saja
            }),
            
            // Relasi nested untuk profil lengkap
            'lokasi_lengkap' => $this->whenLoaded('kecamatan', function () {
                return $this->kecamatan ? $this->kecamatan->nama_kecamatan . ', ' . $this->kecamatan->kabupaten->nama_kabupaten : null;
            }),
            'foto_url' => $this->whenNotNull($this->foto_profil ? url(Storage::url($this->foto_profil)) : null),

            // Relasi Keluarga (jika sudah di-load)
            'keluargas' => PesertaMcuResource::collection($this->whenLoaded('keluargas')),
        ];
    }
}