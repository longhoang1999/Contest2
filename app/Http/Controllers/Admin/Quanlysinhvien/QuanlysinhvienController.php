<?php

namespace App\Http\Controllers\Admin\Quanlysinhvien;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Shared\OLE\PPS;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;


class QuanlysinhvienController extends Controller
{
    public function index(){
        return view('admin/Quanlysinhvien/quanlysinhvien');
    }


    public function dataListStudent(){
        $role = Sentinel::findRoleById(2);
        $users = $role->users()->whereNull('deleted_at');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn(
                'user_image',
                function ($user) {
                    if($user->image == null || $user->image == ""){
                        return "<img src='". asset('userinfo_backup/default/male.jpg') ."' width='50'>";
                    }
                    return "<img src='". asset($user->image) ."' width='50'>";
                }
            )

            ->addColumn(
                'action',
                function ($user) {
                    $action="
                        <div class='btn-group'>
                            <button data-bs-toggle='modal' data-bs-target='#modalDetail' class='btn btn-info btn-sm' data-bs-id='$user->id'>Chi tiết</button>
                            <button data-bs-toggle='modal' data-bs-target='#modalUpdate' class='btn btn-warning btn-sm' data-bs-id='$user->id'>Sửa</button>
                            <button data-bs-toggle='modal' data-bs-target='#modalLock' class='btn btn-danger btn-sm' data-bs-id='$user->id' data-bs-name='$user->name'>Khóa</button>
                        </div>
                    ";
                    return $action;
                }
            )
            ->rawColumns(['user_image','action'])
            ->make(true);
    }


    public function detailStudent(Request $req){
        $user = User::withTrashed()->where('id', $req->id)
            ->select('name', 'gender',
                DB::raw("COALESCE(image, 'userinfo_backup/default/male.jpg') as image"),
                'email', 'phone')
            ->first();
        return json_encode($user);
    }

    public function updateStudent(Request $req){
        $validator = Validator::make($req->all(), [
            'id'    => 'required',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $req->id,
            'gender' => 'required|in:Nam,Nữ',
        ]);

        // Nếu validate thất bại trả lại thông báo lỗi qua view để hiện thị
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Lưu trữ ảnh
        $user = User::where('id', $req->id)->first();
        $imagePath =  $user->image;
        if ($req->hasFile('image')) {
            $image = $req->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->move(public_path('userinfo/' . $user->code), $imageName);
            $imagePath = 'userinfo/' . $user->code . '/' .$imageName;
        }
        $data = [
            'name' => $req->name,
            'image' => $imagePath,
            'email' => $req->email,
            'gender' => $req->gender,
            'phone' => $req->phone,

        ];
        User::where('id', $req->id)->update($data);

        return redirect()->route('admin.quanlysinhvien.index')->with([
            'success'   => 'Chỉnh sửa tài khoản thành công'
        ]);
    }

