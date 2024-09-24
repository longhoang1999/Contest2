<?php
namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class TuyensinhParentImport implements WithMultipleSheets
{
    public $thongkequymo = null;
    public $thongketungkhoa = null;
    public function sheets(): array
    {   
        
        $this->thongkequymo = new ThongkeQuymoDT();
        $this->thongketungkhoa = new ThongkeTungkhoa();

        return [
            'Thống kê quy mô đào tạo' => $this->thongkequymo,
            'Thống kê từng khóa' => $this->thongketungkhoa,
        ];
    }
    public function read() {
        $data = [];
        array_push($data, $this->thongkequymo->read(), $this->thongketungkhoa->read());
        return $data;
    }
}