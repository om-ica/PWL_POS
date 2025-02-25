<?php

namespace App\Http\Controllers;

use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
        public function index() 
        {
            //tambah data user dengan Eloquent Model
            $data =
            [
                'nama' => 'Pelanggan Pertama',
            ];
            UserModel::where('username', 'customer-1')->update($data); //update data user

            //Coba akses model UserModel
            $user = UserModel::all(); //Ambil semua data dari tabel user
            return view('user', ['data' => $user]);
        }
}
