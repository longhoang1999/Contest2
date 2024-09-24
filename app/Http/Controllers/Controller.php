<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Cartalyst\Sentinel\Native\Facades\Sentinel;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    /// CHeck phân quyền ở đây
    public function checkQuyen($solieutk){
        $phanquyen_bcdg = DB::table('phanquyen_bcdg')->where('thongkelbcdg_solieu', $solieutk)->first();
        $user = Sentinel::getUser();
        if($user->inRole('admin')){
            return true;
        }
        if($phanquyen_bcdg){
            $donvi = DB::table('donvi')->where("id", $user->donvi_id)->first();
            if($donvi){
                $arrDonvi = explode("|", $phanquyen_bcdg->donvi_id);

                if($user->id ==  $donvi->truong_dv && in_array($user->donvi_id, $arrDonvi)){
                    return true;
                }else if(in_array($user->donvi_id, $arrDonvi)){
                    if($phanquyen_bcdg->nhansu_id != null){
                        $arr = explode("|", $phanquyen_bcdg->nhansu_id);
                        if(in_array( $user->id, $arr)){
                            return true;
                        }
                    }
                }
            }
        }
        return false;
    }
}