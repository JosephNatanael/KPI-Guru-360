<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect berdasarkan role
            if ($user->role === 'guru') {
                return redirect()->route('dashboard.guru');
            }
            
            if ($user->role === 'wali_murid') {
                return redirect()->route('evaluation.index');
            }
            
            // Kepala sekolah dan role lain → dashboard
            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
