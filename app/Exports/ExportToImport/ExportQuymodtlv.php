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


class ExportQuymodtlv implements FromCollection, WithHeadings, WithEvents
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
    public $styleCellNotEditBold = [
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
            'bold' => true,
            'italic'=> true,
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
        // Header 2
        $row = [
            "", "", "",
            "CQ", "VLVH", "ĐTTX",
            "ThS", "TS", "",
            "KGD", "Số lượng",
            "KDT", "Số lượng",
            "KTC", "KBB", "KLV"
        ];
        array_push($outputArr, $row);


        foreach($this->id as $key => $value){
            $chiso = DB::table('quymodt_chiso')->where("id", $value)->first();
            $row = [
                $key + 1 ,
                $chiso->code,
                $chiso->linhvuc,
                "",
                "",
                "",
                "",
                "",
                "=SUM(D" . count($outputArr) + 2 . ":H". count($outputArr) + 2 .")",
                "",
                "=(D". count($outputArr) + 2 ."+E". count($outputArr) + 2 ."*0.8+F". count($outputArr) + 2 ."*0.5+G". count($outputArr) + 2 ."*1.5+H". count($outputArr) + 2 ."*2)*J" . count($outputArr) + 2,
                "",
                "=(D". count($outputArr) + 2 ."+G". count($outputArr) + 2 ."*1.5+H". count($outputArr) + 2 ."*2)*L" . count($outputArr) + 2,
                "",
                "",
                ""
            ];
            array_push($outputArr, $row);
        }

        foreach($outputArr as $key => $value){
            if($key > 0){
                $outputArr[$key][15] = '=IF(I'. count($outputArr) + 2 .'<>0,I'. $key + 2 .'/I'. count($outputArr) + 2 .'*N'. $key + 2 .',0)';
            }
        }
      //  dd($outputArr);

        $row = [
            "","",
            "Tổng số",
            "=SUM(D3:D" . count($outputArr) + 1 .")",
            "=SUM(E3:E" . count($outputArr) + 1 .")",
            "=SUM(F3:F" . count($outputArr) + 1 .")",
            "=SUM(G3:G" . count($outputArr) + 1 .")",
            "=SUM(H3:H" . count($outputArr) + 1 .")",
            "=SUM(I3:I" . count($outputArr) + 1 .")",
            "",
            "=SUM(K3:K" . count($outputArr) + 1 .")",
            "",
            "=SUM(M3:M" . count($outputArr) + 1 .")",
            "",
            "",
            "=SUM(P3:P" . count($outputArr) + 1 .")",
        ];
        array_push($outputArr, $row);



        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Mã lĩnh vực",
            "Lĩnh vực đào tạo",
            "Quy mô ĐH", "", "",
            "Quy mô SĐH", "",
            "Tổng",
            "Quy đổi về giảng dạy", "",
            "Quy đổi về diện tích", "",
            "Hệ số kinh phí",
            "Hệ số công bố",
            "Hệ số quy đổi"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Quy mô đào tạo " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:P1")->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("A2:P2")->getActiveSheet()->getRowDimension('2')->setRowHeight(35);


                // Hide column code
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setVisible(false);
                // Merge
                $event->sheet->getDelegate()->getStyle("A1:A2")->getActiveSheet()->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")->getActiveSheet()->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("C1:C2")->getActiveSheet()->mergeCells('C1:C2');
                $event->sheet->getDelegate()->getStyle("D1:F1")->getActiveSheet()->mergeCells('D1:F1');
                $event->sheet->getDelegate()->getStyle("G1:H1")->getActiveSheet()->mergeCells('G1:H1');
                $event->sheet->getDelegate()->getStyle("I1:I2")->getActiveSheet()->mergeCells('I1:I2');
                $event->sheet->getDelegate()->getStyle("J1:K1")->getActiveSheet()->mergeCells('J1:K1');
                $event->sheet->getDelegate()->getStyle("L1:M1")->getActiveSheet()->mergeCells('L1:M1');




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
                $event->sheet->getDelegate()->getStyle("N")->getActiveSheet()->getColumnDimension('N')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("O")->getActiveSheet()->getColumnDimension('O')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("P")->getActiveSheet()->getColumnDimension('P')->setWidth(20);





                $columns = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];
                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getStyle($column . '1')
                                    ->applyFromArray($this->styleHeader);
                    $event->sheet->getDelegate()->getStyle($column . '2')
                                    ->applyFromArray($this->styleHeader);
                }

                // Cell
                for($key = 0; $key <= count($this->id); $key ++){
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
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('J'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('K'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('L'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('M'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('N'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('O'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('P'. $key + 3)
                        ->applyFromArray($this->styleCellNotEdit);

                }

                $event->sheet->getDelegate()
                    ->getStyle('A'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('B'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('C'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('D'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('E'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('F'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('G'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('H'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('I'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('J'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('K'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('L'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('M'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('N'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('O'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);
                $event->sheet->getDelegate()
                    ->getStyle('P'. count($this->id) + 3)
                    ->applyFromArray($this->styleCellNotEditBold);


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