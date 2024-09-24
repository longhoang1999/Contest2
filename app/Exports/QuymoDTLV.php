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


class QuymoDTLV implements FromCollection, WithHeadings, WithEvents
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
        $row = [
            "", "",
            "CQ", "VLVH", "ĐTTX",
            "ThS", "TS",
            "",
            "Kgd", "Số lượng",
            "Kdt", "Số lượng",
            "KTC", "KBB", "KLV"
        ];
        array_push($outputArr, $row);


        $this->quymodt_chiso = DB::table("quymodt_chiso")->get();
        foreach( $this->quymodt_chiso as $key => $value){
            $row = [
                $key + 1,
                $value->linhvuc,
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("cq") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("vlvh") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("dttx") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("ths") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("ts") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("tong") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("kgd") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("soluonggd") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("kdt") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("soluongdt") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("ktc") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("kbb") ?? "",
                DB::table("quymodt")->where("nam", $this->nam)->where("linhvuc", $value->id)->value("klv") ?? "",
            ];
            array_push($outputArr, $row);
        }
        return collect($outputArr);
    }
    public function headings() :array {

        return [
            "STT",
            "Lĩnh vực đào tạo",
            "Quy mô ĐH", "", "",
            "Quy mô SĐH", "",
            "Tổng",
            "Quy đổi về giảng dạy", "",
            "Quy đổi về diện tích", "",
            "Hệ số kinh phí",
            "Hệ số công bố",
            "Hệ số quy đổi"
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("QMĐT theo LV và TDĐT");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:O1")->getActiveSheet()->getRowDimension('1')->setRowHeight(35);
                $event->sheet->getDelegate()->getStyle("A2:O2")->getActiveSheet()->getRowDimension('2')->setRowHeight(30);
                // Merge cell
                $event->sheet->getDelegate()->getStyle("A1:A2")->getActiveSheet()->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")->getActiveSheet()->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("C1:E1")->getActiveSheet()->mergeCells('C1:E1');
                $event->sheet->getDelegate()->getStyle("F1:G1")->getActiveSheet()->mergeCells('F1:G1');
                $event->sheet->getDelegate()->getStyle("H1:H2")->getActiveSheet()->mergeCells('H1:H2');
                $event->sheet->getDelegate()->getStyle("I1:J1")->getActiveSheet()->mergeCells('I1:J1');
                $event->sheet->getDelegate()->getStyle("K1:L1")->getActiveSheet()->mergeCells('K1:L1');






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
                $event->sheet->getDelegate()->getStyle("M")->getActiveSheet()->getColumnDimension('M')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("N")->getActiveSheet()->getColumnDimension('N')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("O")->getActiveSheet()->getColumnDimension('O')->setWidth(30);


                for ($i = 'A'; $i <= 'O'; $i++) {
                    $event->sheet->getDelegate()->getStyle($i . '1')
                        ->applyFromArray($this->styleHeader);
                    $event->sheet->getDelegate()->getStyle($i . '2')
                        ->applyFromArray($this->styleHeaderSmall);
                }


                // Cell
                for($key = 0; $key < count($this->quymodt_chiso); $key ++){
                    for ($i = 'A'; $i <= 'B'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $key + 3)
                            ->applyFromArray($this->styleCellNotEdit);
                    }
                    for ($i = 'C'; $i <= 'O'; $i++) {
                        $event->sheet->getDelegate()
                            ->getStyle($i . $key + 3)
                            ->applyFromArray($this->styleCell);
                    }

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
