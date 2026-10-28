<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\AdminUser;
use App\Models\Dokter;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\WithPagination;

class TambahAdmin extends Component
{
    use WithPagination;

    public $adminId;
    public $no_sap, $nik, $nama_lengkap, $email, $password;
    
    // 🌟 PERBAIKAN: Memisahkan Sumber Data dan Hak Akses
    public $tipe_personel = 'manual'; // manual, dokter, karyawan
    public $role = 'admin'; // superadmin, admin, dokter, karyawan
    
    public $editPasswordId = null;
    public $newPassword = '';
    public $isEditing = false;
    
    // Properti untuk Dokter
    public $listDokter = []; 
    public $selectedDokterId; 

    // Properti untuk Pencarian Karyawan (AUTOCOMPLETE)
    public $searchQuery = ''; 
    public $searchedKaryawans = []; 
    public $selectedKaryawanId = null; 
    public $karyawanFound = false; 

    protected $rules = [
        'no_sap' => 'nullable|string|max:50',
        'nama_lengkap' => 'required|string|max:255',
        'email' => 'required|email',
        'role' => 'required|string',
        'nik' => 'nullable|string',
    ];
    
    protected $passwordRules = [
        'newPassword' => 'required|min:6',
    ];

    public function mount()
    {
        // CEK HAK AKSES (GATEKEEPER)
        if (!in_array(auth()->user()->role, ['superadmin', 'admin'])) {
            abort(403, 'Akses Ditolak! Halaman ini khusus untuk Superadmin/Admin.');
        }

        $this->listDokter = Dokter::pluck('nama_lengkap', 'id');
    }

    public function getAdminUsersProperty()
    {
        return AdminUser::with('dokter')->latest()->paginate(10);
    }

    protected function getValidationRules()
    {
        $rules = $this->rules;
        $rules['email'] = [
            'required',
            'email',
            Rule::unique('admin_users', 'email')->ignore($this->adminId), 
        ];
        
        if (!$this->isEditing && !$this->adminId) {
             $rules['password'] = 'required|min:6';
        }
        
        // Validasi SAP diabaikan keunikan ketatnya disini agar fleksibel jika kosong
        if (!empty($this->no_sap)) {
             $rules['no_sap'] = [
                 'nullable',
                 'string',
                 'max:50',
                 Rule::unique('admin_users', 'no_sap')->ignore($this->adminId)
             ];
        }

        return $rules;
    }

    // 🌟 PERBAIKAN: Reset form HANYA saat sumber data (tipe personel) diubah
    public function updatedTipePersonel($value)
    {
        $this->reset(['no_sap', 'nik', 'nama_lengkap', 'email', 'selectedDokterId', 'searchQuery', 'searchedKaryawans', 'selectedKaryawanId', 'karyawanFound']);
        
        // Auto-suggest role agar praktis, tapi user tetap bisa ubah
        if ($value === 'dokter') $this->role = 'dokter';
        if ($value === 'manual') $this->role = 'admin';
    }

    public function updatedSearchQuery($value)
    {
        $this->reset(['nik', 'nama_lengkap', 'email', 'no_sap', 'selectedKaryawanId', 'karyawanFound']);
        
        if ($this->tipe_personel !== 'karyawan' || strlen($value) < 2) { 
            $this->searchedKaryawans = [];
            return;
        }

        $this->searchedKaryawans = Karyawan::where('no_sap', 'like', '%'.$value.'%')
            ->orWhere('nama_karyawan', 'like', '%'.$value.'%')
            ->limit(5)
            ->get();
    }
    
    public function selectKaryawan($id)
    {
        $karyawan = Karyawan::find($id);
        if ($karyawan) {
            $this->no_sap = $karyawan->no_sap;
            $this->nama_lengkap = $karyawan->nama_karyawan;
            $this->nik = $karyawan->nik_karyawan;
            $this->email = $karyawan->email;
            
            $this->selectedKaryawanId = $id;
            $this->karyawanFound = true; 
            
            $this->searchQuery = $karyawan->no_sap . ' - ' . $karyawan->nama_karyawan;
            $this->searchedKaryawans = []; 
        }
    }
    
