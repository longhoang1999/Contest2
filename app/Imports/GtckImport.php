<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class GtckImport implements ToModel,WithHeadingRow, WithCalculatedFormulas
{
    public $data = [];
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;

        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }
        //dd($row);

        if($dataExport[0] != null){
            $dataPost = (object) array(
                'stt'   =>  $dataExport[0] != null ? $dataExport[0] : "",
                'code'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'chisotk'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'giatri'    =>  $dataExport[3] != null ? $dataExport[3] : "",
                'noiluutru'    =>  $dataExport[4] != null ? $dataExport[4] : "",
                'ghichu'    =>  $dataExport[5] != null ? $dataExport[5] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
