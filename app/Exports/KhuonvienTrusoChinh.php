<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class KhuonvienTrusoChinh implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $khuonvientsc = [];

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
            'italic'    => true
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
        $this->khuonvientsc = DB::table("khuonvientsc")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $outputArr = [];
        $Sumdientichdat = 0;
        $Sumdientichqd = 0;
        foreach($this->khuonvientsc as $key => $value){
            $khuonvien_chiso = DB::table("khuonvien_chiso")->where("id", $value->khuonvien)->first();

            $Sumdientichdat = $Sumdientichdat + intval($value->dientichdat);
            $Sumdientichqd = $Sumdientichqd + (intval($value->dientichdat) * intval($value->vitrikhuonvien));
            $row = [
                $key + 1,
                $khuonvien_chiso->khuonvien,
                $khuonvien_chiso->kyhieu,
                $value->hinhthucsd,
                $value->dientichdat,
                $value->vitrikhuonvien,
                intval($value->dientichdat) * intval($value->vitrikhuonvien),
                $value->diachi
            ];
            array_push($outputArr, $row);
        }
        $row = [
            "",
            "Tổng số(S)",
            "",
            "",
            $Sumdientichdat,
            "",
            $Sumdientichqd,
            ""
        ];
        array_push($outputArr, $row);
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Khuôn viên",
            "Ký hiệu",
            "Hình thức sử dụng",
            "Diện tích đất(m2)",
            "Vị trí khuôn viên(Kvt)",
            "Diện tích quy đổi(m2)",
            "Địa chỉ"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Khuôn viên TSC và PH");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:H1")
                    ->getActiveSheet()
                    ->getRowDimension('1')
                    ->setRowHeight(35);

                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(25);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(25);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(25);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(25);

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
                $event->sheet->getDelegate()->getStyle("H1")
                    ->applyFromArray($this->styleHeader);

                // Cell
                foreach($this->khuonvientsc as $key => $value){
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
                    $event->sheet->getDelegate()
                        ->getStyle('H'. $key + 2)
                        ->applyFromArray($this->styleCell);
                }

                $event->sheet->getDelegate()
                    ->getStyle('A'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('B'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('C'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('D'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('E'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('F'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('G'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
                $event->sheet->getDelegate()
                    ->getStyle('H'. count($this->khuonvientsc) + 2)
                    ->applyFromArray($this->styleCellNotEdit);
            },
        ];
    }

}
