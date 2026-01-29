<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('guru')->paginate(10);
        return view('user.index', compact('users'));
    }

    public function create()
    {
        // Ambil daftar kelas dari guru yang menjadi wali kelas
        $kelasList = Guru::where('is_wali_kelas', 1)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('user.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        // Base validation
        $rules = [
            'name'  => 'required|string',
            'role'  => 'required|in:admin,kepala_sekolah,guru,wali_murid',
        ];

        // Email required if not wali_murid
        if ($request->role !== 'wali_murid') {
             $rules['email'] = 'required|email|unique:users';
        }

        // Add validation for Guru profile fields
        if ($request->role === 'guru') {
            // NIP removed
            $rules['kelas'] = 'nullable|string|max:10';
        }

        // Add validation for WaliMurid profile fields
        if ($request->role === 'wali_murid') {
            $rules['nama_anak'] = 'required|string|max:255';
            $rules['kelas_wali'] = 'required|string|max:10';
        }

        $request->validate($rules);

        // Generate temporary email for wali murid to pass creation
        $email = $request->email;
        if ($request->role === 'wali_murid') {
             $email = 'temp_' . uniqid() . '@wali.sekolah.id';
        }

        // Create User
        $user = User::create([
            'name'     => $request->name,
            'email'    => $email,
            'role'     => $request->role,
            'password' => Hash::make('password123') // password default
        ]);

        // Fix Email for Wali Murid: FirstnameLastnameInitial(ID)@gmail.com
        if ($request->role === 'wali_murid') {
             $namaWali = $request->name; // Use generic Name
             $parts = explode(' ', trim($namaWali));
             $firstName = strtolower($parts[0]);
             $lastNameInitial = isset($parts[1]) ? strtoupper(substr($parts[1], 0, 1)) : '';
             
             $newEmail = $firstName . $lastNameInitial . $user->id . '@gmail.com';
             
             $user->update(['email' => $newEmail]);
        }

        // Auto-create Guru profile if role is guru
        if ($request->role === 'guru') {
            Guru::create([
                'nama' => $request->name, // Use generic name
                // nip removed
                'is_wali_kelas' => $request->has('is_wali_kelas') ? 1 : 0,
                'kelas' => $request->kelas,
                'user_id' => $user->id
            ]);
        }

        // Auto-create WaliMurid profile if role is wali_murid
        if ($request->role === 'wali_murid') {
            \App\Models\WaliMurid::create([
                'nama' => $request->name, // Use generic name
                'nama_anak' => $request->nama_anak,
                'kelas' => $request->kelas_wali,
                'user_id' => $user->id
            ]);
        }

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        // Ambil daftar kelas dari guru yang menjadi wali kelas
        $kelasList = Guru::where('is_wali_kelas', 1)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('user.edit', compact('user', 'kelasList'));
    }

    public function update(Request $request, User $user)
    {
        // Base validation
        $rules = [
            'name'  => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,kepala_sekolah,guru,wali_murid',
        ];

        // Add validation for Guru profile fields
        if ($request->role === 'guru') {
            // NIP removed
            $rules['kelas'] = 'nullable|string|max:10';
        }

        // Add validation for WaliMurid profile fields
        if ($request->role === 'wali_murid') {
            $rules['nama_anak'] = 'required|string|max:255';
            $rules['kelas_wali'] = 'required|string|max:10';
        }

        $request->validate($rules);

        // Update User
        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $request->role,
        ]);

        // Handle Guru profile
        if ($request->role === 'guru') {
            if ($user->guru) {
                // Update existing profile
                $user->guru->update([
                    'nama' => $request->name, // Update from generic name
                    // nip removed
                    'is_wali_kelas' => $request->has('is_wali_kelas') ? 1 : 0,
                    'kelas' => $request->kelas,
                ]);
            } else {
                // Create new profile
                Guru::create([
                    'nama' => $request->name, // Update from generic name
                    // nip removed
                    'is_wali_kelas' => $request->has('is_wali_kelas') ? 1 : 0,
                    'kelas' => $request->kelas,
                    'user_id' => $user->id
                ]);
            }
        } else {
            // Jika role bukan guru, hapus profile guru jika ada
            if ($user->guru) {
                $user->guru->delete();
            }
        }

        // Handle WaliMurid profile
        if ($request->role === 'wali_murid') {
            if ($user->waliMurid) {
                // Update existing profile
                $user->waliMurid->update([
                    'nama' => $request->name, // From generic name
                    'nama_anak' => $request->nama_anak,
                    'kelas' => $request->kelas_wali,
                ]);
            } else {
                // Create new profile
                \App\Models\WaliMurid::create([
                    'nama' => $request->name, // From generic name
                    'nama_anak' => $request->nama_anak,
                    'kelas' => $request->kelas_wali,
                    'user_id' => $user->id
                ]);
            }
        } else {
            // Jika role bukan wali_murid, hapus profile wali murid jika ada
            if ($user->waliMurid) {
                $user->waliMurid->delete();
            }
        }

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}
