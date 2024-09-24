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


class TuyensinhDaotaoExport implements WithMultipleSheets
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
        $sheets = [
            new TuyensinhExport($this->nam),
            new TungkhoahocExport($this->nam),
        ];
        return $sheets;
    }
}