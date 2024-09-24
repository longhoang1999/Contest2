<?php

namespace App\Exports\ExportToImport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\DB;


class ExportCauhoiKS implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $id;
    public $nam;
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

    public function __construct($id, $nam)
    {
        $this->id = $id;
        $this->nam = $nam;
    }
    public function collection()
    {
        $outputArr = [];
        $row = [
            "", "", "",
            "Đại học",
            "Sau đại học",
            "Đại học",
            "Sau đại học",
            "Đại học",
            "Sau đại học",
            "Đại học",
            "Sau đại học",
            "Đại học",
            "Sau đại học"
        ];
        array_push($outputArr, $row);


        foreach($this->id as $key => $value){
            $chiso = DB::table('khaosatnh_chiso')->where("id", $value)->first();
            $row = [
                $key + 1 ,
                $chiso->code,
                $chiso->cauhoiks,
                "", "", "", "", "", "",
                "=IF(D". (count($outputArr) + 2) ."<>0,F". (count($outputArr) + 2) ."/D".(count($outputArr) + 2).",0)",
                "=IF(E". (count($outputArr) + 2) ."<>0,G". (count($outputArr) + 2) ."/E".(count($outputArr) + 2).",0)",
                "=IF(F". (count($outputArr) + 2) ."<>0,H". (count($outputArr) + 2) ."/F".(count($outputArr) + 2).",0)",
                "=IF(G". (count($outputArr) + 2) ."<>0,I". (count($outputArr) + 2) ."/G".(count($outputArr) + 2).",0)",
            ];
            array_push($outputArr, $row);
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Mã câu hỏi",
            "Câu hỏi khảo sát ý kiến",
            "Số lượt khảo sát", "",
            "Số lượt phản hồi", "",
            "Phản hồi tích cực", "",
            "Tỉ lệ phản hồi", "",
            "Tỉ lệ phản hồi tích cực", ""
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Kết quản KS người học " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:M1")->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("A2:M3")->getActiveSheet()->getRowDimension('2')->setRowHeight(35);
                // Merge
                $event->sheet->getDelegate()->getStyle("A1:A2")->getActiveSheet()->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")->getActiveSheet()->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("C1:C2")->getActiveSheet()->mergeCells('C1:C2');
                $event->sheet->getDelegate()->getStyle("D1:E1")->getActiveSheet()->mergeCells('D1:E1');
                $event->sheet->getDelegate()->getStyle("F1:G1")->getActiveSheet()->mergeCells('F1:G1');
                $event->sheet->getDelegate()->getStyle("H1:I1")->getActiveSheet()->mergeCells('H1:I1');
                $event->sheet->getDelegate()->getStyle("J1:K1")->getActiveSheet()->mergeCells('J1:K1');
                $event->sheet->getDelegate()->getStyle("L1:M1")->getActiveSheet()->mergeCells('L1:M1');



                // Hide column code
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setVisible(false);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("I")->getActiveSheet()->getColumnDimension('I')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("J")->getActiveSheet()->getColumnDimension('J')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("K")->getActiveSheet()->getColumnDimension('K')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("L")->getActiveSheet()->getColumnDimension('L')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("M")->getActiveSheet()->getColumnDimension('M')->setWidth(20);



                // $event->sheet->getDelegate()->getStyle("A1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("B1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("C1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("D1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("E1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("F1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("G1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("H1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("I1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("J1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("K1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("L1")
                //     ->applyFromArray($this->styleHeader);
                // $event->sheet->getDelegate()->getStyle("M1")
                //     ->applyFromArray($this->styleHeader);

                $startColumn = 'A';
                $endColumn = 'M';
                for ($column = $startColumn; $column <= $endColumn; $column++) {
                    $event->sheet->getDelegate()->getStyle($column.'1')
                                    ->applyFromArray($this->styleHeader);
                     $event->sheet->getDelegate()->getStyle($column.'2')
                                    ->applyFromArray($this->styleHeader);
                }
                // Cell

                foreach($this->id as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
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
                    $event->sheet->getDelegate()
                        ->getStyle('H'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('I'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('J'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('K'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('L'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('M'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()->getStyle('J'. $key + 3 . ':M'. $key + 3)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                }

                // $startCell = 'E1';
                // $endCell = 'E'. count($this->id) + 1;
                // $cellRange = $startCell . ':' . $endCell;

                // $validation = $event->sheet->getDataValidation($cellRange);
                // $validation->setType(DataValidation::TYPE_LIST);
                // $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                // $validation->setAllowBlank(false);
                // $validation->setShowInputMessage(true);
                // $validation->setShowErrorMessage(true);
                // $validation->setShowDropDown(true);
                // $validation->setErrorTitle('Input error');
                // $validation->setError('Value is not in list.');
                // $validation->setPromptTitle('Pick from list');
                // $validation->setPrompt('Please pick a value from the drop-down list.');
                // $validation->setFormula1('"Sở hữu,Liên kết,Thuê lâu năm"');
            },
        ];
    }

}