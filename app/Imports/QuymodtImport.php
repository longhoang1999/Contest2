<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


class QuymodtImport implements ToModel,WithHeadingRow, WithCalculatedFormulas
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
                'stt'       => $dataExport[0] != null ? $dataExport[0] : "",
                'code'   =>  $dataExport[1] != null ? $dataExport[1] : "",
                'linhvuc'   =>  $dataExport[2] != null ? $dataExport[2] : "",
                'cq'        => $dataExport[3] != null ? $dataExport[3] : "",
                'vlvh'      =>  $dataExport[4] != null ? $dataExport[4] : "",
                'dttx'      => $dataExport[5] != null ? $dataExport[5] : "",
                'ths'       =>  $dataExport[6] != null ? $dataExport[6] : "",
                'ts'        =>  $dataExport[7] != null ? $dataExport[7] : "",
                'tong'      =>  $dataExport[8] != null ? $dataExport[8] : "",
                'kgd'       =>  $dataExport[9] != null ? $dataExport[9] : "",
                'soluonggd'   =>  $dataExport[10] != null ? round($dataExport[10], 4) : "",
                'kdt'       =>  $dataExport[11] != null ? $dataExport[11] : "",
                'soluongdt'   =>  $dataExport[12] != null ? round($dataExport[12], 4) : "",
                'ktc'   =>  $dataExport[13] != null ? $dataExport[13] : "",
                'kbb'   =>  $dataExport[14] != null ? $dataExport[14] : "",
                'klv'   =>  $dataExport[15] != null ? round($dataExport[15], 4) : "",
            );
            array_push($this->data, $dataPost);
        }
    }
    public function read() {
        return $this->data;
    }
}