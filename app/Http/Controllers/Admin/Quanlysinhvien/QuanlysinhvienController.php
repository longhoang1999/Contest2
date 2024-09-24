<?php

namespace App\Http\Controllers\Admin\Quanlysinhvien;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Yajra\Datatables\Datatables;


class QuanlysinhvienController extends Controller
{
    public function index(){
        return view('admin/Quanlysinhvien/quanlysinhvien');
    }


    public function dataListStudent(){
        $role = Sentinel::findRoleById(2);
        $users = $role->users();
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn(
                'user_image',
                function ($user) {
                    return "<img src='". asset($user->image) ."' width='100'>";
                }
            )

            ->addColumn(
                'action',
                function ($user) {
                    $action="
                        <div class='btn-group'>
                            <button class='btn btn-info btn-sm'>Chi tiết</button>
                            <button class='btn btn-warning btn-sm'>Sửa</button>
                            <button class='btn btn-danger btn-sm'>Xóa</button>
                        </div>
                    ";
                    return $action;
                }
            )
            ->rawColumns(['user_image','action'])
            ->make(true);
    }



}