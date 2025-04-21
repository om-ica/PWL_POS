<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Welcomecontroller;
use Illuminate\Support\Facades\Route;

Route::pattern('id', '[0-9]+'); // artinya jika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postregister']);

Route::middleware(['auth'])->group(function () { // artinya semua route di dalam group ini harus login dulu
    Route::get('/', [Welcomecontroller::class, 'index']);
    Route::get('profile', [ProfileController::class, 'profile'])->name('profile');
    Route::post('/profile/upload', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::delete('profile/delete',[ProfileController::class, 'delete'])->name('profile.delete');

    Route::middleware(['authorize:ADM'])->group(function () {
        Route::group(['prefix' => 'user'], function () {
            Route::get('/', [UserController::class, 'index']); // menampilkan halaman awal user
            Route::post('/list', [UserController::class, 'list']); // menampilkan data user dalam bentuk JSON untuk datatables
            Route::get('/create', [UserController::class, 'create']); // menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']); // menyimpan data user baru
            Route::get('/create_ajax', [UserController::class, 'create_ajax']); // menampilkan halaman form untuk tambah user Ajax
            Route::post('/ajax', [UserController::class, 'store_ajax']); // menyimpam data user baru Ajax
            Route::get('/{id}', [UserController::class, 'show']); // menampilkan detail user
            Route::get('/{id}/show_ajax', [UserController::class, 'show_ajax']); // menampilkan detail user menggunakan ajax
            Route::get('/{id}/edit', [UserController::class, 'edit']); // menampilkan halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']); // menyimpan perubahan data user
            Route::get('/{id}/edit_ajax', [UserController::class, 'edit_ajax']); // menampilkan halaman form edit user ajax
            Route::put('/{id}/update_ajax', [UserController::class, 'update_ajax']); // menyimpan perubahan data user ajax
            Route::get('/{id}/delete_ajax', [UserController::class, 'confirm_ajax']); // menampilkan halaman form delete user ajax
            Route::delete('/{id}/delete_ajax', [UserController::class, 'delete_ajax']); // menghapus data user menggunakan ajax
            Route::delete('/{id}', [UserController::class, 'destroy']); // menghapus data user
            Route::get('/import', [UserController::class, 'import']); //form ajax untuk mengelola form excel
            Route::post('/import_ajax', [UserController::class, 'import_ajax']); //untuk mengelola import data User
            Route::get('/export_excel', [UserController::class, 'export_excel']); // untuk mengekspor data User ke excel
            Route::get('/export_pdf', [UserController::class, 'export_pdf']); // untuk mengekspor data User ke pdf
        });
    });

    Route::middleware(['authorize:ADM'])->group(function () {
        Route::group(['prefix' => 'level'], function () {
            Route::get('/', [LevelController::class, 'index']); // menampilkan halaman awal level
            Route::post('/list', [LevelController::class, 'list']); // menampilkan data level dalam bentuk JSON untuk datatables
            Route::get('/create', [LevelController::class, 'create']); // menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']); // menyimpan data level baru
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']); // menampilkan halaman form untuk tambah level Ajax
            Route::post('/ajax', [LevelController::class, 'store_ajax']); // menyimpam data level baru Ajax
            Route::get('/{id}', [LevelController::class, 'show']); // menampilkan detail level
            Route::get('/{id}/show_ajax', [LevelController::class, 'show_ajax']); // menampilkan detail level menggunakan ajax
            Route::get('/{id}/edit', [LevelController::class, 'edit']); // menampilkan halaman form edit level
            Route::put('/{id}', [LevelController::class, 'update']); // menyimpan perubahan data level
            Route::get('/{id}/edit_ajax', [LevelController::class, 'edit_ajax']); // menampilkan halaman form edit level ajax
            Route::put('/{id}/update_ajax', [LevelController::class, 'update_ajax']); // menyimpan perubahan data level ajax
            Route::get('/{id}/delete_ajax', [LevelController::class, 'confirm_ajax']); // menampilkan halaman form delete level ajax
            Route::delete('/{id}/delete_ajax', [LevelController::class, 'delete_ajax']); // menghapus data level menggunakan ajax
            Route::delete('/{id}', [LevelController::class, 'destroy']); // menghapus data level
            Route::get('/import', [LevelController::class, 'import']); //form ajax untuk mengelola form excel
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']); //untuk mengelola import data Level
            Route::get('/export_excel', [LevelController::class, 'export_excel']); // untuk mengekspor data Level ke excel
            Route::get('/export_pdf', [LevelController::class, 'export_pdf']); // untuk mengekspor data Level ke pdf
        });
    });

    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'kategori'], function () {
            Route::get('/', [KategoriController::class, 'index']); // menampilkan halaman awal kategori
            Route::post('/list', [KategoriController::class, 'list']); // menampilkan data kategori dalam bentuk JSON untuk datatables
            Route::get('/create', [KategoriController::class, 'create']); // menampilkan halaman form tambah kategori
            Route::post('/', [KategoriController::class, 'store']); // menyimpan data kategori baru
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']); // menampilkan halaman form untuk tambah kategori Ajax
            Route::post('/ajax', [KategoriController::class, 'store_ajax']); // menyimpam data kategori baru Ajax
            Route::get('/{id}/show_ajax', [KategoriController::class, 'show_ajax']); // menampilkan detail kategori menggunakan ajax
            Route::get('/{id}', [KategoriController::class, 'show']); // menampilkan detail kategori
            Route::get('/{id}/edit', [KategoriController::class, 'edit']); // menampilkan halaman form edit kategori
            Route::put('/{id}', [KategoriController::class, 'update']); // menyimpan perubahan data kategori
            Route::get('/{id}/edit_ajax', [KategoriController::class, 'edit_ajax']); // menampilkan halaman form edit kategori ajax
            Route::put('/{id}/update_ajax', [KategoriController::class, 'update_ajax']); // menyimpan perubahan data kategori ajax
            Route::get('/{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']); // menampilkan halaman form delete kategori ajax
            Route::delete('/{id}/delete_ajax', [KategoriController::class, 'delete_ajax']); // menghapus data kategori menggunakan ajax
            Route::delete('/{id}', [KategoriController::class, 'destroy']); // menghapus data kategori
            Route::get('/import', [KategoriController::class, 'import']); //form ajax untuk mengelola form excel
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']); //untuk mengelola import data Kategori
            Route::get('/export_excel', [KategoriController::class, 'export_excel']); // untuk mengekspor data Kategori ke excel
            Route::get('/export_pdf', [KategoriController::class, 'export_pdf']); // untuk mengekspor data Kategori ke pdf
        });
    });

    Route::middleware(['authorize:ADM,MNG'])->group(function () {
        Route::group(['prefix' => 'supplier'], function () {
            Route::get('/', [SupplierController::class, 'index']); // menampilkan halaman awal supplier
            Route::post('/list', [SupplierController::class, 'list']); // menampilkan data supplier dalam bentuk JSON untuk datatables
            Route::get('/create', [SupplierController::class, 'create']); // menampilkan halaman form tambah supplier
            Route::post('/', [SupplierController::class, 'store']); // menyimpan data supplier baru
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']); // menampilkan halaman form untuk tambah supplier Ajax
            Route::post('/ajax', [SupplierController::class, 'store_ajax']); // menyimpan data supplier baru Ajax
            Route::get('/{id}/show_ajax', [SupplierController::class, 'show_ajax']); // menampilkan detail supplier menggunakan ajax
            Route::get('/{id}', [SupplierController::class, 'show']); // menampilkan detail supplier
            Route::get('/{id}/edit', [SupplierController::class, 'edit']); // menampilkan halaman form edit supplier
            Route::put('/{id}', [SupplierController::class, 'update']); // menyimpan perubahan data supplier
            Route::get('/{id}/edit_ajax', [SupplierController::class, 'edit_ajax']); // menampilkan halaman form edit supplier ajax
            Route::put('/{id}/update_ajax', [SupplierController::class, 'update_ajax']); // menyimpan perubahan data supplier ajax
            Route::get('/{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']); // menampilkan halaman form delete supplier ajax
            Route::delete('/{id}/delete_ajax', [SupplierController::class, 'delete_ajax']); // menghapus data supplier menggunakan ajax
            Route::delete('/{id}', [SupplierController::class, 'destroy']); // menghapus data supplier
            Route::get('/import', [SupplierController::class, 'import']); //form ajax untuk mengelola form excel
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']); //untuk mengelola import data supplier
            Route::get('/export_excel', [SupplierController::class, 'export_excel']); // untuk mengekspor data supplier ke excel
            Route::get('/export_pdf', [SupplierController::class, 'export_pdf']); // untuk mengekspor data supplier ke pdf
        });
    });

    Route::middleware(['authorize:ADM,MNG,STF'])->group(function () {
        Route::group(['prefix' => 'barang'], function () {
            Route::get('/', [BarangController::class, 'index']); // menampilkan halaman awal barang
            Route::post('/list', [BarangController::class, 'list']); // menampilkan data barang dalam bentuk JSON untuk datatables
            Route::get('/create', [BarangController::class, 'create']); // menampilkan halaman form tambah barang
            Route::post('/', [BarangController::class, 'store']); // menyimpan data barang baru
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']); // menampilkan halaman form untuk tambah barang Ajax
            Route::post('/ajax', [BarangController::class, 'store_ajax']); // menyimpan data barang baru Ajax
            Route::get('/{id}/show_ajax', [BarangController::class, 'show_ajax']); // menampilkan detail barang menggunakan ajax
            Route::get('/{id}', [BarangController::class, 'show']); // menampilkan detail barang
            Route::get('/{id}/edit', [BarangController::class, 'edit']); // menampilkan halaman form edit barang
            Route::put('/{id}', [BarangController::class, 'update']); // menyimpan perubahan data barang
            Route::get('/{id}/edit_ajax', [BarangController::class, 'edit_ajax']); // menampilkan halaman form edit barang ajax
            Route::put('/{id}/update_ajax', [BarangController::class, 'update_ajax']); // menyimpan perubahan data barang ajax
            Route::get('/{id}/delete_ajax', [BarangController::class, 'confirm_ajax']); // menampilkan halaman form delete barang ajax
            Route::delete('/{id}/delete_ajax', [BarangController::class, 'delete_ajax']); // menghapus data barang menggunakan ajax
            Route::delete('/{id}', [BarangController::class, 'destroy']); // menghapus data barang
            Route::get('/import', [BarangController::class, 'import']); // form ajax untuk menunggah excel data barang
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']); //untuk mengelola import data barang
            Route::get('/export_excel', [BarangController::class, 'export_excel']); // untuk mengekspor data barang ke excel
            Route::get('/export_pdf', [BarangController::class, 'export_pdf']); // untuk mengekspor data barang ke pdf
        });
    });
});