<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class QuymoDTTS implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $quymodt_chiso = [];
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
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'FFFF00'],
        ],
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
            'bold' => true,
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
    public $styleCellNotEditItalicRight = [
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
            'italic' => true
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
    public $styleHeaderSmall = [
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
            'size'  => 10,
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
        $outputArr = [];
        // Thống kê quy mô đào tạo, tuyển sinh của 10 năm(1)
        $row = [
            "", "Thống kê quy mô đào tạo, tuyển sinh của 10 năm",
            $this->nam,
            $this->nam - 1,
            $this->nam - 2,
            $this->nam - 3,
            $this->nam - 4,
            $this->nam - 5,
            $this->nam - 6,
            $this->nam - 7,
            $this->nam - 8,
            $this->nam - 9,
        ];
        array_push($outputArr, $row);

        $titleA = array([
            'stt'   => '1',
            'val' => 'Tổng số sinh viên có mặt cuối năm'
        ],[
            'stt'   => '2',
            'val' => 'Chỉ tiêu tuyển sinh theo kế hoạch từng năm'
        ], [
            'stt'   => '3',
            'val' => 'Số nhập học mới của từng năm'
        ]);
        foreach($titleA as $value){
            $row = [
                $value['stt'],
                $value['val'], "", "", "", "", "", "", "", "", "", ""
            ];
            array_push($outputArr, $row);
        }
        array_push($outputArr, [
            "",
            "Tỉ lệ nhập học = Số nhập học/chỉ tiêu",
            "=IF(C4<>0,C5/C4,0)",
            "=IF(D4<>0,D5/D4,0)",
            "=IF(E4<>0,E5/E4,0)",
            "=IF(F4<>0,F5/F4,0)",
            "=IF(G4<>0,G5/G4,0)",
            "=IF(H4<>0,H5/H4,0)",
            "=IF(I4<>0,I5/I4,0)",
            "=IF(J4<>0,J5/J4,0)",
            "=IF(K4<>0,K5/K4,0)",
            "=IF(L4<>0,L5/L4,0)",
        ]);
        // Thống kê tình trạng từng KHÓA (K) theo năm nhập học (2)
        $row = [
            "", "Thống kê tình trạng từng KHÓA (K) theo năm nhập học",
            $this->nam,
            $this->nam - 1,
            $this->nam - 2,
            $this->nam - 3,
            $this->nam - 4,
            $this->nam - 5,
            $this->nam - 6,
            $this->nam - 7,
            $this->nam - 8,
            $this->nam - 9,
        ];
        array_push($outputArr, $row);
        $titleB = array([
            'stt'   => '5',
            'val' => 'Số hiện tại đang theo học tại cơ sở đào tạo'
        ],[
            'stt'   => '6',
            'val' => 'Số tốt nghiệp trong năm qua, đúng hạn'
        ], [
            'stt'   => '7',
            'val' => 'Số tốt nghiệp trong năm qua, quá hạn ≤ 0,5 thời gian tiêu chuẩn'
        ],[
            'stt'   => '8',
            'val' => 'Số tốt nghiệp trong năm qua, quá hạn 1,5 thời gian tiêu chuẩn '
        ]);
        foreach($titleB as $value){
            $row = [
                $value['stt'],
                $value['val'], "", "", "", "", "", "", "", "", "", ""
            ];
            array_push($outputArr, $row);
        }
        // Tính
        array_push($outputArr, [
            "",
            "Số tốt nghiệp đúng hạn/số nhập học",
            "=IF(C5<>0,C9/C5,0)",
            "=IF(D5<>0,D9/D5,0)",
            "=IF(E5<>0,E9/E5,0)",
            "=IF(F5<>0,F9/F5,0)",
            "=IF(G5<>0,G9/G5,0)",
            "=IF(H5<>0,H9/H5,0)",
            "=IF(I5<>0,I9/I5,0)",
            "=IF(J5<>0,J9/J5,0)",
            "=IF(K5<>0,K9/K5,0)",
            "=IF(L5<>0,L9/L5,0)",
        ], [
            "",
            "Số tốt nghiệp quá hạn ≤ 0,5 thời gian tiêu chuẩn/số nhập học",
            "=IF(C4<>0,C10/C4,0)",
            "=IF(D4<>0,D10/D4,0)",
            "=IF(E4<>0,E10/E4,0)",
            "=IF(F4<>0,F10/F4,0)",
            "=IF(G4<>0,G10/G4,0)",
            "=IF(H4<>0,H10/H4,0)",
            "=IF(I4<>0,I10/I4,0)",
            "=IF(J4<>0,J10/J4,0)",
            "=IF(K4<>0,K10/K4,0)",
            "=IF(L4<>0,L10/L4,0)",
        ], [
            "",
            "Số tốt nghiệp quá hạn  1,5 thời gian tiêu chuẩn/số nhập học",
            "=IF(C5<>0,C11/C5,0)",
            "=IF(D5<>0,D11/D5,0)",
            "=IF(E5<>0,E11/E5,0)",
            "=IF(F5<>0,F11/F5,0)",
            "=IF(G5<>0,G11/G5,0)",
            "=IF(H5<>0,H11/H5,0)",
            "=IF(I5<>0,I11/I5,0)",
            "=IF(J5<>0,J11/J5,0)",
            "=IF(K5<>0,K11/K5,0)",
            "=IF(L5<>0,L11/L5,0)",
        ]);
        return collect($outputArr);
    }
    public function headings() :array {

        return [
            "STT", "CHỈ SỐ THỐNG KÊ", "", "", "", "", "", "", "", "", "", ""
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("KQ đào tạo - tuyển sinh");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:L1")->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
                // Định dạng phần trăm
                $event->sheet->getDelegate()->getStyle('C6:I6')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $event->sheet->getDelegate()->getStyle('C12:L12')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $event->sheet->getDelegate()->getStyle('C13:L13')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);
                $event->sheet->getDelegate()->getStyle('C14:L14')->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE_00);


                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(60);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("I")->getActiveSheet()->getColumnDimension('I')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("J")->getActiveSheet()->getColumnDimension('J')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("K")->getActiveSheet()->getColumnDimension('K')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("L")->getActiveSheet()->getColumnDimension('L')->setWidth(15);


                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()->getStyle($i . '1')
                        ->applyFromArray($this->styleHeader);
                }


                // Cell
                for($key = 0; $key < 14; $key ++){
                    for ($i = 'A'; $i <= 'B'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $key + 1)
                            ->applyFromArray($this->styleCellNotEdit);
                    }
                    for ($i = 'C'; $i <= 'L'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $key + 1)
                            ->applyFromArray($this->styleCell);
                    }

                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 2)
                        ->applyFromArray($this->styleCellBold);
                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 7)
                        ->applyFromArray($this->styleCellBold);
                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 6)
                        ->applyFromArray($this->styleCellNotEditItalicRight);
                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 12)
                        ->applyFromArray($this->styleCellNotEditItalicRight);
                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 13)
                        ->applyFromArray($this->styleCellNotEditItalicRight);
                }
                for ($i = 'A'; $i <= 'L'; $i++) {
                    $event->sheet->getDelegate()
                        ->getStyle($i . 14)
                        ->applyFromArray($this->styleCellNotEditItalicRight);
                }

            },
        ];
    }
}