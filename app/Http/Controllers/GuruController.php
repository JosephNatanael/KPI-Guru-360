<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\WaliMurid;
use Illuminate\Http\Request;

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
            'nip' => 'required|unique:gurus',
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
        ]);

        Guru::create($request->all());

        return redirect()->route('guru.index')->with('success','Guru berhasil ditambahkan');
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
            'nip' => 'required|unique:gurus,nip,' . $guru->id,
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
        ]);

        $guru->update($request->all());

        // Jika kelas berubah, perbarui kelas di data wali murid yang memakai kelas tersebut
        $newKelas = $request->kelas;
        if ($oldKelas && $newKelas && $oldKelas !== $newKelas) {
            WaliMurid::where('kelas', $oldKelas)->update(['kelas' => $newKelas]);
        }

        return redirect()->route('guru.index')->with('success','Guru berhasil diperbarui');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success','Guru berhasil dihapus');
    }
}
