<?php

use App\Http\Controllers\Admin\Answers\AnswerController;
use App\Http\Controllers\Admin\Categories\CategoryController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\Quanlysinhvien\QuanlysinhvienController;
use App\Http\Controllers\Admin\Questions\QuestionController;
use App\Http\Controllers\Admin\Thongtinchung\ThongtinchungController;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
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
                'middleware' => ['checkAdmin'],
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
                'middleware' => ['checkAdmin'],
            ],
            function () {
                // Danh sách sinh viên
                Route::get('/index', [QuanlysinhvienController::class, 'index'])->name('index');
                Route::get('/list-student', [QuanlysinhvienController::class, 'dataListStudent'])->name('dataListStudent');
                Route::get('/detail-student', [QuanlysinhvienController::class, 'detailStudent'])->name('detailStudent');
                Route::post('/update-student', [QuanlysinhvienController::class, 'updateStudent'])->name('updateStudent');
                Route::delete('/lock-student', [QuanlysinhvienController::class, 'lockStudent'])->name('lockStudent');

                // Đăng ký tài khoản
                Route::get('/register-user', [QuanlysinhvienController::class, 'registerUser'])->name('registerUser');
                Route::post('/register-user', [QuanlysinhvienController::class, 'registerPostUser'])->name('registerPostUser');

                // Tài khoản đã khóa
                Route::get('/lock-user', [QuanlysinhvienController::class, 'lockUser'])->name('lockUser');
                Route::get('/list-lock-student', [QuanlysinhvienController::class, 'listLockStudent'])->name('listLockStudent');
                Route::post('/restore-usser', [QuanlysinhvienController::class, 'restoreStudent'])->name('restoreStudent');

                // Xác minh đăng ký
                Route::get('/confirm-register', [QuanlysinhvienController::class, 'confirmRegister'])->name('confirmRegister');
                Route::get('/list-register-student', [QuanlysinhvienController::class, 'listRegisterStudent'])->name('listRegisterStudent');
                Route::post('/confirm-student', [QuanlysinhvienController::class, 'confirmStudent'])->name('confirmStudent');

            }
        );
        Route::group(
            [
                'prefix' => 'danh-muc',
                'as' => 'categories.',
                'namespace' => 'Categories',
                'middleware' => ['checkAdmin'],
            ],
            function () {
                Route::get('/index', [CategoryController::class, 'index'])->name('index');
                Route::get('/list-category', [CategoryController::class, 'dataListCategory'])->name('dataListCategory');
                Route::post('/store', [CategoryController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
                Route::put('/{id}', [CategoryController::class, 'update'])->name('update');
                Route::get('/{id}/show', [CategoryController::class, 'show'])->name('show');
                Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('destroy');
            }
        );
        Route::group(
            [
                'prefix' => 'cau-hoi',
                'as' => 'questions.',
                'namespace' => 'Questions',
                'middleware' => ['checkAdmin'],
            ],
            function () {
                Route::get('/index', [QuestionController::class, 'index'])->name('index');
                Route::get('/list-question', [QuestionController::class, 'dataListQuestion'])->name('dataListQuestion');
                Route::post('/store', [QuestionController::class, 'store'])->name('store');
                Route::get('/{id}/edit', [QuestionController::class, 'edit'])->name('edit');
                Route::put('/{id}', [QuestionController::class, 'update'])->name('update');
                Route::get('/{id}/show', [QuestionController::class, 'show'])->name('show');
                Route::delete('/{id}', [QuestionController::class, 'destroy'])->name('destroy');

                // Answer
                Route::group(
                    [
                        'prefix' => 'dap-an',
                        'as' => 'answers.',
                        'namespace' => 'Answers',
                        'middleware' => ['checkAdmin'],
                    ],
                    function () {
                        Route::get('/{id}', [AnswerController::class, 'getAnswers'])->name('index');
                        Route::post('/{id}/toggle', [AnswerController::class, 'toggleIsCorrect'])->name('toggle');
                        Route::post('/store', [AnswerController::class, 'store'])->name('store');
                        Route::get('/{id}/edit', [AnswerController::class, 'edit'])->name('edit');
                        Route::put('/{id}', [AnswerController::class, 'update'])->name('update');
                        Route::delete('/{id}', [AnswerController::class, 'destroy'])->name('destroy');
                    }
                );

            }
        );

    }
);