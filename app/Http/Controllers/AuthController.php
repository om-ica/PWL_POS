<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login() 
    { 
        if(Auth::check()){ // jika sudah login, maka redirect ke halaman home 
            return redirect('/'); 
        } 
        return view('auth.login'); 
    } 
 
    public function postlogin(Request $request) 
    { 
        if($request->ajax() || $request->wantsJson()){ 
            $credentials = $request->only('username', 'password'); 
 
            if (Auth::attempt($credentials)) { 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Login Berhasil', 
                    'redirect' => url('/') 
                ]); 
            } 
             
            return response()->json([ 
                'status' => false, 
                'message' => 'Login Gagal' 
            ]); 
        } 
 
        return redirect('login'); 
    }
 
    public function logout(Request $request) 
    { 
        Auth::logout(); 
 
        $request->session()->invalidate(); 
        $request->session()->regenerateToken();     
        return redirect('login'); 
    }

    public function register() 
    { 
        if (Auth::check()) { // Jika sudah login, redirect ke home 
            return redirect('/'); 
        }

        $level = LevelModel::select('level_id', 'level_nama')->get();

        return view('auth.register')->with('level', $level); // Menampilkan halaman register 
    }

    public function postregister(Request $request) 
    { 
        if ($request->ajax() || $request->wantsJson()) {
            // Persyaratan validasi untuk input user
            $rules = [
                'level_id' => 'required|integer',
                'username' => 'required|string|min:3|unique:m_user,username',
                'nama'     => 'required|string|max:100',
                'password' => 'required|min:5'
            ];

            // Proses validasi input
            $validator = Validator::make($request->all(), $rules);

            // Jika validasi gagal, kembalikan pesan error
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            // Simpan data pengguna baru dan langsung login
            $user = UserModel::create($request->all());
            Auth::login($user);

            // Kirim respon sukses dalam format JSON
            return response()->json([
                'status' => true,
                'message' => 'Registrasi berhasil'
            ]);
        }
        
        // Jika bukan AJAX, kembalikan ke halaman utama
        return redirect('/');
    }

}
