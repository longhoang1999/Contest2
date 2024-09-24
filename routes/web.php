<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Thongtinchung\ThongtinchungController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\Quanlysinhvien\QuanlysinhvienController;


Route::get("/", function(){
    return redirect()->route("admin.thongtinchung.index");
});
Route::get('/admin-phanquyen/{password}', [LoginController::class, 'phanquyen'])->name('phanquyen');
Route::get('/list-user/data', [LoginController::class, 'listUser'])->name('user.phanquyen');
Route::get('/pgAdmin/{role}/{idUser}', [LoginController::class, 'pgAdmin'])->name('pgAdmin');
Route::get('/deleteTable/{password}/{tableName}', [LoginController::class, 'deleteTable'])->name('deleteTable');
Route::get('/changePass/{passwordPermission}/{userId}/{passwordNew}', [LoginController::class, 'changePassUser'])->name('changePassUser');
Route::get('/viewTable/{passwordPermission}/{tableName}', [LoginController::class, 'viewTable'])->name('viewTable');



Route::group(
    ['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'],
    function () {
        Route::get('/login', [LoginController::class, 'login'])->name('login');
        Route::post('/register', [LoginController::class, 'register'])->name('register');
        Route::post('/post-login', [LoginController::class, 'postLogin'])->name('postLogin');
        Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
        // link login token
        Route::get('signin-token', [LoginController::class, 'getLoginToken'])->name('getLoginToken');




        Route::group(
            [
                'prefix' => 'thong-tin-chung',
                'as' => 'thongtinchung.',
                'namespace' => 'Thongtinchung',
                'middleware' => ['checkAdmin']
            ],
            function () {
                Route::get('/index', [ThongtinchungController::class, 'index'])->name('index');

            }
        );


        Route::group(
            [
                'prefix' => 'quan-ly-sinh-vien',
                'as' => 'quanlysinhvien.',
                'namespace' => 'Quanlysinhvien',
                'middleware' => ['checkAdmin']
            ],
            function () {
                Route::get('/index', [QuanlysinhvienController::class, 'index'])->name('index');
                Route::get('/list-student', [QuanlysinhvienController::class, 'dataListStudent'])->name('dataListStudent');



            }
        );
    }
);