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
use Illuminate\Support\Facades\DB;


class ExportCongboKH implements FromCollection, WithHeadings, WithEvents
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
        foreach($this->id as $key => $value){
            $chiso = DB::table('congbokhgv_chiso')->where("id", $value)->first();
            $row = [
                $key + 1 ,
                $chiso->code,
                $chiso->chisotk,
                "",
                "",
                ""
            ];
            array_push($outputArr, $row);
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Mã chỉ số",
            "Chỉ số thống kê",
            "Số lượng",
            "Hệ số",
            "Ghi chú"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Công trình PV đào tạo " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);
                // Hide column code
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setVisible(false);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);


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

                foreach($this->id as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
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