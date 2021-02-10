<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MyExport implements
    FromView,
    WithEvents,
    WithTitle
{

    private $date;
    private $pays;
    private $jMax = 1;

    /**
     * BillingsExport constructor.
     * @param $date
     * @param $pays
     */
    public function __construct($date,$pays)
    {
        $this->date = $date;
        $this->pays = $pays;
    }

    public function view(): View
    {
        $d = explode('/',$this->date);
        $jour = $d[0];
        $mois = $d[1];
        $annee = $d[2];
        switch ($mois){
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
                if ($annee % 400 == 0){
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
        $from = date($annee.'-'.$mois.'-01');
        $to = date($annee.'-'.$mois.'-'.$this->jMax);
        $lesDates = DB::select('SELECT DISTINCT stats_date as d
                                FROM billing_stats
                                WHERE stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                ORDER BY stats_date ASC');
        $lesBEF = DB::select('SELECT DISTINCT b.subscriber_name as name
                                FROM billing_stats b, subscribers s
                                WHERE b.subscriber_name=s.name AND lower(s.sector)="banque" AND b.subscriber_name like "%'.$this->pays.'"
                                ORDER BY b.subscriber_name ASC');
        $lesSFD = DB::select('SELECT DISTINCT b.subscriber_name as name
                                FROM billing_stats b, subscribers s
                                WHERE b.subscriber_name=s.name AND lower(s.sector)="autre sfd" AND b.subscriber_name like "%'.$this->pays.'"
                                ORDER BY b.subscriber_name ASC');
        $nbBEF = DB::select('SELECT COUNT(DISTINCT b.subscriber_name) as n
                             FROM billing_stats b, subscribers s
                             WHERE b.subscriber_name = s.name
                             AND lower(s.sector) = "banque"
                             AND b.subscriber_name like "%'.$this->pays.'"');

        $nbSFD = DB::select('SELECT COUNT(DISTINCT b.subscriber_name) as n
                             FROM billing_stats b, subscribers s
                             WHERE b.subscriber_name = s.name
                             AND lower(s.sector) = "autre sfd"
                             AND b.subscriber_name like "%'.$this->pays.'"');


        return view('myexport', [
            'lesBEF' => $lesBEF,
            'lesSFD' => $lesSFD,
            'lesdates' => $lesDates,
            'from' => $from,
            'to' => $to,
            'BEF' => $nbBEF,
            'SFD' => $nbSFD,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
            $range = '';
            $rangeTotal = '';
            $border = '';
            $borderDate = '';
            $colorBEF = '';
            $colorSFD = '';
            switch ($this->pays){
                case 'BF':
                    $range = 'B2:CA4';
                    $rangeTotal = 'A5:CA5';
                    $border = 'B2:BO'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:BC2';
                    $colorSFD = 'BD2:BO2';
                    break;
                case 'ML':
                case 'BJ':
                    $range = 'B2:CA4';
                    $rangeTotal = 'A5:CA5';
                    $border = 'B2:CA'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:AW2';
                    $colorSFD = 'AX2:CA2';
                    break;
                case 'SN':
                    $range = 'B2:DN4';
                    $rangeTotal = 'A5:DN5';
                    $border = 'B2:DN'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:CG2';
                    $colorSFD = 'CH2:DN2';
                    break;
                case 'TG':
                    $range = 'B2:CA4';
                    $rangeTotal = 'A5:CA5';
                    $border = 'B2:BU'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:AQ2';
                    $colorSFD = 'AR2:BU2';
                    break;
                case 'NE':
                    $range = 'B2:CA4';
                    $rangeTotal = 'A5:CA5';
                    $border = 'B2:AW'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:AT2';
                    $colorSFD = 'AU2:AW2';
                    break;
                case 'GW':
                    $range = 'B2:P4';
                    $rangeTotal = 'A5:P5';
                    $border = 'B2:P'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:P2';
                    $colorSFD = 'B2:P2';
                    break;
                case 'CI':
                    $range = 'B2:DK4';
                    $rangeTotal = 'A5:DK5';
                    $border = 'B2:DK'.($this->jMax+5);
                    $borderDate = 'A5:A'.($this->jMax+5);
                    $colorBEF = 'B2:CG2';
                    $colorSFD = 'CH2:DK2';
                    break;
            }

                $event->sheet->getDelegate()->getStyle($range)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(12);
                $event->sheet->getStyle($range)->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle($rangeTotal)->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle($border)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getStyle($borderDate)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);
                $event->sheet->getDelegate()->getStyle($colorBEF)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('2995d7');
                if ($this->pays != 'GW')
                $event->sheet->getDelegate()->getStyle($colorSFD)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('fe2c00');
            }
        ];
    }

    public function title(): string
    {
        return "Détails";
    }
}
