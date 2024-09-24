<?php

namespace App\Exports;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class TonghopAdd implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $dataTong;

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
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'ededed'],
        ],
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
            'bold' => true
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

    public function __construct( $nam, $dataTong)
    {
        $this->nam = $nam;
        $this->dataTong = $dataTong;
    }
    public function collection()
    {
        $outputArr = [];
        $tieuchuan1 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "1")->first();
        foreach($this->dataTong[0] as $key => $value){
            $row = [
                $tieuchuan1->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        $tieuchuan2 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "2")->first();
        foreach($this->dataTong[1] as $key => $value){
            $row = [
                $tieuchuan2->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        $tieuchuan3 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "3")->first();
        foreach($this->dataTong[2] as $key => $value){
            $row = [
                $tieuchuan3->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        $tieuchuan4 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "4")->first();
        foreach($this->dataTong[3] as $key => $value){
            $row = [
                $tieuchuan4->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        $tieuchuan5 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "5")->first();
        foreach($this->dataTong[4] as $key => $value){
            $row = [
                $tieuchuan5->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        $tieuchuan6 = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "6")->first();
        foreach($this->dataTong[5] as $key => $value){
            $row = [
                $tieuchuan6->mo_ta,
                $value[0],
                $value[1],
                $value[2],
                $value[3],
                $value[4]
            ];
            array_push($outputArr, $row);
        }

        return collect($outputArr);
    }


    public function headings() :array {
        return [
            "Tiêu chuẩn",
            "Tiêu chí",
            "Chỉ số",
            "Yêu cầu",
            "Thực tế",
            "Kết quả"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Báo cáo tổng hợp");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);

                $event->sheet->getDelegate()->getStyle("A2:A5")
                    ->getActiveSheet()
                    ->mergeCells('A2:A5');
                $event->sheet->getDelegate()->getStyle("A6:A8")
                    ->getActiveSheet()
                    ->mergeCells('A6:A8');
                $event->sheet->getDelegate()->getStyle("A9:A18")
                    ->getActiveSheet()
                    ->mergeCells('A9:A18');
                $event->sheet->getDelegate()->getStyle("A19:A20")
                    ->getActiveSheet()
                    ->mergeCells('A19:A20');
                $event->sheet->getDelegate()->getStyle("A21:A33")
                    ->getActiveSheet()
                    ->mergeCells('A21:A33');
                $event->sheet->getDelegate()->getStyle("A34:A37")
                    ->getActiveSheet()
                    ->mergeCells('A34:A37');

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(40);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(15);

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
                // Cell
                for($key = 0; $key <= 35; $key ++){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
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
                }
            },
        ];
    }
}
