<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Illuminate\Support\Facades\DB;


class Thuchihoatdongnam implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $boldParent = array();
    public $boldParent2 = array();
    public $lastRow = array();

    public $chisotk_tc4thuchi = [];
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
            'bold' => true,
        ],
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
        'fill' => [
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor'    => ['argb' => 'ededed'],
        ],
        'font'  => [
            'size'  => 10,
            'name'  => 'Times New Roman',
            'italic' => true,
            'bold' => true,
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
        $outputArr = [];
        $tongThuHDNam1 = 0;
        $chenhlechThuchiNam1 = 0;
        $tongThuHDNam2 = 0;
        $chenhlechThuchiNam2 = 0;
        $tongThuHDNam3 = 0;
        $chenhlechThuchiNam3 = 0;
        $tongThuHDNam4 = 0;
        $chenhlechThuchiNam4 = 0;

        $this->chisotk_tc4thuchi = DB::table("chisotk_tc4thuchi")->get();
        foreach( $this->chisotk_tc4thuchi as $value){
            if( $value->parent  == null){

                $thuchihd__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam)->first();
                $thuchihd2__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam - 1)->first();
                $thuchihd3__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam - 2)->first();
                $thuchihd4__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam - 3)->first();
                $row = [
                    $value->stt,
                    $value->chisotk,
                    $thuchihd__ ? $thuchihd__->giatri : "",
                    $thuchihd2__ ? $thuchihd2__->giatri: "",
                    $thuchihd3__ ? $thuchihd3__->giatri : "",
                    $thuchihd4__ ? $thuchihd4__->giatri : "",
                    $thuchihd__ ? $thuchihd__->ghichu : "",
                ];
                array_push($outputArr, $row);
                array_push($this->boldParent, count($outputArr) + 1);

                if( $value->parent  == null && $value->code == "A"){
                    $tongThuHDNam1 = $thuchihd__ ? $thuchihd__->giatri : 0;
                    $tongThuHDNam2 = $thuchihd2__ ? $thuchihd2__->giatri : 0;
                    $tongThuHDNam3 = $thuchihd3__ ? $thuchihd3__->giatri : 0;
                    $tongThuHDNam4 = $thuchihd4__ ? $thuchihd4__->giatri : 0;

                }else if( $value->parent  == null && $value->code == "C"){
                    $chenhlechThuchiNam1 =  $thuchihd__ ? $thuchihd__->giatri : 0;
                    $chenhlechThuchiNam2 =  $thuchihd2__ ? $thuchihd2__->giatri : 0;
                    $chenhlechThuchiNam3 =  $thuchihd3__ ? $thuchihd3__->giatri : 0;
                    $chenhlechThuchiNam4 =  $thuchihd4__ ? $thuchihd4__->giatri : 0;
                }



                foreach( $this->chisotk_tc4thuchi as $value2){
                    if($value2->parent == $value->id){
                        $thuchihd = DB::table("thuchihd")->where("chisotk",$value2->id)->where("nam", $this->nam)->first();
                        $thuchihd2 = DB::table("thuchihd")->where("chisotk",$value2->id)->where("nam", $this->nam - 1)->first();
                        $thuchihd3 = DB::table("thuchihd")->where("chisotk",$value2->id)->where("nam", $this->nam - 2)->first();
                        $thuchihd4 = DB::table("thuchihd")->where("chisotk",$value2->id)->where("nam", $this->nam - 3)->first();

                        $row = [
                            $value2->stt,
                            $value2->chisotk,
                            $thuchihd ? $thuchihd->giatri : "",
                            $thuchihd2 ? $thuchihd2->giatri: "",
                            $thuchihd3 ? $thuchihd3->giatri : "",
                            $thuchihd4 ? $thuchihd4->giatri : "",
                            $thuchihd ? $thuchihd->ghichu : "",
                        ];
                        array_push($outputArr, $row);
                        array_push($this->boldParent2, count($outputArr) + 1);


                        foreach( $this->chisotk_tc4thuchi as $value3){
                            if($value3->parent == $value2->id){
                                $thuchihd_ = DB::table("thuchihd")->where("chisotk",$value3->id)->where("nam", $this->nam)->first();
                                $thuchihd2_ = DB::table("thuchihd")->where("chisotk",$value3->id)->where("nam", $this->nam - 1)->first();
                                $thuchihd3_ = DB::table("thuchihd")->where("chisotk",$value3->id)->where("nam", $this->nam - 2)->first();
                                $thuchihd4_ = DB::table("thuchihd")->where("chisotk",$value3->id)->where("nam", $this->nam - 3)->first();
                                $row = [
                                    $value3->stt,
                                    $value3->chisotk,
                                    $thuchihd_ ? $thuchihd_->giatri : "",
                                    $thuchihd2_ ? $thuchihd2_->giatri: "",
                                    $thuchihd3_ ? $thuchihd3_->giatri : "",
                                    $thuchihd4_ ? $thuchihd4_->giatri : "",
                                    $thuchihd_ ? $thuchihd_->ghichu : "",
                                ];
                                array_push($outputArr, $row);

                            }
                        }
                    }
                }


                // Tổng nguồn thu học phí và hỗ trợ chi thường xuyên
                if($value->code == "A"){
                    // Năm 1
                    $nhanuocNam1 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam)
                        ->where("chisotk_tc4thuchi.code", "bdU0HYzbE2")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $lephiNghocNam1 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam)
                        ->where("chisotk_tc4thuchi.code", "E29q8kGtzG")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $resultNam1 = ($lephiNghocNam1 ? $lephiNghocNam1->giatri : 0) + ($nhanuocNam1 ? $nhanuocNam1->giatri : 0);
                    // Năm 2
                    $nhanuocNam2 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 1)
                        ->where("chisotk_tc4thuchi.code", "bdU0HYzbE2")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $lephiNghocNam2 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 1)
                        ->where("chisotk_tc4thuchi.code", "E29q8kGtzG")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $resultNam2 = ($lephiNghocNam2 ? $lephiNghocNam2->giatri : 0) + ($nhanuocNam2 ? $nhanuocNam2->giatri : 0);
                    // Năm 3
                    $nhanuocNam3 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 2)
                        ->where("chisotk_tc4thuchi.code", "bdU0HYzbE2")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $lephiNghocNam3 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 2)
                        ->where("chisotk_tc4thuchi.code", "E29q8kGtzG")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $resultNam3 = ($lephiNghocNam3 ? $lephiNghocNam3->giatri : 0) + ($nhanuocNam3 ? $nhanuocNam3->giatri : 0);
                    // Năm 4
                    $nhanuocNam4 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 3)
                        ->where("chisotk_tc4thuchi.code", "bdU0HYzbE2")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $lephiNghocNam4 = DB::table("thuchihd")
                        ->where("thuchihd.nam", $this->nam - 3)
                        ->where("chisotk_tc4thuchi.code", "E29q8kGtzG")
                        ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                        ->first();
                    $resultNam4 = ($lephiNghocNam4 ? $lephiNghocNam4->giatri : 0) + ($nhanuocNam4 ? $nhanuocNam4->giatri : 0);
                    $row = [
                        "",
                        "Tổng nguồn thu học phí và hỗ trợ chi thường xuyên",
                        $resultNam1,
                        $resultNam2,
                        $resultNam3,
                        $resultNam4,
                        "",
                    ];
                    array_push($outputArr, $row);
                    array_push($this->lastRow,  count($outputArr) + 1);
                }
            }
        }


        // Chênh lệch thu chi/Tổng thu
        $row = [
            "",
            "Chênh lệch thu chi/Tổng thu",
            $tongThuHDNam1 != 0 ? $chenhlechThuchiNam1 / $tongThuHDNam1 : "0",
            $tongThuHDNam2 != 0 ? $chenhlechThuchiNam2 / $tongThuHDNam2 : "0",
            $tongThuHDNam3 != 0 ? $chenhlechThuchiNam3 / $tongThuHDNam3 : "0",
            $tongThuHDNam4 != 0 ? $chenhlechThuchiNam4 / $tongThuHDNam4 : "0",
            ""
        ];
        array_push($outputArr, $row);


        array_push($this->lastRow,  count($outputArr) + 1);
        return collect($outputArr);
    }
    public function headings() :array {

        return [
            "STT",
            "Chỉ số thống kê",
            $this->nam,
            $this->nam - 1,
            $this->nam - 2,
            $this->nam - 3,
            "Ghi chú"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Tình hình thu chi hoạt động năm");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:G1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(60);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(70);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(30);

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

                // Cell
                foreach($this->chisotk_tc4thuchi as $key => $value){
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
                        ->getStyle('G' . $key + 2)
                        ->applyFromArray($this->styleCell);
                }
                foreach($this->boldParent as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('G' . $value)
                        ->applyFromArray($this->styleCellNotEdit);
                }
                foreach($this->boldParent2 as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $value)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('G' . $value)
                        ->applyFromArray($this->styleCellNotEdit);
                }
                for($key = 0; $key < count($this->lastRow); $key ++){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('C'.  $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('E'.  $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()
                        ->getStyle('G' .  $this->lastRow[$key])
                        ->applyFromArray($this->styleCellNotEditItalic);
                    $event->sheet->getDelegate()->getStyle('C'. $this->lastRow[count($this->lastRow) - 1] . ':F'. $this->lastRow[count($this->lastRow) - 1])->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
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