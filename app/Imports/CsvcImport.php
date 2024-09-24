<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CsvcImport implements ToModel,WithHeadingRow
{
    public $data = [];
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;
        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }

        if($dataExport[0] != null){
            $dataPost = (object) array(
                'stt'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'code'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'khuonvien'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'kyhieu'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'hinhthuc'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'dientichdat'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'vitrikhuonvien'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'diachi'    =>  $dataExport[7] != null ? $dataExport[7] : "",
                'tongso'    => ($dataExport[5] != null ? intval($dataExport[5]) : 0) * ($dataExport[6] != null ? intval($dataExport[6]) : 0)
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
