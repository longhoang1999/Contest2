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


class SolieuTHExport implements WithMultipleSheets
// class AdmissionsExport implements FromView
{
    use Exportable;
    public $nam;
    public $dataTong;

    public function __construct($nam, $dataTong)
    {
        $this->nam = $nam;
        $this->dataTong = $dataTong;
    }
    public function sheets(): array
    {
        $sheets = [
            new TonghopAdd($this->nam, $this->dataTong),
            new TonghopTC1($this->nam),
            new DanhsachlanhdaoExport($this->nam),
            new TinhtranghoanthienExport($this->nam),
            new KetquahoatdongExport($this->nam),
            new LapBCDGExport($this->nam),
            new TonghopTC2($this->nam),
            new DoinguGVToanTimeExport($this->nam),
            new TonghopTC3($this->nam),
            new KhuonvienTrusoChinh($this->nam),
            new CongtrinhpvDaotao($this->nam),
            new GiaotrinhSach($this->nam),
            new HatangCNTT($this->nam),
            new TonghopTC4($this->nam),
            new Thuchihoatdongnam($this->nam),
            new TonghopTC5($this->nam),
            new KetquadaotaoTS($this->nam),
            new QuymoDTLV($this->nam),
            new TonghopTC6($this->nam),
            new CongboKhoahoc($this->nam),
            new KhaosatNguoihoc($this->nam),
        ];
        return $sheets;
    }
}
