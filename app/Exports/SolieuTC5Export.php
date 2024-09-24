<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class SolieuTC5Export implements WithMultipleSheets
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
            new TonghopTC5($this->nam),
            new KetquadaotaoTS($this->nam),
            new QuymoDTLV($this->nam),
        ];
        return $sheets;
    }
}
