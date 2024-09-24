<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


class KetquadttsImport implements ToModel,WithHeadingRow, WithCalculatedFormulas
{
    public $data = [];
    public function model(array $row)
    {
        $dataExport = array();
        $i = 0;
        foreach ($row as $key => $value) {
            $dataExport[$i++] = $value;
        }

        if($dataExport[0] != null || $dataExport[1] != null || $dataExport[2] != null){
            $dataPost = (object) array(
                'stt'       => $dataExport[0] !== null ? $dataExport[0] : "",
                'chiso'       => $dataExport[1] !== null ? $dataExport[1] : "",
                'nam'       => $dataExport[2] !== null ? $dataExport[2] : "",
                'nam1'       => $dataExport[3] !== null ? $dataExport[3] : "",
                'nam2'       => $dataExport[4] !== null ? $dataExport[4] : "",
                'nam3'       => $dataExport[5] !== null ? $dataExport[5] : "",
                'nam4'       => $dataExport[6] !== null ? $dataExport[6] : "",
                'nam5'       => $dataExport[7] !== null ? $dataExport[7] : "",
                'nam6'       => $dataExport[8] !== null ? $dataExport[8] : "",
                'nam7'       => $dataExport[9] !== null ? $dataExport[9] : "",
                'nam8'       => $dataExport[10] !== null ? $dataExport[10] : "",
                'nam9'       => $dataExport[11] !== null ? $dataExport[11] : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}
