<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CtdtImport implements ToModel,WithHeadingRow
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
                'congtrinh'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'kyhieu'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'mucdichsd'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'tongSsanxd'    =>  $dataExport[5] != null ? $dataExport[5] : "",
                'hesoSsd'    =>  $dataExport[6] != null ? $dataExport[6] : "",
                'dientichsddt'    =>  round(($dataExport[5] != null ? floatval($dataExport[5]) : 0) * ($dataExport[6] != null ? floatval($dataExport[6]) : 0), 2),
                'dientichlvgv'  => $dataExport[7] != null ? $dataExport[7] : "",
                'diachi'    =>  $dataExport[8] != null ? $dataExport[8] : "",

            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
