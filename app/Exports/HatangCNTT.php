<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class HatangCNTT implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $hatangcntt = [];

    public $styleCell = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'wrapText' => true
        ],
        'font'  => [
            'name'  => 'Times New Roman',
            'size'  => 10,
        ],
    ];
    public $styleCellNotEdit = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'wrapText' => true
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'ededed'],
        ],
        'font'  => [
            'name'  => 'Times New Roman',
            'size'  => 10,
        ],
    ];

    public $styleHeader = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '284a74'],
            ],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'font'  => [
            'size'  => 12,
            'name'  => 'Times New Roman',
            'bold' => true
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'd9e1f2'],
        ]
    ];

    public function __construct( $nam)
    {
        $this->nam = $nam;
    }
    public function collection()
    {
        $this->hatangcntt = DB::table("hatangcntt")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $outputArr = [];

        foreach($this->hatangcntt as $key => $value){
            $hatangcntt_chiso = DB::table('hatangcntt_chiso')->where("id", $value->chisotk)->first();
            $row = [
                $key + 1,
                $hatangcntt_chiso ? $hatangcntt_chiso->chisotk : "",
                $value->giatri,
                $value->ghichu,
            ];
            array_push($outputArr, $row);
        }

        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Chỉ số thống kê",
            "Giá trị",
            "Ghi chú"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Hạng tầng CNTT");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:D1")
                    ->getActiveSheet()
                    ->getRowDimension('1')
                    ->setRowHeight(35);

                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);

                $event->sheet->getDelegate()->getStyle("A1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("B1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("C1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("D1")
                    ->applyFromArray($this->styleHeader);

                // Cell
                foreach($this->hatangcntt as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $key + 2)
                        ->applyFromArray($this->styleCell);
                }

            },
        ];
    }

}