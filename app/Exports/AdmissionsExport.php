<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

use Illuminate\Support\Facades\DB;


class AdmissionsExport implements FromCollection, WithHeadings, WithEvents
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
        ],
        'font'  => [
            'size'  => 14,
            'name'  => 'Times New Roman',
            'bold' => true,
        ],
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'd9e1f2'],
        ]
    ];
    public $validateData = array();

    public function __construct($id, $nam)
    {
        $this->id = $id;
        $this->nam = $nam;
    }
    public function collection()
    {
        $outputArr = [];
        foreach($this->id as $key => $value){
            $chiso = DB::table('chiso_ketquacshdc')->where("id", $value)->first();

            $showChitieucl = '';
            if($chiso->loaics == 'Đạt Không đạt'){
                $showChitieucl = $chiso->loaics;
            }else if($chiso->loaics == 'Phần trăm'){
                switch ($chiso->loaiss) {
                    case 'Lớn hơn':
                        $showChitieucl = ">" . $chiso->chitieucl . '%';
                        break;
                    case 'Lớn hơn hoặc bằng':
                        $showChitieucl = ">=" . $chiso->chitieucl . '%';
                        break;
                    case 'Bằng':
                        $showChitieucl = $chiso->chitieucl . '%';
                        break;
                    case 'Nhỏ hơn':
                        $showChitieucl = "<" . $chiso->chitieucl . '%';
                        break;
                    case 'Nhỏ hơn hoặc bằng':
                        $showChitieucl = "<=" . $chiso->chitieucl . '%';
                        break;
                }
            }else if($chiso->loaics == 'Số'){
                switch ($chiso->loaiss) {
                    case 'Lớn hơn':
                        $showChitieucl = ">" . $chiso->chitieucl;
                        break;
                    case 'Lớn hơn hoặc bằng':
                        $showChitieucl = ">=" . $chiso->chitieucl;
                        break;
                    case 'Bằng':
                        $showChitieucl = $chiso->chitieucl;
                        break;
                    case 'Nhỏ hơn':
                        $showChitieucl = "<" . $chiso->chitieucl;
                        break;
                    case 'Nhỏ hơn hoặc bằng':
                        $showChitieucl = "<=" . $chiso->chitieucl;
                        break;
                }
            }
            $row = [
                $key + 1 ,
                $chiso->code,
                $chiso->chisochinh,
                $showChitieucl,
                "",
                ""
            ];
            if($chiso->loaics == 'Đạt Không đạt'){
                array_push($this->validateData, count($outputArr) + 2);
            }
            array_push($outputArr, $row);
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Mã chỉ số",
            "Chỉ số chính",
            "Chỉ tiêu chiến lược",
            "Kết quả đạt được năm " . $this->nam,
            "Ghi chú"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Kết quả thực hiện " . $this->nam);
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setVisible(false);
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(50);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(40);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(30);

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
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 2)
                        ->applyFromArray($this->styleCell);
                }

                // Validation data
                foreach($this->validateData as $validateD){
                    $validation = $event->sheet->getDataValidation('E'. $validateD);
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setErrorTitle('Input error');
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setPrompt('Please pick a value from the drop-down list.');
                    $validation->setFormula1('"Đạt,Không đạt"');
                }
            },
        ];
    }

    // public function view(): View
    // {
    //     $data = DB::table('chiso_ketquacshdc')->whereIn("id", $this->id)->get();
    //     return view('admin/Exports/export1', [
    //         'data' => $data,
    //         'nam' => $this->nam
    //     ]);
    // }
}
