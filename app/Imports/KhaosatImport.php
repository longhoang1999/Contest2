<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


class KhaosatImport implements ToModel,WithHeadingRow, WithCalculatedFormulas
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
                'cauhoiks'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'daihoc_luotks'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'saudaihoc_luotks'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'daihoc_luotph'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'saudaihoc_luotph'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'daihoc_phtc'    =>  $dataExport[7] != null ? $dataExport[7] : "",
                'saudaihoc_phtc'    =>  $dataExport[8] != null ? $dataExport[8] : "",

                'daihoc_tlph'    =>  round(($dataExport[3] != null ? (($dataExport[5] != null ? intval($dataExport[5]) : 0) / ($dataExport[3] != null ? intval($dataExport[3]) : 0)) : 0) * 100, 2),
                'saudaihoc_tlph'    =>   round(($dataExport[4] != null ? (($dataExport[6] != null ? intval($dataExport[6]) : 0) / ($dataExport[4] != null ? intval($dataExport[4]) : 0)) : 0) * 100,2),
                'daihoc_tlphtc'    =>   round(($dataExport[5] != null ? (($dataExport[7] != null ? intval($dataExport[7]) : 0) / ($dataExport[5] != null ? intval($dataExport[5]) : 0)) : 0 ) * 100, 2),
                'saudaihoc_tlphtc'    =>  round(($dataExport[6] != null ? (($dataExport[8] != null ? intval($dataExport[8]) : 0) / ($dataExport[6] != null ? intval($dataExport[6]) : 0)) : 0) * 100, 2),

            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
