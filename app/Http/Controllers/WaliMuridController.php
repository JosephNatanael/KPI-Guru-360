<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaliMurid;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WaliMuridController extends Controller
{
    /**
     * Tampilkan daftar wali murid.
     */
    public function index()
    {
        $waliMurids = WaliMurid::with('user')->latest()->paginate(10);

        return view('wali-murid.index', compact('waliMurids'));
    }

    /**
     * Form tambah wali murid.
     */
    public function create()
    {
        // Ambil daftar kelas dari guru yang menjadi wali kelas (distinct)
        $kelasList = Guru::where('is_wali_kelas', 1)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('wali-murid.create', compact('kelasList'));
    }

    /**
     * Simpan data wali murid baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:191',
            'nama_anak' => 'required|string|max:191',
            'kelas'     => 'required|string|max:191',
            'email'     => 'required|email|unique:users,email',
        ]);

        // Create User with input email
        $user = User::create([
            'name' => $request->nama,
            'email' => $request->email,
            'password' => bcrypt('password'), // default password
            'role' => 'wali_murid',
        ]);
        
        // Auto email generation removed.

        // Create WaliMurid linked to User
        WaliMurid::create([
            'user_id'   => $user->id,
            'nama'      => $request->nama,
            'nama_anak' => $request->nama_anak,
            'kelas'     => $request->kelas,
        ]);

        return redirect()->route('wali-murid.index')
            ->with('success', 'Data wali murid berhasil ditambahkan. Akun dibuat dengan Email: ' . $request->email . ' dan Password: password.');
    }

    /**
     * Form edit wali murid.
     */
    public function edit(WaliMurid $wali_murid)
    {
        // User yang bisa dipilih: wali_murid dan
        // - belum punya record wali_murids, atau
        // - adalah user yang sekarang terhubung
        $users = User::where('role', 'wali_murid')
            ->where(function ($q) use ($wali_murid) {
                $q->whereDoesntHave('waliMurid')
                  ->orWhere('id', $wali_murid->user_id);
            })
            ->get();

        // Daftar kelas wali kelas untuk dropdown
        $kelasList = Guru::where('is_wali_kelas', 1)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('wali-murid.edit', [
            'waliMurid' => $wali_murid,
            'users'     => $users,
            'kelasList' => $kelasList,
        ]);
    }

    /**
     * Update data wali murid.
     */
    public function update(Request $request, WaliMurid $wali_murid)
    {
        $request->validate([
            // 'user_id'   => 'required|exists:users,id|unique:wali_murids,user_id,' . $wali_murid->id, // Removed as we are editing the existing user/wali relation or simple profile
            'nama'      => 'required|string|max:191',
            'nama_anak' => 'required|string|max:191',
            'kelas'     => 'required|string|max:191',
            'email'     => 'required|email|unique:users,email,' . $wali_murid->user_id,
        ]);

        $wali_murid->update($request->only('nama', 'nama_anak', 'kelas'));
        
        // Update user email and name if changed
        $wali_murid->user->update([
            'name' => $request->nama,
            'email' => $request->email
        ]);

        return redirect()->route('wali-murid.index')
            ->with('success', 'Data wali murid berhasil diperbarui.');
    }

    /**
     * Hapus data wali murid.
     */
    public function destroy(WaliMurid $wali_murid)
    {
        $wali_murid->delete();

        return redirect()->route('wali-murid.index')
            ->with('success', 'Data wali murid berhasil dihapus.');
    }
}


