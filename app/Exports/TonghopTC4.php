<?php

namespace App\Exports;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use Illuminate\Support\Facades\DB;


class TonghopTC4 implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $tieuchi = [];
    public $countRow = 0;

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
        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "4")->first();
        $this->tieuchi = DB::table('tieuchi')->where("tieuchuan_id", $tieuchuan->id)->orderBy("stt", "asc")->get();
        foreach($this->tieuchi as $tchi){
            $tchiCon =  DB::table('tieuchi_con')->where("id_tieuchi", $tchi->id)->orderBy("stt", "asc")->get();
            $tchi->tchiCon = $tchiCon;
        }

        $outputArr = [];
        foreach($this->tieuchi as $key => $value){
            $mocchuan = DB::table("mocchuan")->where("idTieuchi", $value->id)->first();//
            // Tính toán
            $kqTieuchi = $this->kqTChi($key + 1, $mocchuan);
            $row = [
                $tieuchuan->stt .".". $value->stt,
                $value->mo_ta,
                $mocchuan ? ($mocchuan->donvitinh == "percent" ? $mocchuan->chisomc . "%" : $mocchuan->chisomc) : "",
                $kqTieuchi[0],
                $kqTieuchi[1],
                ""
            ];
            array_push($outputArr, $row);
            $this->countRow ++;


            foreach($value->tchiCon as $keyCon => $tchiCon){
                $mocchuan2 = DB::table("mocchuan")->where("idTieuchiCon", $tchiCon->id)->first();
                $kqTieuchiCon = $this->kqTChiCon($key + 1, $keyCon + 1, $mocchuan2);
                $row = [
                    $tieuchuan->stt .".". $value->stt . "." . $tchiCon->stt,
                    $tchiCon->mo_ta,
                    $mocchuan2 ? ($mocchuan2->donvitinh == "percent" ? $mocchuan2->chisomc . "%" : $mocchuan2->chisomc) : "",
                    $kqTieuchiCon[0],
                    $kqTieuchiCon[1],
                    ""
                ];
                array_push($outputArr, $row);
                $this->countRow ++;

            }
        }
        return collect($outputArr);
    }
    // Tính toán
    public function kqTChi($stt, $mocchuan){
        switch($stt){
            case 1: {
                $tongThuHDNam1 = 0;
                $chenhlechThuchiNam1 = 0;
                $tongThuHDNam2 = 0;
                $chenhlechThuchiNam2 = 0;
                $tongThuHDNam3 = 0;
                $chenhlechThuchiNam3 = 0;

                $chisotk_tc4thuchi = DB::table("chisotk_tc4thuchi")->get();
                foreach( $chisotk_tc4thuchi as $value){
                    if( $value->parent  == null){
                        $thuchihd__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam)->first();
                        $thuchihd2__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam - 1)->first();
                        $thuchihd3__ = DB::table("thuchihd")->where("chisotk",$value->id)->where("nam", $this->nam - 2)->first();

                        if( $value->code == "A"){
                            $tongThuHDNam1 = $thuchihd__ ? $thuchihd__->giatri : 0;
                            $tongThuHDNam2 = $thuchihd2__ ? $thuchihd2__->giatri : 0;
                            $tongThuHDNam3 = $thuchihd3__ ? $thuchihd3__->giatri : 0;

                        }else if($value->code == "C") {
                            $chenhlechThuchiNam1 =  $thuchihd__ ? $thuchihd__->giatri : 0;
                            $chenhlechThuchiNam2 =  $thuchihd2__ ? $thuchihd2__->giatri : 0;
                            $chenhlechThuchiNam3 =  $thuchihd3__ ? $thuchihd3__->giatri : 0;

                        }
                    }
                }
                $result =(($tongThuHDNam1 != 0 ? $chenhlechThuchiNam1 / $tongThuHDNam1 : 0) +
                            ( $tongThuHDNam2 != 0 ? $chenhlechThuchiNam2 / $tongThuHDNam2 : 0) +
                            ( $tongThuHDNam3 != 0 ? $chenhlechThuchiNam3 / $tongThuHDNam3 : 0) ) / 3 ;

                if($tongThuHDNam1 != 0 && $tongThuHDNam2 != 0 && $tongThuHDNam3 != 0){
                    if($result * 100 > 0 && $result * 100 < $mocchuan->chisomc){
                        return [round($result * 100, 2) . ' %', 'Đạt'];
                    }else{
                        return [round($result * 100, 2) . ' %', 'Không đạt'];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            case 2: {
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
                $thuchihd1 =  DB::table("thuchihd")->where("thuchihd.nam", $this->nam)
                    ->where("chisotk_tc4thuchi.code", "A")
                    ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                    ->first();
                $resultNam1 = ($lephiNghocNam1 ? $lephiNghocNam1->giatri : 0) + ($nhanuocNam1 ? $nhanuocNam1->giatri : 0);
                $tongThuHDNam1 = $thuchihd1 ? $thuchihd1->giatri : 0;
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
                $thuchihd2 =  DB::table("thuchihd")->where("thuchihd.nam", $this->nam-1)
                    ->where("chisotk_tc4thuchi.code", "A")
                    ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                    ->first();
                $resultNam2 = ($lephiNghocNam2 ? $lephiNghocNam2->giatri : 0) + ($nhanuocNam2 ? $nhanuocNam2->giatri : 0);
                $tongThuHDNam2 = $thuchihd2 ? $thuchihd2->giatri : 0;
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
                $thuchihd3 =  DB::table("thuchihd")->where("thuchihd.nam", $this->nam-2)
                    ->where("chisotk_tc4thuchi.code", "A")
                    ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                    ->first();
                $resultNam3 = ($lephiNghocNam3 ? $lephiNghocNam3->giatri : 0) + ($nhanuocNam3 ? $nhanuocNam3->giatri : 0);
                $tongThuHDNam3 = $thuchihd3 ? $thuchihd3->giatri : 0;

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
                $thuchihd4 =  DB::table("thuchihd")->where("thuchihd.nam", $this->nam-3)
                    ->where("chisotk_tc4thuchi.code", "A")
                    ->leftJoin('chisotk_tc4thuchi', 'thuchihd.chisotk', '=', 'chisotk_tc4thuchi.id')
                    ->first();
                $resultNam4 = ($lephiNghocNam4 ? $lephiNghocNam4->giatri : 0) + ($nhanuocNam4 ? $nhanuocNam4->giatri : 0);
                $tongThuHDNam4 = $thuchihd4 ? $thuchihd4->giatri : 0;

                $lastResult = ((
                    ($tongThuHDNam4 != 0 ? $tongThuHDNam3 / $tongThuHDNam4 : 0) +
                    ($tongThuHDNam3 != 0 ? $tongThuHDNam2 / $tongThuHDNam3 : 0) +
                    ($tongThuHDNam2 != 0 ? $tongThuHDNam1 / $tongThuHDNam2 : 0) +
                    (($tongThuHDNam4 - $resultNam4) != 0 ? ($tongThuHDNam3 - $resultNam3) / ($tongThuHDNam4 - $resultNam4) : 0) +
                    (($tongThuHDNam3 - $resultNam3) != 0 ? ($tongThuHDNam2 - $resultNam2) / ($tongThuHDNam3 - $resultNam3) : 0 ) +
                    (($tongThuHDNam2 - $resultNam2) != 0 ? ($tongThuHDNam1 - $resultNam1) / ($tongThuHDNam2 - $resultNam2) : 0
                )) / 6) - 1;

                if($thuchihd1 && $thuchihd2 && $thuchihd3 && $thuchihd4){
                    if($lastResult * 100 >= $mocchuan->chisomc){
                        return [round($lastResult * 100, 2) . " %", 'Đạt'];
                    }else{
                        return [round($lastResult * 100, 2) . " %", 'Không đạt'];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            case 3: {
                return ['', ''];
                break;
            }
            case 4: {
                return ['', ''];
                break;
            }
            default: {
                return ['', ''];
            }
        }
    }
    public function kqTChiCon($sttCha, $stt, $mocchuan){
        switch($stt){
            case 1: {
                return ['', ''];
                break;
            }
            case 2: {
                return ['', ''];
                break;
            }
            default: {
                return ['', ''];
            }
        }
    }



    public function headings() :array {
        return [
            "Tiêu chí",
            "Chỉ số đánh giá",
            "Ngưỡng",
            "Thực tế",
            "Kết quả",
            "Giải trình"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("TC4 - Tài chính");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(60);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
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
                for($key = 0; $key < $this->countRow; $key++ ){
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
                    $event->sheet->getDelegate()->getStyle('D'. $key + 2)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_PERCENTAGE);
                }
            },
        ];
    }
}