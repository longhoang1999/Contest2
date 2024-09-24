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


class KetquahoatdongExport implements FromCollection, WithHeadings, WithEvents
// class AdmissionsExport implements FromView
{
    public $nam;
    public $ketquacshdc = [];
    public $ketquacshdc_old = [];

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
        $this->ketquacshdc = DB::table('ketquacshdc')->where("nam", $this->nam)->orderBy("stt", "asc")->get();
        $this->ketquacshdc_old = DB::table('ketquacshdc')->where("nam", $this->nam - 1)->orderBy("stt", "asc")->get();

        $outputArr = [];
        $row = [
            "", "", "",
            $this->nam - 1,
            $this->nam,
            "So sánh",
            ""
        ];
        array_push($outputArr, $row);

        foreach( $this->ketquacshdc as $key => $value){
            foreach( $this->ketquacshdc_old as $key2 => $value2){
                if($value->chisochinh_id == $value2->chisochinh_id){
                    $chiso_ketquacshdc = DB::table("chiso_ketquacshdc")->where("id", $value->chisochinh_id)->first();
                    if($chiso_ketquacshdc->loaics == "Số"){
                        switch($chiso_ketquacshdc->loaiss){
                            case "Lớn hơn":{
                                $chitieucl = ">" . $chiso_ketquacshdc->chitieucl;
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Lớn hơn hoặc bằng": {
                                $chitieucl = ">=" . $chiso_ketquacshdc->chitieucl;
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Bằng": {
                                $chitieucl = $chiso_ketquacshdc->chitieucl;
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Nhỏ hơn": {
                                $chitieucl = "<" . $chiso_ketquacshdc->chitieucl;
                                if($value->ketqua < $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Nhỏ hơn hoặc bằng": {
                                $chitieucl = "<=" . $chiso_ketquacshdc->chitieucl;
                                if($value->ketqua < $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                        }
                    }else if($chiso_ketquacshdc->loaics == "Phần trăm"){
                        switch($chiso_ketquacshdc->loaiss){
                            case "Lớn hơn":{
                                $chitieucl = ">" . $chiso_ketquacshdc->chitieucl . "%";
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Lớn hơn hoặc bằng": {
                                $chitieucl = ">=" . $chiso_ketquacshdc->chitieucl . "%";
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Bằng": {
                                $chitieucl = $chiso_ketquacshdc->chitieucl . "%";
                                if($value->ketqua > $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Nhỏ hơn": {
                                $chitieucl = "<" . $chiso_ketquacshdc->chitieucl . "%";
                                if($value->ketqua < $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                            case "Nhỏ hơn hoặc bằng": {
                                $chitieucl = "<=" . $chiso_ketquacshdc->chitieucl . "%";
                                if($value->ketqua < $value2->ketqua){
                                    $sosanh = "Tốt hơn";
                                }else if($value->ketqua == $value2->ketqua){
                                    $sosanh = "Tương đương";
                                }else{
                                    $sosanh = "Kém đi";
                                }
                                break;
                            }
                        }
                    }else if($chiso_ketquacshdc->loaics == "Đạt Không đạt"){
                        $chitieucl = $chiso_ketquacshdc->loaics;
                        if($value->ketqua == "Đạt" && $value2->ketqua == 'Không đạt'){
                            $sosanh = "Tốt hơn";
                        }else if($value->ketqua == "Không đạt" && $value2->ketqua == 'Đạt'){
                            $sosanh = "Kém đi";
                        }else if($value->ketqua == "Đạt" && $value2->ketqua == 'Đạt'){
                            $sosanh = "Tương đương";
                        }else if($value->ketqua == "Không đạt" && $value2->ketqua == 'Không đạt'){
                            $sosanh = "Tương đương";
                        }

                    }

                    $row = [
                        $key + 1,
                        $chiso_ketquacshdc ? $chiso_ketquacshdc->chisochinh : "",
                        $chitieucl,
                        $value2->ketqua,
                        $value->ketqua,
                        $sosanh,
                        $value->ghichu
                    ];
                    array_push($outputArr, $row);


                }
            }
        }
        return collect($outputArr);
    }
    public function headings() :array {
        return [
            "STT",
            "Chỉ số chính",
            "Chỉ tiêu chiến lược",
            "Kết quả đạt được",
            "",
            "",
            "Ghi chú"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("KQTH chỉ số hoạt động chính");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:G1")
                    ->getActiveSheet()
                    ->mergeCells('D1:F1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("A2:G2")
                    ->getActiveSheet()
                    ->getRowDimension('2')
                    ->setRowHeight(30);
                $event->sheet->getDelegate()->getStyle("A1:A2")
                    ->getActiveSheet()
                    ->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")
                    ->getActiveSheet()
                    ->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("C1:C2")
                    ->getActiveSheet()
                    ->mergeCells('C1:C2');
                $event->sheet->getDelegate()->getStyle("G1:G2")
                    ->getActiveSheet()
                    ->mergeCells('G1:G2');

                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(20);

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
                $event->sheet->getDelegate()->getStyle("A2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("B2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("C2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("D2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("E2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("F2")
                    ->applyFromArray($this->styleHeader);
                $event->sheet->getDelegate()->getStyle("G2")
                    ->applyFromArray($this->styleHeader);
                // Cell
                foreach( $this->ketquacshdc as $key => $value){
                    $event->sheet->getDelegate()
                        ->getStyle('A'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('B'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('C'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('D'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('E'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('F'. $key + 3)
                        ->applyFromArray($this->styleCell);
                    $event->sheet->getDelegate()
                        ->getStyle('G'. $key + 3)
                        ->applyFromArray($this->styleCell);
                }
            },
        ];
    }
}