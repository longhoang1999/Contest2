<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class NghiencuuImport implements ToModel,WithHeadingRow
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

                'chosotk'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'soluong'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'heso'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'quydoi'    =>  intval($dataExport[3] != null ? $dataExport[3] : "0") * intval($dataExport[4] != null ? $dataExport[4] : "0"),
                'ghichu'    =>  $dataExport[5] != null ? $dataExport[5] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
