<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class TaichinhImport implements WithMultipleSheets
{
    public $tongthuhd = null;
    public $tongchihd = null;
    public function sheets(): array
    {   
        
        $this->tongthuhd = new Tongthuhoatdong();
        $this->tongchihd = new Tongchihoatdong();

        return [
            'Tổng thu hoạt động' => $this->tongthuhd,
            'Tổng chi hoạt động' => $this->tongchihd,
        ];
    }
    public function read() {
        $data = [];
        array_push($data, $this->tongthuhd->read(), $this->tongchihd->read());
        return $data;
    }
}