<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CshdcImport implements ToModel,WithHeadingRow
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
                'maso'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'chisochinh'    =>  $dataExport[2] != null ? $dataExport[2] : "",
                'ctchienluoc'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'ketqua'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'ghichu'    =>  $dataExport[5] != null ? $dataExport[5] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}