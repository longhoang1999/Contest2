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


class KetquadaotaoTS implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $countDTTS = 0;
    public $rowBold = array();
    public $rowItalic = array();

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
        ]
    ];
    public $styleCellNotEditItalic = [
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
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'ededed'],
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
    public $styleCellItalicRight = [
        'borders' => [
            'outline' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['argb' => '000000'],
            ],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
            'vertical'  => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
            'italic' => true,
        ],
    ];


    public function __construct( $nam)
    {
        $this->nam = $nam;
    }
    public function collection()
    {
        // Bảng 5A: Kết quả đào tạo và tuyển sinh (cả ĐH và SĐH)
        $bang5A = [];
        $daotao_tuyensinh = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam);

        array_push($bang5A, [
            'A', 'Thống kê quy mô đào tạo, tuyển sinh của 10 năm',
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
        ]);
        // Tổng số sinh viên có mặt cuối năm
        $tong_svcmcn = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('tong_svcmcn')
        ->get();
        if(count($tong_svcmcn) != 0){
            $newArr = [
                '1', 'Tổng số sinh viên có mặt cuối năm',
            ];
            foreach($tong_svcmcn as $value){
                array_push($newArr, $value->tong_svcmcn);
            }
            array_push($bang5A, $newArr);
        }

        // Chỉ tiêu tuyển sinh theo kế hoạch từng năm
        $chitieu_ts = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('chitieu_ts')
        ->get();
        if(count($chitieu_ts) != 0){
            $newArr = [
                '2', 'Chỉ tiêu tuyển sinh theo kế hoạch từng năm',
            ];
            foreach($chitieu_ts as $value){
                array_push($newArr, $value->chitieu_ts);
            }
            array_push($bang5A, $newArr);
        }

        // Số nhập học mới của từng năm
        $sonh_moi = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sonh_moi')
        ->get();
        if(count($sonh_moi) != 0){
            $newArr = [
                '3', 'Số nhập học mới của từng năm',
            ];
            foreach($sonh_moi as $value){
                array_push($newArr, $value->sonh_moi);
            }
            array_push($bang5A, $newArr);
        }

        // Tỉ lệ nhập học = Số nhập học/chỉ tiêu
        $tile_nh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('tile_nh')
        ->get();
        if(count($tile_nh) != 0){
            $newArr = [
                '', 'Tỉ lệ nhập học = Số nhập học/chỉ tiêu',
            ];
            foreach($tile_nh as $value){
                array_push($newArr, $value->tile_nh);
            }
            array_push($bang5A, $newArr);
        }
        array_push($bang5A, [
            'A', 'Thống kê tình trạng từng KHÓA (K) theo năm nhập học',
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
        ]);
        // Số hiện tại đang theo học tại cơ sở đào tạo
        $soht_dh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('soht_dh')
        ->get();
        if(count($soht_dh) != 0){
            $newArr = [
                '5', 'Số hiện tại đang theo học tại cơ sở đào tạo',
            ];
            foreach($soht_dh as $value){
                array_push($newArr, $value->soht_dh);
            }
            array_push($bang5A, $newArr);
        }

        // Số tốt nghiệp trong năm qua, đúng hạn
        $sotn_dh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotn_dh')
        ->get();
        if(count($sotn_dh) != 0){
            $newArr = [
                '6', 'Số tốt nghiệp trong năm qua, đúng hạn',
            ];
            foreach($sotn_dh as $value){
                array_push($newArr, $value->sotn_dh);
            }
            array_push($bang5A, $newArr);
        }

        // Số tốt nghiệp trong năm qua, quá hạn ≤ 0,5 thời gian tiêu chuẩn
        $sotn_qh05 = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotn_qh05')
        ->get();
        if(count($sotn_qh05) != 0){
            $newArr = [
                '7', 'Số tốt nghiệp trong năm qua, quá hạn ≤ 0,5 thời gian tiêu chuẩn',
            ];
            foreach($sotn_qh05 as $value){
                array_push($newArr, $value->sotn_qh05);
            }
            array_push($bang5A, $newArr);
        }

        // Số tốt nghiệp trong năm qua, quá hạn 1,5 thời gian tiêu chuẩn
        $sotn_qh15 = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotn_qh15')
        ->get();
        if(count($sotn_qh15) != 0){
            $newArr = [
                '8', 'Số tốt nghiệp trong năm qua, quá hạn 1,5 thời gian tiêu chuẩn',
            ];
            foreach($sotn_qh15 as $value){
                array_push($newArr, $value->sotn_qh15);
            }
            array_push($bang5A, $newArr);
        }

        // Số tốt nghiệp đúng hạn/số nhập học
        $sotndh_nh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotndh_nh')
        ->get();
        if(count($sotndh_nh) != 0){
            $newArr = [
                '', 'Số tốt nghiệp đúng hạn/số nhập học',
            ];
            foreach($sotndh_nh as $value){
                array_push($newArr, $value->sotndh_nh);
            }
            array_push($bang5A, $newArr);
        }

        //  Số tốt nghiệp quá hạn ≤ 0,5 thời gian tiêu chuẩn/số nhập học
        $sotn_qh05_nh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotn_qh05_nh')
        ->get();
        if(count($sotn_qh05_nh) != 0){
            $newArr = [
                '', 'Số tốt nghiệp quá hạn ≤ 0,5 thời gian tiêu chuẩn/số nhập học',
            ];
            foreach($sotn_qh05_nh as $value){
                array_push($newArr, $value->sotn_qh05_nh);
            }
            array_push($bang5A, $newArr);
        }

        //  Số tốt nghiệp quá hạn  1,5 thời gian tiêu chuẩn/số nhập học
        $sotn_qh15_nh = $daotao_tuyensinh->orderBy('nam_solieu', 'desc')
        ->select('sotn_qh15_nh')
        ->get();
        if(count($sotn_qh15_nh) != 0){
            $newArr = [
                '', 'Số tốt nghiệp quá hạn  1,5 thời gian tiêu chuẩn/số nhập học',
            ];
            foreach($sotn_qh15_nh as $value){
                array_push($newArr, $value->sotn_qh15_nh);
            }
            array_push($bang5A, $newArr);
        }
        return collect($bang5A);
    }
    // public function caculateData($year, $type) {
    //     if($type == 1){
    //         $daotao_tuyensinh = DB::table("daotao_tuyensinh")->where("nam", $year)->where("chisotk_namnh", "");
    //         if($daotao_tuyensinh->count() != 0){
    //             return $daotao_tuyensinh->get();
    //         }else{
    //             return [];
    //         }
    //     }else if($type == 2){
    //         $daotao_tuyensinh = DB::table("daotao_tuyensinh")->where("nam", $year)->where("chisotk_quymo", "");
    //         if($daotao_tuyensinh->count() != 0){
    //             return $daotao_tuyensinh->get();
    //         }else{
    //             return [];
    //         }
    //     }
    // }
    public function headings() :array {

        return [
            "STT",
            "Chỉ số thống kê",
            "", "","", "", "", "","","", "",""
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Kết quả đào tạo, tuyển sinh");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:L1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(30);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(60);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("I")->getActiveSheet()->getColumnDimension('I')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("J")->getActiveSheet()->getColumnDimension('J')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("K")->getActiveSheet()->getColumnDimension('K')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("L")->getActiveSheet()->getColumnDimension('L')->setWidth(10);

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
                $event->sheet->getDelegate()->getStyle("I1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("J1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("K1")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("L1")
                    ->applyFromArray($this->styleHeader);
                // Cell
                for($key = 0; $key <= $this->countDTTS + 2; $key++){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 2)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('G' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('H' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('I' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('J' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('K' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('L' . $key + 2)
                        ->applyFromArray($this->styleCellItalic);
                }


                foreach ($this->rowBold as $value) {
                    for ($i = 'A'; $i <= 'L'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $value)
                            ->applyFromArray($this->styleCellBold);
                    }
                }

                foreach ($this->rowItalic as $value) {
                    for ($i = 'A'; $i <= 'L'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $value)
                            ->applyFromArray($this->styleCellNotEditItalic);
                    }
                }
                $event->sheet->getDelegate()->getStyle("A6:L6")->applyFromArray($this->styleCellItalicRight);
                $event->sheet->getDelegate()->getStyle("A12:L12")->applyFromArray($this->styleCellItalicRight);
                $event->sheet->getDelegate()->getStyle("A13:L13")->applyFromArray($this->styleCellItalicRight);
                $event->sheet->getDelegate()->getStyle("A14:L14")->applyFromArray($this->styleCellItalicRight);

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