    public function lockStudent(Request $req){
        $validator = Validator::make($req->all(), [
            'id'    => 'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back();
        }
        User::where('id', $req->id)->delete();
        return redirect()->route('admin.quanlysinhvien.index')->with([
            'success' => 'Khóa tài khoản thành công'
        ]);
    }


    public function registerUser(){
        return view('admin.Quanlysinhvien.dangkysinhvien');
    }

    public function registerPostUser(Request $req){
        $validator = Validator::make($req->all(), [
            'student_code' => 'required',
            'student_name'  => 'required',
            'student_username'  => 'required',
            'file' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            'student_email' => 'nullable|email|unique:users,email',
            'student_gender'    => 'required|in:Nam,Nữ',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $randomFolderName = Str::random(10);
        if ($folderName = $this->makeDir($randomFolderName)) {
            // Lưu trữ ảnh
            $imagePath =  "";
            if ($req->hasFile('file')) {
                $image = $req->file('file');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $imagePath = $image->move(public_path('userinfo/' . $folderName), $imageName);
                $imagePath = 'userinfo/' . $folderName . '/' .$imageName;
            }

            // Tạo user
            $data = [
                'ma_nhansu' => $req->student_code,
                'name'  => $req->student_name,
                'ten_dangnhap'  => $req->student_username,
                'image' => $imagePath,
                'email' => $req->student_email,
                'password' => $req->student_password == "" ? Hash::make($req->student_username) :  Hash::make($req->student_password),
                'gender'    =>  $req->student_gender,
                'phone' =>  $req->student_phone,
                'code'  => $folderName
            ];
            $user = User::create($data);

            Sentinel::activate(Sentinel::findById($user->id));
            $role = Sentinel::findRoleById(2);
            if ($role) {
                $role->users()->attach($user);
            }
            return redirect()->route('admin.quanlysinhvien.index')->with([
                'success' => 'Thêm tài khoản thành công'
            ]);
        }
        return redirect()->back()->with([
            'success' => 'Cố lỗi trong quá trình tạo tài khoản'
        ]);

    }

    public function makeDir($randomFolderName)
    {
        $folderPath = public_path('userinfo/' . $randomFolderName);
        if (!File::exists($folderPath)) {
            File::makeDirectory($folderPath, 0755, true);
            return $randomFolderName;
        } else {
            $randomFolderName = Str::random(10);
            return $this->makeDir($randomFolderName);
        }
    }


    public function lockUser(){
        return view('admin/Quanlysinhvien/sinhviendakhoa');
    }

    public function listLockStudent(){
        $role = Sentinel::findRoleById(2);
        $users = $role->users()->where('deleted_at', "<>", null);
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn(
                'user_image',
                function ($user) {
                    if($user->image == null || $user->image == ""){
                        return "<img src='". asset('userinfo_backup/default/male.jpg') ."' width='50'>";
                    }
                    return "<img src='". asset($user->image) ."' width='50'>";
                }
            )

            ->addColumn(
                'action',
                function ($user) {
                    $action="
                        <div class='btn-group'>
                            <button data-bs-toggle='modal' data-bs-target='#modalDetail' class='btn btn-info btn-sm' data-bs-id='$user->id'>Chi tiết</button>
                        </div>
                    ";
                    return $action;
                }
            )
            ->rawColumns(['user_image','action'])
            ->make(true);
    }


    public function restoreStudent(Request $req){
        if($req->id_restore){
            $userId = $req->id_restore;
            $user = User::onlyTrashed()->where('id', $userId)->first();
            if ($user) {
                $user->restore();
            }
            return redirect()->route('admin.quanlysinhvien.index')->with([
                'success' => 'Khôi phục tài khoản thành công'
            ]);
        }
        return redirect()->back()->with([
            'success' => 'Cố lỗi trong quá trình khôi phục'
        ]);
    }


    public function confirmRegister(){
        return view('admin/Quanlysinhvien/xacminhdangky');
    }

    public function listRegisterStudent(){
        $users = User::where('is_register', '1');
        return DataTables::of($users)
            ->addIndexColumn()
            ->addColumn(
                'user_image',
                function ($user) {
                    if($user->image == null || $user->image == ""){
                        return "<img src='". asset('userinfo_backup/default/male.jpg') ."' width='50'>";
                    }
                    return "<img src='". asset($user->image) ."' width='50'>";
                }
            )

            ->addColumn(
                'action',
                function ($user) {
                    $action="
                        <div class='btn-group'>
                            <button data-bs-toggle='modal' data-bs-target='#modalDetail' class='btn btn-info btn-sm' data-bs-id='$user->id'>Chi tiết</button>
                        </div>
                    ";
                    return $action;
                }
            )
            ->rawColumns(['user_image','action'])
            ->make(true);
    }

    public function confirmStudent(Request $req){
        if($req->id_confirm){
            $user = Sentinel::findById($req->id_confirm);
            Sentinel::activate($user);


            $role = Sentinel::findRoleById(2);
            if ($role) {
                $role->users()->attach($user);
            }

            User::where('id', $req->id_confirm)->update([
                'is_register' => '0'
            ]);

            return redirect()->route('admin.quanlysinhvien.index')->with([
                'success' => 'Xác minh tài khoản thành công'
            ]);
        }
        return redirect()->back()->with([
            'success' => 'Cố lỗi trong quá trình xác minh tài khoản'
        ]);
    }
}