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


class ExportTaiChinhSL implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $id;
    public $chiSoCha = array();
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
    public $styleCellBold = [
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
            'bold' => true,
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
            $chisoCha = DB::table('chisotk_tc4thuchi')->where("id", $value)
                ->where("parent", null)
                ->first();
            if($chisoCha ){
                // Con 1
                $conC1 = DB::table('chisotk_tc4thuchi')->where("parent", $chisoCha->id)->orderBy('stt', 'asc');
                $row = [
                    $chisoCha->stt ,
                    $chisoCha->code,
                    $chisoCha->chisotk,
                    "",
                    "",
                ];
                array_push($outputArr, $row);
                $indexParent = count($outputArr) - 1;
                $con1Sum = array();

                foreach($conC1->get() as $con1){
                    if (in_array($con1->id, $this->id)) {
                        $conC2 = DB::table('chisotk_tc4thuchi')->where("parent", $con1->id)->orderBy('stt', 'asc');
                        $commonElements = array_intersect($this->id, $conC2->pluck('id')->toArray());
                        $row1 = [
                            $con1->stt ,
                            $con1->code,
                            $con1->chisotk,
                            count($commonElements) == 0 ? "" : "=SUM(D".count($outputArr)  + 3 .":D" . (count($outputArr) + 3 + count($commonElements) - 1) . ")",
                            "",
                        ];
                        array_push($outputArr, $row1);
                        array_push($con1Sum, count($outputArr) - 1 + 2);

                        // Con 2

                        foreach($conC2->get() as $con2){
                            if (in_array($con2->id, $this->id)) {
                                $row2 = [
                                    $con2->stt ,
                                    $con2->code,
                                    $con2->chisotk,
                                    "",
                                    "",
                                ];
                                array_push($outputArr, $row2);
                            }
                        }
                    }
                }
                if(count($con1Sum) != 0){
                    $congthuc = "";
                    foreach($con1Sum as $keySum => $sum){
                        if($keySum != count($con1Sum) - 1){
                            $congthuc =$congthuc . "D" . $sum . ',';
                        }else{
                            $congthuc =$congthuc . "D" . $sum;

                        }
                    }
                    $outputArr[$indexParent][3] = "=SUM(" . $congthuc . ")" ;
                }
            }
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Mã chỉ số",
            "Chỉ số thống kê",
            $this->nam,
            "Ghi chú",
        ];
    }

    public function registerEvents(): array
    {

        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Tình hình thu chi " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:E1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(40);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);

                $event->sheet->getColumnDimension('B')->setVisible(false);
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

                    $chisoCha = DB::table('chisotk_tc4thuchi')->where("id", $value)
                        ->where("parent", null)
                        ->first();
                    if($chisoCha){

                        array_push($this->chiSoCha, $chisoCha->id);
                        $event->sheet->getDelegate()
                            ->getStyle('A'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('B'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('C'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('D'. $key + 2)
                            ->applyFromArray($this->styleCellBold);
                        $event->sheet->getDelegate()
                            ->getStyle('E'. $key + 2)
                            ->applyFromArray($this->styleCellBold);
                    }

                    $chisoCon1 = DB::table('chisotk_tc4thuchi')->where("id", $value)->whereIn("parent", $this->chiSoCha)
                        ->first();
                    if($chisoCon1){
                        $event->sheet->getDelegate()
                            ->getStyle('A'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('B'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('C'. $key + 2)
                            ->applyFromArray($this->styleCellNotEditBold);
                        $event->sheet->getDelegate()
                            ->getStyle('D'. $key + 2)
                            ->applyFromArray($this->styleCellBold);
                        $event->sheet->getDelegate()
                            ->getStyle('E'. $key + 2)
                            ->applyFromArray($this->styleCellBold);
                    }

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