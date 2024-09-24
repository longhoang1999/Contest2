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


class ExportDaotaoTuyensinh implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $id;
    public $nam;
    public $formatPercent = array();
    public $headerIndex = array();
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
            'size'  => 10,
            'name'  => 'Times New Roman',
            'bold' => true,
        ],
    ];
    public $styleCellItalic = [
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
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
            'italic' => true,
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
        // Tỉ lệ nhập học = Số nhập học/chỉ tiêu
        $start  = 0; $end = 0;
        // Số tốt nghiệp đúng hạn/số nhập học
        $start1  = 0; $end1 = 0;
        //  Số tốt nghiệp quá hạn ≤ 0,5 thời gian tiêu chuẩn/số nhập học
        $start2  = 0; $end2 = 0;
        //  Số tốt nghiệp quá hạn  1,5 thời gian tiêu chuẩn/số nhập học
        $start3  = 0; $end3 = 0;

        foreach($this->id as $key => $value){
            $chisoCha = DB::table('daotao_ts_chiso')->where("id", $value)
                ->where("parent", null)
                ->where("code", "A")
                ->first();
            if($chisoCha ){
                $row = [
                    $chisoCha->stt ,
                    $chisoCha->code,
                    $chisoCha->chisotk,
                    $this->nam,
                ];
                array_push($outputArr, $row);
                array_push($this->headerIndex, count($outputArr) + 1);

                $conC1 = DB::table('daotao_ts_chiso')->where("parent", $chisoCha->id);
                foreach($conC1->get() as $con1){
                    if (in_array($con1->id, $this->id)) {
                        $row1 = [
                            $con1->stt ,
                            $con1->code,
                            $con1->chisotk,
                            "",
                        ];
                        array_push($outputArr, $row1);

                        // Tỉ lệ nhập học = Số nhập học/chỉ tiêu
                        if($con1->id == "4"){
                            $start = count($outputArr) + 1;
                            $start2 = count($outputArr) + 1;
                        }
                        if($con1->id == "5"){
                            $end = count($outputArr) + 1;
                            $start1 = count($outputArr) + 1;
                            $start3 = count($outputArr) + 1;
                        }
                    }
                }
                // Các thông số tính %
                if($start != 0 && $end != 0){
                    $row2 = [
                        "" , "tlnh",
                        "Tỉ lệ nhập học = Số nhập học/chỉ tiêu",
                        "=IF(D".$start."<>0,D".$end."/D".$start.",0)",
                    ];
                    array_push($outputArr, $row2);
                    array_push($this->formatPercent, count($outputArr) + 1);
                }
            }
        }

        foreach($this->id as $key => $value){
            $chisoCha = DB::table('daotao_ts_chiso')->where("id", $value)
                ->where("parent", null)
                ->where("code", "B")
                ->first();
            if($chisoCha ){
                $row = [
                    $chisoCha->stt ,
                    $chisoCha->code,
                    $chisoCha->chisotk,
                    $this->nam,
                ];
                array_push($outputArr, $row);
                array_push($this->headerIndex, count($outputArr) + 1);

                $conC1 = DB::table('daotao_ts_chiso')->where("parent", $chisoCha->id);
                foreach($conC1->get() as $con1){
                    if (in_array($con1->id, $this->id)) {
                        $row1 = [
                            $con1->stt ,
                            $con1->code,
                            $con1->chisotk,
                            "",
                        ];
                        array_push($outputArr, $row1);

                        // Số tốt nghiệp đúng hạn/số nhập học
                        if($con1->id == "7"){
                            $end1 = count($outputArr) + 1;
                        }
                        if($con1->id == "8"){
                            $end2 = count($outputArr) + 1;
                        }
                        if($con1->id == "10"){
                            $end3 = count($outputArr) + 1;
                        }
                    }
                }
                // Các thông số tính %
                if($start1 != 0 && $end1 != 0){
                    $row2 = [
                        "" , "stndh",
                        "Số tốt nghiệp đúng hạn/số nhập học",
                        "=IF(D".$start1."<>0,D".$end1."/D".$start1.",0)",
                    ];
                    array_push($outputArr, $row2);
                    array_push($this->formatPercent, count($outputArr) + 1);
                }
                if($start2 != 0 && $end2 != 0){
                    $row3 = [
                        "" , "stnqh",
                        "Số tốt nghiệp quá hạn ≤ 0,5 thời gian tiêu chuẩn/số nhập học",
                        "=IF(D".$start2."<>0,D".$end2."/D".$start2.",0)",
                    ];
                    array_push($outputArr, $row3);
                    array_push($this->formatPercent, count($outputArr) + 1);
                }
                if($start3 != 0 && $end3 != 0){
                    $row4 = [
                        "" , "stnqh2",
                        "Số tốt nghiệp quá hạn  1,5 thời gian tiêu chuẩn/số nhập học",
                        "=IF(D".$start3."<>0,D".$end3."/D".$start3.",0)",
                    ];
                    array_push($outputArr, $row4);
                    array_push($this->formatPercent, count($outputArr) + 1);
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
            "Năm",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("KQ đào tạo tuyển sinh " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:D1")->getActiveSheet()->getRowDimension('1')->setRowHeight(35);




                // Hide column code
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setVisible(false);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(100);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(15);





                $startColumn = 'A';
                $endColumn = 'D';
                for ($column = $startColumn; $column <= $endColumn; $column++) {
                    $event->sheet->getDelegate()->getStyle($column.'1')
                                    ->applyFromArray($this->styleHeader);
                }
                // Cell

                for($key = 0; $key < count($this->id) + 4; $key ++){
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
                }
                foreach($this->headerIndex as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $value)
                        ->applyFromArray($this->styleCellNotEditBold);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $value)
                        ->applyFromArray($this->styleCellNotEditBold);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellNotEditBold);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $value)
                        ->applyFromArray($this->styleCellBold);


                }

                foreach($this->formatPercent as $key => $value){
                    $event->sheet->getDelegate()->getStyle('D'. $value)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellItalic);
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