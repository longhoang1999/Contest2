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


class TonghopTC3 implements FromCollection, WithHeadings, WithEvents
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
            'italic'    => true
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
            'bold' => true,
            'italic'    => true
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
            'size'  => 12,
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

        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "3")->first();
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
            $this->countRow ++;
            array_push($outputArr, $row);

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
                $this->countRow ++;
                array_push($outputArr, $row);
            }
        }
        return collect($outputArr);

    }
    // Tính toán
    public function kqTChi($stt, $mocchuan){
        switch($stt){
            case 1: {
                // $G17 / $TC5_L60
                $G17 = 0;
                $khuonvientsc = DB::table("khuonvientsc")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
                foreach($khuonvientsc as $key => $value){
                    $G17 = $G17 + (intval($value->dientichdat) * intval($value->vitrikhuonvien));
                }

                $soluongdt = DB::table('quymodt')->where("nam", $this->nam)->pluck('soluongdt')->toArray();
                $TC5_L60 = array_sum($soluongdt);

                if($TC5_L60 != 0 && $G17 != 0){
                    $result = $TC5_L60 != 0 ? $G17 / $TC5_L60 : 0;
                    if($result >= $mocchuan->chisomc){
                        return [round($result, 2), 'Đạt'];
                    }else{
                        return [round($result, 2), 'Không đạt'];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            case 2: {
                return ["", ""];
                break;
            }
            case 3: {
                return ["", ""];
                break;
            }
            case 4: {
                return ["", ""];
                break;
            }
            default: {
                return ['', ''];
            }
        }
    }
    public function kqTChiCon($sttCha, $stt, $mocchuan){
        if($sttCha == 2){
            switch($stt){
                case 1: {
                    // G64 / $TC5_L60
                    $G64 = 0;
                    $congtrinhdt = DB::table("congtrinhdt")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
                    foreach($congtrinhdt as $key => $value){
                        $G64 = $G64 + ( floatval($value->tongSsanxd) * floatval($value->hesoSsd));
                    }
                    $soluongdt = DB::table('quymodt')->where("nam", $this->nam)->pluck('soluongdt')->toArray();
                    $TC5_L60 = array_sum($soluongdt);

                    if($TC5_L60 != 0 && $G64 != 0){
                        $result = $TC5_L60 != 0 ? $G64 / $TC5_L60 : 0;
                        if($result >= $mocchuan->chisomc){
                            return [round($result, 2), 'Đạt'];
                        }else{
                            return [round($result, 2), 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                case 2:{
                    // 100 * $TC2_H12 / $TC2_H10
                    $TC2_H12 = DB::table('danhsachgvtt')
                        ->where('doituong', 'Toàn thời gian')
                        ->where('cholamrb', '1')
                        ->where('nam', $this->nam)
                        ->count();

                    $TC2_H10 = DB::table('danhsachgvtt')
                        ->where('doituong', 'Toàn thời gian')
                        ->count();

                    if($TC2_H12 != 0 && $TC2_H10 != 0){
                        $result = $TC2_H10 != 0 ? (100 * $TC2_H12 / $TC2_H10) : 0;
                        if($result >= $mocchuan->chisomc){
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Đạt'];
                            else
                                return [round($result, 2), 'Đạt'];
                        }else{
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Không đạt'];
                            else
                                return [round($result, 2), 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                default: {
                    return ['', ''];
                }
            }
        }

        if($sttCha == 3){
            switch($stt){
                case 1: {
                    // =(C70+C71)/C68
                    $C70 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "3")->value('giatri') ?? 0;
                    $C71 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "4")->value('giatri') ?? 0;
                    $C68 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "1")->value('giatri') ?? 0;
                    if($C70+$C71 != 0 && $C68 != 0){
                        $result = $C68 != 0 ? (($C70+$C71)/$C68) : 0;
                        if($result >= $mocchuan->chisomc){
                            return [round($result, 2), "Đạt"];
                        }else{
                            return [round($result, 2), "Không đạt"];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                case 2:{
                    // =C70/(C68*40)*5+C71/((C68*40)-C70)*(C72/TC5_J60)
                    $C70 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "3")->value('giatri') ?? 0;
                    $C68 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "1")->value('giatri') ?? 0;
                    $C71 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "4")->value('giatri') ?? 0;
                    $C72 = DB::table('giaotrinhck')->where("nam", $this->nam)->where('chisotk', "5")->value('giatri') ?? 0;

                    $soluonggd = DB::table('quymodt')->where("nam", $this->nam)->pluck('soluonggd')->toArray();
                    $TC5_J60 = array_sum($soluonggd);

                    if($C68 != 0 && $TC5_J60 != 0){
                        if( ((($C68*40)-$C70)*($C72/$TC5_J60)) != 0){
                            $result = $C70/($C68*40)*5+$C71/(($C68*40)-$C70)*($C72/$TC5_J60) ?? 0;
                        }else{
                            $result = 0;
                        }

                        if($result >= $mocchuan->chisomc){
                            return [round($result, 2), "Đạt"];
                        }else{
                            return [round($result, 2), "Không đạt"];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                default: {
                    return ['', ''];
                }
            }
        }

        if($sttCha == 4){
            switch($stt){
                case 1: {
                    // =C80*100/C79
                    $C80 = DB::table('hatangcntt')->where('chisotk', '3')->where("nam", $this->nam)->value("giatri") ?? 0;
                    $C79 = DB::table('hatangcntt')->where('chisotk', '2')->where("nam", $this->nam)->value("giatri") ?? 0;

                    if($C79 != 0){
                        $result = $C79 != 0 ? $C80*100/$C79 : 0;
                        if($result >= $mocchuan->chisomc){
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Đạt'];
                            else
                                return [round($result, 2), 'Đạt'];
                        }else{
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Không đạt'];
                            else
                                return [round($result, 2), 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                case 2:{
                    // =C78/ROUNDUP(TC5_H60/1000,0)
                    $C78 = DB::table('hatangcntt')->where('chisotk', '1')->where("nam", $this->nam)->value("giatri") ?? 0;
                    $tong = DB::table('quymodt')->where("nam", $this->nam)->pluck('tong')->toArray();
                    $TC5_H60 = array_sum($tong);

                    if($TC5_H60 != 0){
                        $result = $TC5_H60 != 0 ? $C78 / round(($TC5_H60 / 1000), 0) : 0;
                        if($result >= $mocchuan->chisomc){
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Đạt'];
                            else
                                return [round($result, 2), 'Đạt'];
                        }else{
                            if($mocchuan->donvitinh == 'percent')
                                return [round($result, 2) . '%', 'Không đạt'];
                            else
                                return [round($result, 2), 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                }
                default: {
                    return ['', ''];
                }
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
                $event->sheet->getDelegate()->setTitle("TC3 - Cơ sở vật chất");
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
