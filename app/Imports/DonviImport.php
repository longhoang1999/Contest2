<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Cartalyst\Sentinel\Native\Facades\Sentinel;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DonviImport implements ToModel,WithHeadingRow
{
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;
        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }

        if($dataExport[0] != null){
            $dataPost = array(
                'ma_donvi'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'ten_donvi'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'trang_thai'    => 'active',
                'nguoi_tao'     => Sentinel::getUser()->id
            );
           DB::table('donvi')->insert($dataPost);
        }
    }
}
