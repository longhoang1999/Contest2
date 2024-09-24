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


class TonghopTC1 implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $tieuchi;
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
        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "1")->first();
        $this->tieuchi = DB::table('tieuchi')->where("tieuchuan_id", $tieuchuan->id)->orderBy("stt", "asc")->get();
        $outputArr = [];

        foreach($this->tieuchi as $key => $value){
            $mocchuan = DB::table("mocchuan")->where("idTieuchi", $value->id)->first();//
            // Tính toán
            $kqTieuchi = $this->kqTChi($key + 1, $mocchuan);
            $row = [
                $tieuchuan->stt .".". $value->stt,
                $value->mo_ta,
                $mocchuan->donvitinh == "percent" ? $mocchuan->chisomc . "%" : $mocchuan->chisomc,
                $kqTieuchi[0],
                $kqTieuchi[1],
                ""
            ];
            array_push($outputArr, $row);
        }
        //dd($outputArr);
        return collect($outputArr);
    }
    // Tính đã ban hành
    public function tinhtrangVBQC(){
        $vanbanquyche = DB::table("vanbanquyche")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        foreach( $vanbanquyche as $key => $value){
            $dateTimestamp = strtotime($value->ngaybanhanh);
            $nowTimestamp = time();

            if ($dateTimestamp < $nowTimestamp) {
                DB::table('vanbanquyche')->where('id',$value->id )->where("nam", $this->nam)->update([
                    'tinhtrang' => 'Đã ban hành'
                ]);
            } else {
                DB::table('vanbanquyche')->where('id',$value->id )->where("nam", $this->nam)->update([
                    'tinhtrang' => 'Chưa ban hành'
                ]);
            }
        }
    }

    // Tính toán
    public function kqTChi($stt, $mocchuan){
        // Tình trạng văn bản quy chế
        $this->tinhtrangVBQC();

        switch($stt){
            case 1: {
                $now = Carbon::now();
                $lanhdaocc = DB::table("lanhdaocc")->where("nam", $this->nam)->orderBy("stt", "asc")->get();
                $arr = array();
                foreach($lanhdaocc as $ldcc){
                    $date = Carbon::createFromFormat('d/m/Y', $ldcc->thoihancv);
                    if($now > $date){
                        $daysDiff = $now->diffInMonths($date);
                        array_push($arr,  $daysDiff);
                    }else{
                        array_push($arr,  0);
                    }
                }
                if(count($arr) == 0){
                    return ['---', '---'];
                }else{
                    if(min($arr) > $mocchuan->chisomc){
                        return [strval(min($arr)), 'Không đạt'];
                    }else{

                        return [strval(min($arr)), 'Đạt'];
                    }
                }
                break;
            }
            case 2: {
                $vanbanquyche = DB::table("vanbanquyche")->where("nam", $this->nam)->orderBy("stt", "asc");
                $countFail = 0;
                $total = $vanbanquyche->count();
                foreach($vanbanquyche->get() as $value){
                    if($value->tinhtrang != 'Đã ban hành'){
                        $countFail ++;
                    }
                }

                if($total != 0){
                    if(100 - round(($countFail / $total) * 100, 2) < $mocchuan->chisomc){
                        $result = strval(100 - round(($countFail / $total) * 100, 2)) . "%";
                        return [$result, 'Không đạt'];
                    }else{
                        $result = strval(100 - round(($countFail / $total) * 100, 2)) . "%";
                        return [$result, 'Đạt'];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            case 3: {
                $ketquacshdc = DB::table('ketquacshdc')->where("nam", $this->nam)->orderBy("stt", "asc");
                $ketquacshdc_old = DB::table('ketquacshdc')->where("nam", $this->nam - 1)->orderBy("stt", "asc");

                $TotalCount = $ketquacshdc->count();
                $countResult = 0;

                foreach( $ketquacshdc->get() as $key => $value){
                    foreach( $ketquacshdc_old->get() as $key2 => $value2){
                        if($value->chisochinh_id == $value2->chisochinh_id){
                            $chiso_ketquacshdc = DB::table("chiso_ketquacshdc")->where("id", $value->chisochinh_id)->first();
                            if($chiso_ketquacshdc->loaics == "Số"){
                                switch($chiso_ketquacshdc->loaiss){
                                    case "Lớn hơn":{
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Lớn hơn hoặc bằng": {
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Bằng": {
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Nhỏ hơn": {
                                        if($value->ketqua < $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Nhỏ hơn hoặc bằng": {
                                        if($value->ketqua < $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                }
                            }else if($chiso_ketquacshdc->loaics == "Phần trăm"){
                                switch($chiso_ketquacshdc->loaiss){
                                    case "Lớn hơn":{
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Lớn hơn hoặc bằng": {
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Bằng": {
                                        if($value->ketqua > $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Nhỏ hơn": {
                                        if($value->ketqua < $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                    case "Nhỏ hơn hoặc bằng": {
                                        if($value->ketqua < $value2->ketqua){
                                            $countResult ++;
                                        }
                                        break;
                                    }
                                }
                            }else if($chiso_ketquacshdc->loaics == "Đạt Không đạt"){
                                $chitieucl = $chiso_ketquacshdc->loaics;
                                if($value->ketqua == "Đạt" && $value2->ketqua == 'Không đạt'){
                                    $countResult ++;
                                }else if($value->ketqua == "Đạt" && $value2->ketqua == 'Đạt'){
                                    $countResult ++;
                                }
                            }
                        }
                    }
                }




                if($TotalCount != 0){
                    if(round(($countResult / $TotalCount) * 100, 2) < $mocchuan->chisomc){
                        $result = strval(round(($countResult / $TotalCount) * 100, 2)) . "%";
                        return [$result, 'Không đạt'];
                    }else{
                        $result = strval(round(($countResult / $TotalCount) * 100, 2)) . "%";
                        return [$result, 'Đạt'];
                    }
                }
                return ['---', '---'];
                break;
            }
            case 4: {
                $thongkelbcdg = DB::table("thongkelbcdg")->where("nam", $this->nam)->orderBy("stt", "asc");
                $countFail = 0;
                $total = $thongkelbcdg->count();
                foreach($thongkelbcdg->get() as $value){
                    if($value->daydu_hemis != 'Đầy đủ'){
                        $countFail ++;
                    }
                }
                if( $total  != 0){
                    if(100 - round(($countFail / $total) * 100, 2) < $mocchuan->chisomc){
                        $result = strval(100 - round(($countFail / $total) * 100, 2)) . "%";
                        return [$result, 'Không đạt'];
                    }else{
                        $result = strval(100 - round(($countFail / $total) * 100, 2)) . "%";
                        return [$result, 'Đạt'];
                    }
                }
                return ['---', '---'];
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
                $event->sheet->getDelegate()->setTitle("TC1 - Tổ chức và quản trị");
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
                foreach($this->tieuchi as $key => $value){
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