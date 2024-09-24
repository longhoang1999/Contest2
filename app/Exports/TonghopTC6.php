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

use Illuminate\Support\Facades\DB;


class TonghopTC6 implements FromCollection, WithHeadings, WithEvents
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
        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "6")->first();
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
                $kqTieuchiCon = $this->kqTChiCon($keyCon + 1, $mocchuan2);
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
               // ( ($TC4_C15 / $TC4_C8  + $TC4_D15 / $TC4_D8 + $TC4_E15 / $TC4_E8) * $TC5_O60 ) / 3
                $TC4_C15 = DB::table('thuchihd')->where('nam', $this->nam)->where("chisotk", "38")->value("giatri") ?? 0;
                $TC4_C8 = DB::table('thuchihd')->where('nam', $this->nam)->where("chisotk", "1")->value("giatri") ?? 0;

                $TC4_D15 = DB::table('thuchihd')->where('nam', $this->nam - 1)->where("chisotk", "38")->value("giatri") ?? 0;
                $TC4_D8 = DB::table('thuchihd')->where('nam', $this->nam - 1)->where("chisotk", "1")->value("giatri") ?? 0;

                $TC4_E15 = DB::table('thuchihd')->where('nam', $this->nam - 2)->where("chisotk", "38")->value("giatri") ?? 0;
                $TC4_E8 = DB::table('thuchihd')->where('nam', $this->nam - 2)->where("chisotk", "1")->value("giatri") ?? 0;

                $klv = DB::table("quymodt")->where("nam", $this->nam)->pluck('klv')->toArray();
                $TC5_O60 = array_sum($klv);

                $result = (
                    (($TC4_C8 != 0 ? $TC4_C15 / $TC4_C8 : 0) +
                    ($TC4_D8 != 0 ? $TC4_D15 / $TC4_D8 : 0) +
                    ($TC4_E8 != 0 ? $TC4_E15 / $TC4_E8 : 0)) * $TC5_O60
                ) / 3 ;
                if($result * 100 > $mocchuan->chisomc){
                    return [ round($result * 100, 3) . " %", "Đạt"];
                }else{
                    return [ round($result * 100, 3) . " %", "Không đạt"];
                }
                break;
            }
            case 2: {
                return ["", ""];
                break;
            }
            default: {
                return ['', ''];
            }
        }
    }
    public function kqTChiCon($stt, $mocchuan){
        switch($stt){
            case 1: {
                $congbokhgv = DB::table("congbokhgv")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
                $tongsl = 0;
                foreach($congbokhgv as $key => $value){
                    $tongsl = $tongsl + intval($value->soluong);
                }

                $chisogvfull = DB::table("chisogv")->where("nam", $this->nam)->where('chisotk', '1')->first();
                if( $chisogvfull){
                    $tongFull = $chisogvfull->dh + $chisogvfull->thS + $chisogvfull->ts + $chisogvfull->pgs + $chisogvfull->gs;
                }
                if($tongsl != 0 && $tongFull != 0){
                    $kq = round($tongsl / $tongFull, 1);
                    if($kq >= $mocchuan->chisomc ){
                        return [$kq, "Đạt"];
                    }else{
                        return [$kq, "Không đạt"];
                    }
                }
                return ['', ''];
                break;
            }
            case 2: {
                $congbokhgv = DB::table("congbokhgv")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
                $tongqd = 0;
                foreach($congbokhgv as $key => $value){
                    $tongqd = $tongqd + intval($value->quydoi);
                }


                $chisogvfull = DB::table("chisogv")->where("nam", $this->nam)->where('chisotk', '1')->first();
                if( $chisogvfull){
                    $tongFull = $chisogvfull->dh + $chisogvfull->thS + $chisogvfull->ts + $chisogvfull->pgs + $chisogvfull->gs;
                }

                if($tongqd != 0 && $tongFull != 0){
                    $kq = round($tongqd / $tongFull, 1);
                    if($kq >= $mocchuan->chisomc ){
                        return [$kq, "Đạt"];
                    }else{
                        return [$kq, "Không đạt"];
                    }
                }
                return ['', ''];

                return ["Chưa tính tieu chi con 1.2", "Chưa tính tieu chi con 1.2"];
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
                $event->sheet->getDelegate()->setTitle("TC6 - Nghiên cứu và ĐM sáng tạo");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:F1")
                    ->getActiveSheet()
                    // ->mergeCells('D1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
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
                }
            },
        ];
    }
}
