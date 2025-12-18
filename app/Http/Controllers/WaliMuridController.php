<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WaliMurid;
use App\Models\Guru;
use Illuminate\Http\Request;

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
        // Ambil user yang berperan sebagai wali murid dan belum terhubung
        $users = User::where('role', 'wali_murid')
            ->whereDoesntHave('waliMurid')
            ->get();

        // Ambil daftar kelas dari guru yang menjadi wali kelas (distinct)
        $kelasList = Guru::where('is_wali_kelas', 1)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('wali-murid.create', compact('users', 'kelasList'));
    }

    /**
     * Simpan data wali murid baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id'   => 'required|exists:users,id|unique:wali_murids,user_id',
            'nama'      => 'required|string|max:191',
            'nama_anak' => 'required|string|max:191',
            'kelas'     => 'required|string|max:191',
        ]);

        WaliMurid::create($request->only('user_id', 'nama', 'nama_anak', 'kelas'));

        return redirect()->route('wali-murid.index')
            ->with('success', 'Data wali murid berhasil ditambahkan.');
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
            'user_id'   => 'required|exists:users,id|unique:wali_murids,user_id,' . $wali_murid->id,
            'nama'      => 'required|string|max:191',
            'nama_anak' => 'required|string|max:191',
            'kelas'     => 'required|string|max:191',
        ]);

        $wali_murid->update($request->only('user_id', 'nama', 'nama_anak', 'kelas'));

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


