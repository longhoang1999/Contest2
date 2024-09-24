<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;


class DoinguGVToanTimeExport implements FromCollection, WithHeadings, WithEvents
{
    public $nam;
    public $chisogv = [];

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
        // $this->tinhchisogv();
        $this->chisogv = DB::table("danhsachgvtt")->where('nam', $this->nam)->where('doituong', 'Toàn thời gian')->count();
        $outputArr = [];

        $row = [
            "", "",
            "ĐH", "ThS", "TS",
            "PGS", "GS",
            ""
        ];
        array_push($outputArr, $row);

        $chisogv_solieu = DB::table('chisogv_solieu')->where('id', '<>', '4')->get();
        foreach($chisogv_solieu as $key =>  $chiso_sl){
            if($key == 0){
                $daihoc = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Đại học')->count();
                $thacsi = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Thạc sĩ')->count();
                $tiensi = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Tiến sĩ')->count();
                $pgs = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('chucdanh', 'Phó giáo sư')->count();
                $gs = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('chucdanh', 'Giáo sư')->count();
            }else if($key == 1){
                $currentYear = Carbon::now()->year;
                $daihoc = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Đại học')
                            ->where(function($query) use ($currentYear) {
                                $query->where(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nam')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 62]);
                                })
                                ->orWhere(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nữ')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 60]);
                                });
                            })
                            ->count();
                $thacsi = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Thạc sĩ')
                            ->where(function($query) use ($currentYear) {
                                $query->where(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nam')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 62]);
                                })
                                ->orWhere(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nữ')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 60]);
                                });
                            })
                            ->count();
                $tiensi = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('trinhdo', 'Tiến sĩ')
                            ->where(function($query) use ($currentYear) {
                                $query->where(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nam')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 62]);
                                })
                                ->orWhere(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nữ')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 60]);
                                });
                            })
                            ->count();
                $pgs = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('chucdanh', 'Phó giáo sư')
                            ->where(function($query) use ($currentYear) {
                                $query->where(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nam')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 62]);
                                })
                                ->orWhere(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nữ')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 60]);
                                });
                            })
                            ->count();
                $gs = DB::table('danhsachgvtt')->where('nam', $this->nam)
                            ->where('doituong', 'Toàn thời gian')
                            ->where('chucdanh', 'Giáo sư')
                            ->where(function($query) use ($currentYear) {
                                $query->where(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nam')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 62]);
                                })
                                ->orWhere(function($query) use ($currentYear) {
                                    $query->where('gioitinh', 'Nữ')
                                          ->whereBetween(DB::raw($currentYear . ' - namsinh'), [18, 60]);
                                });
                            })
                            ->count();
            }

            if($key == 0 || $key == 1){
                $row = [
                    $key + 1,
                    $chiso_sl->chisotk,
                    (string)$daihoc,
                    (string)$thacsi,
                    (string)$tiensi,
                    (string)$pgs,
                    (string)$gs,
                    (string)($daihoc + $thacsi + $tiensi)
                ];
                array_push($outputArr, $row);
            }

        }
        return collect($outputArr);
    }

    public function headings() :array {
        return [
            "STT",
            "Chỉ số thống kê",
            "Trình độ", "", "",
            "Chức danh", "",
            "Tổng số",
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                //Sheet name
                $event->sheet->getDelegate()->setTitle("Đội ngũ GV fulltime");
                // All headers
                $event->sheet->getDelegate()->getStyle("A1:H1")
                    ->getActiveSheet()
                    ->mergeCells('C1:E1')
                    ->getRowDimension('1')
                    ->setRowHeight(35);
                 $event->sheet->getDelegate()->getStyle("A1:H1")
                    ->getActiveSheet()
                    ->mergeCells('F1:G1');
                $event->sheet->getDelegate()->getStyle("A2:H2")
                    ->getActiveSheet()
                    ->getRowDimension('2')
                    ->setRowHeight(30);

                $event->sheet->getDelegate()->getStyle("A1:A2")
                    ->getActiveSheet()
                    ->mergeCells('A1:A2');
                $event->sheet->getDelegate()->getStyle("B1:B2")
                    ->getActiveSheet()
                    ->mergeCells('B1:B2');
                $event->sheet->getDelegate()->getStyle("H1:H2")
                    ->getActiveSheet()
                    ->mergeCells('H1:H2');

                // Set width
                $event->sheet->getDelegate()->getStyle("A")->getActiveSheet()->getColumnDimension('A')->setWidth(10);
                $event->sheet->getDelegate()->getStyle("B")->getActiveSheet()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("C")->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("D")->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("E")->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("F")->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getStyle("G")->getActiveSheet()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getDelegate()->getStyle("H")->getActiveSheet()->getColumnDimension('H')->setWidth(30);

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
                $event->sheet->getDelegate()->getStyle("H2")
                    ->applyFromArray($this->styleHeader);
                // Cell
                for($key = 0; $key < $this->chisogv; $key ++){
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
                    $event->sheet->getDelegate()
                        ->getStyle('H'. $key + 3)
                        ->applyFromArray($this->styleCell);
                }
            },
        ];
    }

}