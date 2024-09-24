<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\File;


class NhansuImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;
        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }

        $chuoiRandom = $this->generateRandomString();
        if($dataExport[0] != null){
            $password = $dataExport[1] != null ? Hash::make($dataExport[1]) : "";
            $email = $dataExport[3] != null ? $dataExport[3] : "";
            $code_donvi = DB::table('donvi')->where('ma_donvi', $dataExport[4] != null ? $dataExport[4] : "")->select('id')->first();
            $dataPost = array(
                'ma_nhansu'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'ten_dangnhap'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'password'   =>  $password,
                'name'  => $dataExport[2] != null ? $dataExport[2] : "",
                'email' =>  $email,
                'donvi_id'  => $code_donvi ? $code_donvi->id : '',
                'code' => $chuoiRandom ,
                'gender' => 'Nam',
            );

            $user = Sentinel::registerAndActivate([
                'email' => $email,
                'password'  => $password,
            ]);

            DB::table('users')->where("id", $user->id)->update($dataPost);

            // create file code
            $path = public_path(). "/userinfo/" . $chuoiRandom;
            File::makeDirectory($path, $mode = 0777, true, true);

             // Attach role
            $user = Sentinel::findById($user->id);
            $role = Sentinel::findRoleByName('user');
            $role->users()->attach($user);


        }
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
}
