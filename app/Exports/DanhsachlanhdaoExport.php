<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class DanhsachlanhdaoExport implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $lanhdaocc = [];

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
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
            'wrapText' => true
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'ededed'],
        ],
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
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
            'size'  => 14,
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
        $this->lanhdaocc = DB::table("lanhdaocc")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $outputArr = [];

        $row = [
            "", "", "", "",
            "Nơi ban hành",
            "Ngày có hiệu lực",
            ""
        ];
        array_push($outputArr, $row);

        foreach($this->lanhdaocc as $key => $value){
            $row = [
                $key + 1,
                $value->hovaten,
                $value->chuctrach,
                $value->thoihancv,
                $value->noibanhanh,
                $value->cohieuluc,
                $value->duongdan,
            ];
            array_push($outputArr, $row);
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Họ và tên",
            "Chức trách",
            "Thời hạn giữ chức vụ đến",
            "Văn bản quyết định",
            "",
            "Đường dẫn trang web"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Danh sách lãnh đạo chủ chốt");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:G1")
                    ->getActiveSheet()
                    ->mergeCells('E1:F1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("A2:G2")
                    ->getActiveSheet()
                    ->getRowDimension('2')
                    ->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle("A1:A2")
                    ->getActiveSheet()
                    ->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")
                    ->getActiveSheet()
                    ->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("C1:C2")
                    ->getActiveSheet()
                    ->mergeCells('C1:C2');
                $event->sheet->getDelegate()->getStyle("D1:D2")
                    ->getActiveSheet()
                    ->mergeCells('D1:D2');
                $event->sheet->getDelegate()->getStyle("G1:G2")
                    ->getActiveSheet()
                    ->mergeCells('G1:G2');

                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);
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
                $event->sheet->getDelegate()->getStyle("A2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("B2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("C2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("D2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("E2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("F2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("G2")
                    ->applyFromArray($this->styleHeader);
                // Cell
                foreach($this->lanhdaocc as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('G'. $key + 3)
                        ->applyFromArray($this->styleCell);
                }
            },
        ];
    }

}