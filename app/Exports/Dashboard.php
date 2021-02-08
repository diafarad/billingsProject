<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Dashboard implements
    FromView,
    WithEvents,
    WithTitle
{

    private $mois;
    private $pays;
    private $jMax = 1;

    /**
     * BillingsExport constructor.
     * @param $mois
     * @param $pays
     */
    public function __construct($mois,$pays)
    {
        $this->mois = $mois;
        $this->pays = $pays;
    }
    public function view(): View
    {
        $institutions = DB::select('SELECT DISTINCT subscriber_name as name
                                            FROM billing_stats
                                            WHERE subscriber_name LIKE "%'.$this->pays.'"
                                            ORDER BY subscriber_name ASC');
        switch ($this->mois){
            case 5:
            case 3:
            case 7:
            case 8:
            case 10:
            case 12:
            case 1:
                $this->jMax=31;
                break;
            case 2:
                $an = date('Y');
                if ($an % 400 == 0){
                    $this->jMax=29;
                }
                else{
                    $this->jMax=28;
                }
                break;
            case 6:
            case 9:
            case 11:
            case 4:
                $this->jMax=30;
                break;
        }
        $from = date('2020-'.$this->mois.'-01');
        $to = date('2020-'.$this->mois.'-'.$this->jMax);
        return view('dashboard', [
            'institutions' => $institutions,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $range = 'A1:P3';
                $colorRange1 = 'C2:G2';
                $colorRange2 = 'H2:M2';

                $event->sheet->getDelegate()->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(15);
                $event->sheet->getStyle($range)->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle('C2:M2')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('B3:M3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle('B3:M3')->getAlignment()->setWrapText(true);
                $event->sheet->getDelegate()->getStyle('B3:M3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $event->sheet->getDelegate()->getStyle($colorRange1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('fe2c00');
                $event->sheet->getDelegate()->getStyle($colorRange2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('0dd4ff');
                $event->sheet->getDelegate()->getStyle('F4:F40')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('J4:J40')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        ];
    }

    public function title(): string
    {
        return "Dashboard";
    }
}
