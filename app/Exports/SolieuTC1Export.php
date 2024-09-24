<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

use Illuminate\Support\Facades\DB;


class SolieuTC1Export implements WithMultipleSheets
// class AdmissionsExport implements FromView
{
    use Exportable;
    public $nam;
    public function __construct($nam)
    {
        $this->nam = $nam;
    }
    public function sheets(): array
    {
        $sheet2 = new DanhsachlanhdaoExport($this->nam);
        $sheet3 = new TinhtranghoanthienExport($this->nam);
        $sheet4 = new KetquahoatdongExport($this->nam);
        $sheet5 = new LapBCDGExport($this->nam);

        $sheet1 = new TonghopTC1($this->nam);
        $sheets = [
            $sheet1,
            $sheet2,
            $sheet3,
            $sheet4,
            $sheet5,
        ];
        return $sheets;
    }
}