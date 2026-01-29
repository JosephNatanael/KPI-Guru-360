<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function index()
    {
        $gurus = Guru::latest()->paginate(10);
        return view('guru.index', compact('gurus'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            // nip removed
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $guru = Guru::create($request->all());

            // Create user for the guru
            // Generate dummy email from name
            $emailName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $guru->nama));
            \App\Models\User::create([
                'name' => $guru->nama,
                'email' => $emailName . rand(10,99) . '@gmail.com',
                'password' => bcrypt('password123'), // Default logic changed
                'role' => 'guru',
                'guru_id' => $guru->id,
            ]);

            DB::commit();

            return redirect()->route('guru.index')->with('success','Guru berhasil ditambahkan dan akun berhasil dibuat.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan guru: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $oldKelas = $guru->kelas; // simpan kelas lama untuk sinkronisasi wali murid

        $request->validate([
            'nama' => 'required',
            // nip removed
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $guru->update($request->all());

            // Jika kelas berubah, perbarui kelas di data wali murid yang memakai kelas tersebut
            $newKelas = $request->kelas;
            if ($oldKelas && $newKelas && $oldKelas !== $newKelas) {
                WaliMurid::where('kelas', $oldKelas)->update(['kelas' => $newKelas]);
            }

            DB::commit();

            return redirect()->route('guru.index')->with('success','Guru berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui guru: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Guru $guru)
    {
        try {
            $guru->delete();
            return redirect()->route('guru.index')->with('success','Guru berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->route('guru.index')->with('error', 'Gagal menghapus guru: ' . $e->getMessage());
        }
    }
}
