<?php

namespace App\Http\Controllers;

use App\Models\Guru;
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
            'jabatan' => 'required',
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
            'mata_pelajaran' => 'nullable',
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
        $request->validate([
            'nama' => 'required',
            'nip' => 'required|unique:gurus,nip,' . $guru->id,
            'jabatan' => 'required',
            'is_wali_kelas' => 'required|boolean',
            'kelas' => 'nullable',
            'mata_pelajaran' => 'nullable',
        ]);

        $guru->update($request->all());

        return redirect()->route('guru.index')->with('success','Guru berhasil diperbarui');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success','Guru berhasil dihapus');
    }
}
