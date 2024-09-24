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


class TonghopTC5 implements FromCollection, WithHeadings, WithEvents
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
            'bold' => true
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
        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "5")->first();
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

                return ['', ''];
                break;
            }
            case 2: {
                return ['', ''];
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
            case 5: {
                $thamso1 = DB::table('khaosatnh')->where("cauhoiks", "4")->where("nam", $this->nam)->select("daihoc_luotph", "saudaihoc_luotph")->first();
                if($thamso1){
                    $sum_soluotPh = $thamso1->daihoc_luotph + $thamso1->saudaihoc_luotph;
                }else{
                    $sum_soluotPh = 0;
                }

                $thamso2 = DB::table('khaosatnh')->where("cauhoiks", "4")->where("nam", $this->nam)->select("daihoc_phtc", "saudaihoc_phtc")->first();
                if($thamso2){
                    $sum_phanhoiTc = $thamso2->daihoc_phtc + $thamso2->saudaihoc_phtc;
                }else{
                    $sum_phanhoiTc = 0;
                }
                if($sum_soluotPh != 0){
                    $result = $sum_soluotPh == 0 ? 0 : $sum_phanhoiTc / $sum_soluotPh;
                    if(round($result * 100, 3) >= $mocchuan->chisomc){
                        return [round($result * 100, 3) . " %", 'Đạt'];
                    }else{
                        return [round($result * 100, 3) . " %", 'Không đạt'];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            default: {
                return ['', ''];
            }
        }
    }
    public function kqTChiCon($sttCha, $stt, $mocchuan){
        if($sttCha == "1"){
            switch($stt){
                case 1: {
                    $result = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)
                        ->where('nam_solieu', $this->nam)
                        ->value("tile_nh") ?? "";
                    if($result != ""){
                        if(floatval(substr($result, 0, -1)) >= $mocchuan->chisomc){
                            return [$result, 'Đạt'];
                        }else{
                            return [$result, 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                case 2: {
                    $daotao_tuyensinh = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)
                    ->where('nam_solieu', $this->nam)
                    ->value("sonh_moi") ?? 0;

                    $daotao_tuyensinhTru1 = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam )
                    ->where('nam_solieu', $this->nam - 1)
                    ->value("sonh_moi") ?? 0;

                    $daotao_tuyensinhTru2 = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)
                    ->where('nam_solieu', $this->nam - 2)
                    ->value("sonh_moi") ?? 0;

                    $daotao_tuyensinhTru3 = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)
                    ->where("nam_solieu", $this->nam - 3)
                    ->value("sonh_moi") ?? 0;

                    if($daotao_tuyensinhTru1 != 0 && $daotao_tuyensinhTru2 != 0 && $daotao_tuyensinhTru3 != 0){
                        $result = ((
                            $daotao_tuyensinh / $daotao_tuyensinhTru1 +
                            $daotao_tuyensinhTru1 / $daotao_tuyensinhTru2 +
                            $daotao_tuyensinhTru2 / $daotao_tuyensinhTru3
                        ) / 3 ) - 1;
                        if(round($result * 100, 2) >= $mocchuan->chisomc){
                            return [round($result * 100, 2) . " %" , 'Đạt'];
                        }else{
                            return [round($result * 100, 2) . " %" , 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                default: {
                    return ['', ''];
                }
            }
        }else if($sttCha == "2"){
            switch($stt){
                case 1: {
                    $D16 = DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)
                    ->where("nam_solieu", $this->nam - 1)->value("tong_svcmcn") ?? 0;

                    $sum = (
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("soht_dh")) ?? 0) +
                        (intval(DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("soht_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotn_dh")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotn_qh15")) ?? 0 ) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotn_qh15")) ?? 0) +
                        (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotn_qh15") )?? 0)
                    );

                    if($D16 != 0){
                        $result = $D16 != 0 ? (($D16 - $sum ) / $D16) : 0;
                        if(round($result * 100, 2) <= $mocchuan->chisomc){
                            return [round($result * 100, 2) . "%", 'Đạt'];
                        }else{
                            return [round($result * 100, 2) . "%", 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                case 2: {
                    if((intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sonh_moi") )?? 0) != 0){
                        $result = (
                            (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sonh_moi") )?? 0) -
                            (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("soht_dh") )?? 0) -
                            (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotn_dh") )?? 0)
                        )/ (intval( DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sonh_moi") )?? 0);
                    }else{
                        return ["---", '---'];
                    }

                    if(round($result * 100, 2) <= $mocchuan->chisomc){
                        return [round($result * 100, 2) . "%", 'Đạt'];
                    }else{
                        return [round($result * 100, 2) . "%", 'Không đạt'];
                    }
                    break;
                }
                default: {
                    return ['', ''];
                }
            }
        }else if($sttCha == "3"){
            switch($stt){
                case 1: {
                    $sum = (
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotndh_nh") ?? "0"))) +

                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotn_qh05_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotn_qh05_nh") ?? "0"))) +

                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotn_qh15_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotn_qh15_nh") ?? "0")))
                    );
                    if($sum != 0){
                        if($sum  >= $mocchuan->chisomc){
                            return [$sum . " %", 'Đạt'];
                        }else{
                            return [$sum . " %", 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                case 2: {
                    $sum = (
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 1)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 2)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 3)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 4)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 5)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 6)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 7)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 8)->value("sotndh_nh") ?? "0"))) +
                        intval(preg_replace('/[^0-9.]/', '',  (DB::table("daotao_tuyensinh_3")->where("nam_nhap", $this->nam)->where("nam_solieu", $this->nam - 9)->value("sotndh_nh") ?? "0")))
                    );
                    if($sum != 0){
                        if($sum  >= $mocchuan->chisomc){
                            return [$sum . " %", 'Đạt'];
                        }else{
                            return [$sum . " %", 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                default: {
                    return ['', ''];
                }
            }
        }else if($sttCha == "4"){
            switch($stt){
                case 1: {
                    $thamso1 = DB::table('khaosatnh')->where("cauhoiks", "1")->where("nam", $this->nam)->select("daihoc_luotph", "saudaihoc_luotph")->first();
                    if($thamso1){
                        $sum_soluotPh = $thamso1->daihoc_luotph + $thamso1->saudaihoc_luotph;
                    }else{
                        $sum_soluotPh = 0;
                    }

                    $thamso2 = DB::table('khaosatnh')->where("cauhoiks", "1")->where("nam", $this->nam)->select("daihoc_phtc", "saudaihoc_phtc")->first();
                    if($thamso2){
                        $sum_phanhoiTc = $thamso2->daihoc_phtc + $thamso2->saudaihoc_phtc;
                    }else{
                        $sum_phanhoiTc = 0;
                    }

                    if($sum_soluotPh != 0){
                        $result = $sum_soluotPh == 0 ? 0 : $sum_phanhoiTc / $sum_soluotPh;
                        if(round($result * 100, 3) >= $mocchuan->chisomc){
                            return [round($result * 100, 3) . " %", 'Đạt'];
                        }else{
                            return [round($result * 100, 3) . " %", 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
                }
                case 2: {
                    $thamso1 = DB::table('khaosatnh')->where("cauhoiks", "2")->where("nam", $this->nam)->select("daihoc_luotph", "saudaihoc_luotph")->first();
                    if($thamso1){
                        $sum_soluotPh = $thamso1->daihoc_luotph + $thamso1->saudaihoc_luotph;
                    }else{
                        $sum_soluotPh = 0;
                    }

                    $thamso2 = DB::table('khaosatnh')->where("cauhoiks", "2")->where("nam", $this->nam)->select("daihoc_phtc", "saudaihoc_phtc")->first();
                    if($thamso2){
                        $sum_phanhoiTc = $thamso2->daihoc_phtc + $thamso2->saudaihoc_phtc;
                    }else{
                        $sum_phanhoiTc = 0;
                    }

                    if($sum_soluotPh != 0){
                        $result = $sum_soluotPh == 0 ? 0 : $sum_phanhoiTc / $sum_soluotPh;
                        if(round($result * 100, 3) >= $mocchuan->chisomc){
                            return [round($result * 100, 3) . " %", 'Đạt'];
                        }else{
                            return [round($result * 100, 3) . " %", 'Không đạt'];
                        }
                    }else{
                        return ['---', '---'];
                    }
                    break;
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
                $event->sheet->getDelegate()->setTitle("TC5 - Tuyển sinh đào tạo");
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
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 2)
                        ->applyFromArray($this->styleCellNotEdit);
                }
            },
        ];
    }
}