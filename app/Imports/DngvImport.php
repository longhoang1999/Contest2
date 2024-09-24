<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DngvImport implements ToModel,WithHeadingRow
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
            $tongso = ($dataExport[3] != null ? intval($dataExport[3]) : 0 ) +
            ($dataExport[4] != null ? intval($dataExport[4]) : 0 ) +
            ($dataExport[5] != null ? intval($dataExport[5]) : 0 ) +
            ($dataExport[6] != null ? intval($dataExport[6]) : 0 ) +
            ($dataExport[7] != null ? intval($dataExport[7]) : 0 ) ;

            $dataPost = (object) array(
                'stt'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'code'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'chisotk'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'dh'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'thS'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'ts'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'pgs'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'gs'    =>  $dataExport[7] != null ? $dataExport[7] : "",
                'tongso'    =>  $tongso,
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
