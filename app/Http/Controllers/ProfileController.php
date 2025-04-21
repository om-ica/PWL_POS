<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\UserModel;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile() 
    {
        $breadcrumb = (object) [
            'title' => 'Profile',
            'list' => ['Home', 'Profile']
        ];
    
        $activeMenu = 'profile';
        return view('profile.index', ['breadcrumb' => $breadcrumb, 'activeMenu' => $activeMenu]);
    }

    public function updateProfile(Request $request)
    {
        // Validasi file foto yang diupload
        $request->validate([
            'foto' => 'image|mimes:jpg,jpeg,png,gif|max:2048', // Atur sesuai kebutuhan
        ]);

        $user = UserModel::find(Auth::id()); // mendapatkan user saat ini

        if (!$user) {
            return redirect()->route('profile')->with('error', 'User tidak ditemukan.');
        }        

        // Cek apakah ada file yang diupload
        if ($request->hasFile('foto')) {
            // Ambil file foto
            $foto = $request->file('foto');

            // Buat nama file yang unik
            $fotoName = time() . '.' . $foto->getClientOriginalExtension();

            // Simpan file di folder 'public/profile' dan dapatkan pathnya
            $fotoPath = $foto->storeAs('public/profile', $fotoName);

            // Simpan nama file foto di database
            $user->foto = $fotoName;

            // Simpan perubahan di database
            $user->save();
        }

        return redirect()->route('profile')->with('success', 'Foto profil berhasil diperbarui!');
    }

    public function delete()
    {
        $user = UserModel::find(Auth::id());

        if (!$user || !$user->foto) {
            return redirect()->route('profile')->with('error', 'Tidak ada foto untuk dihapus.');
        }

        // Hapus file foto dari storage jika ada
        $fotoPath = 'public/profile/' . $user->foto;
        if (Storage::exists($fotoPath)) {
            Storage::delete($fotoPath);
        }

        // Kosongkan kolom foto di database
        $user->foto = null;
        $user->save();

        return redirect()->route('profile')->with('success', 'Foto profil berhasil dihapus!');
    }

}