    public function updatedSelectedDokterId($value)
    {
        $this->reset(['no_sap', 'nik', 'nama_lengkap', 'email', 'selectedKaryawanId', 'karyawanFound']);
        
        if (!empty($value)) {
            $dokter = Dokter::find($value);
            if ($dokter) {
                $this->nama_lengkap = $dokter->nama_lengkap;
                $this->nik = $dokter->nik;
                $this->email = $dokter->email;
                $this->karyawanFound = true; 
            }
        } else {
            $this->reset(['nama_lengkap', 'nik', 'email', 'no_sap', 'karyawanFound']);
        }
    }

    public function save()
    {
        $this->validate($this->getValidationRules());
        
        $dokterId = null;
        $karyawanId = null;
        
        // 🌟 PERBAIKAN: Validasi berdasarkan tipe personel, bukan role
        if ($this->tipe_personel === 'dokter') {
            $dokterId = $this->selectedDokterId;
            if (empty($dokterId)) {
                 session()->flash('error', 'Silakan pilih Dokter dari daftar.');
                 return;
            }
        } elseif ($this->tipe_personel === 'karyawan') {
            $karyawanId = $this->selectedKaryawanId;
            if (empty($karyawanId)) {
                session()->flash('error', 'Silakan cari dan pilih Karyawan yang valid.');
                return;
            }
        }

        AdminUser::create([
            'no_sap' => $this->no_sap,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik, 
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'dokter_id' => $dokterId, 
            'karyawan_id' => $karyawanId, 
        ]);

        session()->flash('success', 'Akun pengguna sistem berhasil ditambahkan.');
        $this->reset(['no_sap', 'nama_lengkap', 'nik', 'email', 'password', 'selectedDokterId', 'selectedKaryawanId', 'searchQuery', 'karyawanFound']);
    }

    public function edit($id)
    {
        $admin = AdminUser::findOrFail($id);
        $this->isEditing = true;
        $this->adminId = $admin->id;
        $this->no_sap = $admin->no_sap;
        $this->nama_lengkap = $admin->nama_lengkap;
        $this->nik = $admin->nik;
        $this->email = $admin->email;
        $this->role = $admin->role;

        // 🌟 PERBAIKAN: Menentukan ulang tipe personel saat mode edit
        if ($admin->karyawan_id) {
            $this->tipe_personel = 'karyawan';
            $this->selectedKaryawanId = $admin->karyawan_id;
            $this->karyawanFound = true;
        } elseif ($admin->dokter_id) {
            $this->tipe_personel = 'dokter';
            $this->selectedDokterId = $admin->dokter_id;
            $this->karyawanFound = true;
        } else {
            $this->tipe_personel = 'manual';
            $this->karyawanFound = false;
        }
    }

    public function update()
    {
        $this->validate($this->getValidationRules());

        $admin = AdminUser::findOrFail($this->adminId);
        $admin->update([
            'no_sap' => $this->no_sap,
            'nama_lengkap' => $this->nama_lengkap,
            'nik' => $this->nik,
            'email' => $this->email,
            'role' => $this->role,
        ]);

        session()->flash('success', 'Data admin berhasil diperbarui.');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->reset(['adminId', 'no_sap', 'nama_lengkap', 'nik','email', 'role', 'tipe_personel', 'selectedKaryawanId', 'selectedDokterId', 'karyawanFound', 'searchQuery']);
        $this->isEditing = false;
    }

    public function editPassword($id)
    {
        $this->editPasswordId = $id;
        $this->newPassword = '';
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules);
        $admin = AdminUser::find($this->editPasswordId);
        $admin->update([
            'password' => Hash::make($this->newPassword),
        ]);
        session()->flash('success', 'Password akun berhasil diperbarui.');
        $this->reset(['editPasswordId', 'newPassword']);
    }

    public function delete($id)
    {
        AdminUser::find($id)->delete();
        session()->flash('success', 'Akun berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.admin.tambah-admin')
            ->layout('layouts.app');
    }
}