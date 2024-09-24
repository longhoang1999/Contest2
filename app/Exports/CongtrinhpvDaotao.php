<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class CongtrinhpvDaotao implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $congtrinhdt = [];
    public $cellNotEditArr = [];

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
            'size'  => 10,
            'name'  => 'Times New Roman',
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
            'size'  => 10,
            'name'  => 'Times New Roman',
            'bold'  => true,
            'italic'    =>true
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
        $this->congtrinhdt = DB::table("congtrinhdt")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $outputArr = [];

        $tongSsan = 0;
        $tongSsanDt = 0;
        foreach($this->congtrinhdt as $key => $value){
            $congtrinhdt_chiso = DB::table("congtrinhdt_chiso")->where("id", $value->congtrinh)->first();
            //
            $tongSsan = $tongSsan + floatval($value->tongSsanxd);
            $tongSsanDt = $tongSsanDt + ( floatval($value->tongSsanxd) * floatval($value->hesoSsd));
            $row = [
                $key + 1,
                $congtrinhdt_chiso ? $congtrinhdt_chiso->congtrinh : "",
                $congtrinhdt_chiso ? $congtrinhdt_chiso->kyhieu : "",
                $value->tongSsanxd,
                $value->hesoSsd,
                round(floatval($value->tongSsanxd) * floatval($value->hesoSsd), 3),
                $value->diachi
            ];
            array_push($outputArr, $row);
        }
        $row = [
            "",
            "Tổng sô",
            "",
            $tongSsan,
            "",
            $tongSsanDt,
            ""
        ];
        array_push($outputArr, $row);
        array_push($this->cellNotEditArr, count($outputArr) + 1);


        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Công trình",
            "Ký hiệu",
            "Tổng diện tích sàn xây dựng",
            "Hệ số diện tích sử dụng cho đào tạo (Ksd)",
            "Diện tích sàn sử dụng cho đào tạo (m2)",
            "Địa chỉ"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Công trình PV đào tạo");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:G1")
                    ->getActiveSheet()
                    ->getRowDimension('1')
                    ->setRowHeight(45);

                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(35);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(35);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(35);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(30);

                $event->sheet->getDelegate()->getStyle("A1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("B1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("C1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("D1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("E1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("F1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("G1")
                    ->applyFromArray($this->styleHeader);
                // Cell
                foreach($this->congtrinhdt as $key => $value){
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
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('G'. $key + 2)
                        ->applyFromArray($this->styleCell);
                }

                foreach($this->cellNotEditArr as $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('G'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                }

            },
        ];
    }

}