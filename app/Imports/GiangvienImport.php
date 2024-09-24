<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GiangvienImport implements ToModel,WithHeadingRow
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
                'hoten'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'chucvu'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'namsinh'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'gioitinh'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'trinhdo'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'chucdanh'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'doituong'    =>  $dataExport[7] != null ? $dataExport[7] : "",
                'cholamrb'    =>  $dataExport[8] != null ? $dataExport[8] : "",

            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
