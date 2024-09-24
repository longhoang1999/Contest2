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


class TonghopTC2 implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $tieuchi = [];
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
        $tieuchuan = DB::table("tieuchuan")->where("bo_tieuchuan_id", "18")->where("stt", "2")->first();
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
        return collect($outputArr);
    }
    // Tính toán
    public function kqTChi($stt, $mocchuan){
        switch($stt){
            // 	Tiêu chí 2.1 Tỉ lệ người học trên giảng viên
            case 1: {
                $soluonggd = DB::table('quymodt')->where("nam", $this->nam)->pluck('soluonggd')->toArray();
                $sum = array_sum($soluonggd);

                $tonggvFulltime = DB::table('danhsachgvtt')->where('nam', $this->nam)
                    ->where('doituong', 'Toàn thời gian')->count();

                if($sum != 0 && $tonggvFulltime != 0){
                    $result = $sum / floatval($tonggvFulltime);
                    if($result <= $mocchuan->chisomc){
                        return [round($result, 2), "Đạt"];
                    }else{
                        return [round($result, 2), "Không đạt"];
                    }
                }else{
                    return ['---', '---'];
                }
                break;
            }
            // 	Tiêu chí 2.2 Tỉ lệ giảng viên cơ hữu trong độ tuổi lao động
            case 2: {
                $danhsachgvttFT = DB::table('danhsachgvtt')->where('nam', $this->nam)
                        ->where('doituong', 'Toàn thời gian');

                $countTuoiLd = 0;
                foreach($danhsachgvttFT->get() as $value){
                    $tuoi = date('Y') - $value->namsinh;
                    if($value->gioitinh == 'Nam'){
                        if($tuoi >= 15 && $tuoi <= 61){
                            $countTuoiLd += 1;
                        }
                    }
                    if($value->gioitinh == 'Nữ'){
                        if($tuoi >= 15 && $tuoi <= 56){
                            $countTuoiLd += 1;
                        }
                    }
                }
                if( $danhsachgvttFT->count() != 0  && $countTuoiLd != 0){
                    $result = ($countTuoiLd / $danhsachgvttFT->count()) * 100;
                    if($result < $mocchuan->chisomc){
                        return [round($result, 2) . "%", "Không đạt"];
                    }else{
                        return [round($result, 2) . "%", "Đạt"];
                    }
                }
                return ["---", "---"];
                break;
            }
            // Tiêu chí 2.3 Tỉ lệ giảng viên có trình độ tiến sĩ
            case 3: {
                $danhsachgvtt_TS = DB::table('danhsachgvtt')
                        ->where('trinhdo', 'Tiến sĩ')
                        ->where('doituong', 'Toàn thời gian')
                        ->where('nam', $this->nam)->count();

                $danhsachgvtt = DB::table('danhsachgvtt')
                        ->where('doituong', 'Toàn thời gian')
                        ->where('nam', $this->nam)->count();

                if($danhsachgvtt_TS != 0 && $danhsachgvtt != 0){
                    $result = ($danhsachgvtt_TS / $danhsachgvtt) * 100;
                    if($result < $mocchuan->chisomc){
                        return [round($result, 2) . "%", "Không đạt"];
                    }else{
                        return [round($result, 2) . "%", "Đạt"];
                    }
                }
                return ["---", "---"];
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
                $event->sheet->getDelegate()->setTitle("TC2 - Giảng viên");
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