<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;

use Illuminate\Support\Facades\DB;


class KhaosatNguoihoc implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $khaosatnh = [];

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
        $this->khaosatnh = DB::table("khaosatnh")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $outputArr = [];

        foreach($this->khaosatnh as $key => $value){
            $khaosatnh_chiso = DB::table('khaosatnh_chiso')->where("id", $value->cauhoiks)->first();
            $row = [
                $value->stt,
                $khaosatnh_chiso->cauhoiks,
                "Đại học",
                $value->daihoc_luotks,
                $value->daihoc_luotph,
                $value->daihoc_phtc,
                $value->daihoc_tlph. "%",
                $value->daihoc_tlphtc ."%",
            ];
            array_push($outputArr, $row);

            $row = [
                $value->stt,
                $khaosatnh_chiso->cauhoiks,
                "Sau đại học",
                $value->saudaihoc_luotks,
                $value->saudaihoc_luotph,
                $value->saudaihoc_phtc,
                $value->saudaihoc_tlph . "%",
                $value->saudaihoc_tlphtc . "%",
            ];
            array_push($outputArr, $row);

            $row = [
                $value->stt,
                $khaosatnh_chiso->cauhoiks,
                "Tổng số",
                intval($value->daihoc_luotks) + intval($value->saudaihoc_luotks),
                intval($value->daihoc_luotph) + intval($value->saudaihoc_luotph),
                intval($value->daihoc_phtc) + intval($value->saudaihoc_phtc),
                round(
                    ((intval($value->daihoc_luotph) + intval($value->saudaihoc_luotph)) /
                    ( intval($value->daihoc_luotks) + intval($value->saudaihoc_luotks))) * 100,
                2) . "%",

                round(
                    ( (intval($value->daihoc_phtc) + intval($value->saudaihoc_phtc)) /
                    (intval($value->daihoc_luotph) + intval($value->saudaihoc_luotph))) * 100,
                2) . "%"
            ];
            array_push($outputArr, $row);
        }

        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Câu hỏi khảo sát ý kiến",
            "Người học",
            "Số lượt khảo sát",
            "Số lượt phản hồi",
            "Phản hồi tích cực",
            "Tỉ lệ phản hồi",
            "Tỉ lệ phản hồi tích cực"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Kết quả khảo sát");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    ->getRowDimension('1')
                    ->setRowHeight(35);


                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(40);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(30);

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

                for($key = 2; $key <= (count($this->khaosatnh) * 3) + 1 ; $key = $key + 3){
                    $event->sheet->getDelegate()->getStyle("A" . $key . ":A" . $key + 2)
                        ->getActiveSheet()
                        ->mergeCells("A" . $key . ":A" . $key + 2);
                    $event->sheet->getDelegate()->getStyle("B" . $key . ":B" . $key + 2)
                        ->getActiveSheet()
                        ->mergeCells("B" . $key . ":B" . $key + 2);
                }

                for($key = 0; $key < count($this->khaosatnh) * 3 ; $key++){
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
            },
        ];
    }

}
