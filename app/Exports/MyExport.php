<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MyExport implements
    FromView,
    WithEvents,
    WithTitle
{

    private $mois;
    private $pays;

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
        $intitutions = DB::select('SELECT DISTINCT subscriber_name as name
                                            FROM billing_stats
                                            WHERE subscriber_name LIKE "%'.$this->pays.'"
                                            ORDER BY subscriber_name ASC');
        $jourMax = 1;
        switch ($this->mois){
            case 5:
            case 3:
            case 7:
            case 8:
            case 10:
            case 12:
            case 1:
                $jourMax=31;
                break;
            case 2:
                $an = date('Y');
                if ($an % 400 == 0){
                    $jourMax=29;
                }
                else{
                    $jourMax=28;
                }
                break;
            case 6:
            case 9:
            case 11:
            case 4:
                $jourMax=30;
                break;
        }
        $from = date('2020-'.$this->mois.'-01');
        $to = date('2020-'.$this->mois.'-'.$jourMax);
        $lesDates = DB::select('SELECT DISTINCT stats_date as d
                                        FROM billing_stats
                                        WHERE stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                        ORDER BY stats_date ASC');

        return view('myexport', [
            'institutions' => $intitutions,
            'lesdates' => $lesDates,
            'from' => $from,
            'to' => $to,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getDelegate()->getStyle('A1:DR1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(12);
                $event->sheet->getStyle('A1:DR1')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle('A2:DR2')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle('A3:DR3')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);

                $event->sheet->getStyle('A1:DQ34')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
            }
        ];
    }

    public function title(): string
    {
        return "Details";
    }
}
