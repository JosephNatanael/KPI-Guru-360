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
                // Cari data wali murid berdasarkan user_id
                $wali = WaliMurid::where('user_id', $user->id)->first();
                
                if ($wali) {
                    // Cari guru wali kelas sesuai kelas anak
                    $guruWaliKelas = Guru::where('is_wali_kelas', 1)
                        ->where('kelas', $wali->kelas)
                        ->first();
                    
                    if ($guruWaliKelas) {
                        // Langsung arahkan ke form penilaian wali kelas tersebut
                        return redirect()->route('evaluation.create', $guruWaliKelas->id);
                    }
                }
                
                // Jika tidak ada wali kelas, redirect ke evaluation index
                return redirect()->route('evaluation.index')
                    ->with('info', 'Guru wali kelas untuk kelas anak Anda belum diatur. Silakan hubungi admin.');
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
