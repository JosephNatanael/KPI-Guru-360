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
        // guru yang belum punya akun user
        $gurus = Guru::whereDoesntHave('user')->get();

        return view('user.create', compact('gurus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:users',
            'role'  => 'required|in:admin,kepala_sekolah,guru,wali_murid',
            'guru_id' => 'nullable|exists:gurus,id'
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'role'     => $request->role,
            'guru_id'  => $request->role === 'guru' ? $request->guru_id : null,
            'password' => Hash::make('password123') // password default
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit(User $user)
    {
        $gurus = Guru::all();

        return view('user.edit', compact('user', 'gurus'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role'  => 'required|in:admin,kepala_sekolah,guru,wali_murid',
            'guru_id' => 'nullable|exists:gurus,id'
        ]);

        $user->update([
            'name'    => $request->name,
            'email'   => $request->email,
            'role'    => $request->role,
            'guru_id' => $request->role === 'guru' ? $request->guru_id : null,
        ]);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus!');
    }
}
