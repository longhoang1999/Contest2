<?php

namespace App\Http\Controllers\Admin\Thongtinchung;

use Illuminate\Http\Request;
use Cartalyst\Sentinel\Native\Facades\Sentinel;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;


class ThongtinchungController extends Controller
{
    public function index(){
        return view('admin/ThongTinChung/thongtinchung');
    }

}