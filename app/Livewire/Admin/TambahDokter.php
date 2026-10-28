<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Dokter; 
use Livewire\WithPagination;

class TambahDokter extends Component
{
    use WithPagination;

    public $nik, $nama_lengkap, $email, $spesialisasi, $tanggal_lahir, $golongan_darah, $no_hp;
    public $daftarSpesialisasi = [];
    public $isEditing = false;
    public $dokterId;

    public function mount()
    {
        $this->daftarSpesialisasi = [
            'Dokter Umum',
            'Dokter Gigi & Mulut',
            'Spesialis Mata',
            'Lainnya',
        ];
    }

    public function getDokterUsersProperty()
    {
        return Dokter::latest()->paginate(10);
    }

    public function save()
    {
        $validatedData = $this->validate([
            'nik' => 'nullable|string|unique:dokters,nik',
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:dokters,email',
            'spesialisasi' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'golongan_darah' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
        ]);

        $validatedData['role'] = 'dokter';
        $validatedData['color'] = '#3b82f6';

        Dokter::create($validatedData);

        session()->flash('success', 'Profil dokter berhasil ditambahkan. Silakan atur akun loginnya di menu Manajemen Admin jika diperlukan.');
        $this->resetForm();
    }

    public function edit($id)
    {
        $dokter = Dokter::findOrFail($id);
        $this->isEditing = true;
        $this->dokterId = $dokter->id;
        $this->nik = $dokter->nik;
        $this->nama_lengkap = $dokter->nama_lengkap;
        $this->email = $dokter->email;
        $this->spesialisasi = $dokter->spesialisasi;
        $this->tanggal_lahir = $dokter->tanggal_lahir;
        $this->golongan_darah = $dokter->golongan_darah;
        $this->no_hp = $dokter->no_hp;
    }

    public function update()
    {
        $validatedData = $this->validate([
            'nik' => 'nullable|string|unique:dokters,nik,' . $this->dokterId,
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:dokters,email,' . $this->dokterId,
            'spesialisasi' => 'required|string',
            'tanggal_lahir' => 'nullable|date',
            'golongan_darah' => 'nullable|string|max:5',
            'no_hp' => 'nullable|string|max:20',
        ]);
        
        $dokter = Dokter::findOrFail($this->dokterId);
        $dokter->update($validatedData);

        session()->flash('success', 'Profil dokter berhasil diperbarui.');
        $this->resetForm();
        $this->isEditing = false;
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->isEditing = false;
    }

    public function delete($id)
    {
        Dokter::findOrFail($id)->delete();
        session()->flash('success', 'Profil dokter berhasil dihapus.');
    }

    private function resetForm()
    {
        $this->reset(['nik', 'nama_lengkap', 'email', 'spesialisasi', 'tanggal_lahir', 'golongan_darah', 'no_hp']);
    }

    public function render()
    {
        return view('livewire.admin.tambah-dokter')
            ->layout('layouts.app');
    }
}