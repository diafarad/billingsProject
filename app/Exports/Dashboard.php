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

    private $date;
    private $pays;
    private $jMax = 1;
    private $nbreBef = 0;
    private $nbreSfd = 0;

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
        $moisPrec = 0;
        $annee = $d[2];
        $today = $annee.'-'.$mois.'-'.$jour;
        switch ($mois){
            case 1:
                $moisPrec = 12;
                $this->jMax=31;
                break;
            case 5:
            case 3:
            case 7:
            case 8:
            case 10:
            case 12:
                $moisPrec = $mois - 1;
                $this->jMax=31;
                break;
            case 2:
                $moisPrec = $mois - 1;
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
                $moisPrec = $mois - 1;
                $this->jMax=30;
                break;
        }
        $from = date($annee.'-'.$mois.'-01');
        $to = date($annee.'-'.$mois.'-'.$this->jMax);
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
        foreach($nbBEF as $nbef){
            $this->nbreBef = $nbef->n;
        }
        foreach($nbSFD as $nsfd){
            $this->nbreSfd = $nsfd->n;
        }
        return view('dashboard', [
            'lesBEF' => $lesBEF,
            'lesSFD' => $lesSFD,
            'from' => $from,
            'to' => $to,
            'jour' => $this->date,
            'today' => $today,
            'jj' => $jour,
            'mm' => $mois,
            'aaaa' => $annee,
            'mPrec' => $moisPrec,
            'pays' => $this->pays,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $range = 'A1:P3';
                $ok = $this->nbreBef+12;
                $b = $this->nbreBef+5;
                $colorRange1 = 'C2:G2';
                $colorRange2 = 'H2:M2';
                $borderBEF = 'B4:M'.$b;

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
                $event->sheet->getStyle($borderBEF)->applyFromArray([
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
                $row = $this->nbreBef+5;
                $event->sheet->getDelegate()->getStyle('F4:F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('J4:J'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $event->sheet->getDelegate()->getStyle('M4:M'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                if($this->nbreSfd>0){
                    $b1 = $this->nbreBef+14;
                    $b2 = $this->nbreSfd+$b1+1;
                    $ok = $this->nbreBef+12;
                    $colorSFD1 = 'C'.$ok.':'.'G'.$ok;
                    $colorSFD2 = 'H'.$ok.':'.'M'.$ok;
                    $borderSFD = 'B'.$b1.':'.'M'.$b2;

                    $event->sheet->getDelegate()->getStyle($colorSFD1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getDelegate()->getStyle($colorSFD2)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $col = $ok+1;
                    $event->sheet->getStyle('A'.$ok.':P'.$col)->applyFromArray([
                        'font' => [
                            'bold' => 'true'
                        ]
                    ]);

                    $event->sheet->getStyle($borderSFD)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                    $event->sheet->getStyle('C'.$ok.':M'.$ok)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);
                    $r = $ok+1;
                    $event->sheet->getStyle('B'.$r.':M'.$r)->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                'color' => ['argb' => '000000'],
                            ],
                        ],
                    ]);

                    $event->sheet->getStyle('B'.$r.':M'.$r)->getAlignment()->setWrapText(true);
                    $event->sheet->getDelegate()->getStyle('B'.$r.':M'.$r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $event->sheet->getDelegate()->getStyle('B'.$r.':M'.$r)->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $event->sheet->getDelegate()->getStyle($colorSFD1)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('fe2c00');
                    $event->sheet->getDelegate()->getStyle($colorSFD2)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('0dd4ff');
                    $l = $this->nbreBef+14;
                    $l1 = $this->nbreSfd+1;
                    $l1 = $l1+$l;
                    $event->sheet->getDelegate()->getStyle('F'.$l.':F'.$l1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->getDelegate()->getStyle('J'.$l.':J'.$l1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $event->sheet->getDelegate()->getStyle('M'.$l.':M'.$l1)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }
            }
        ];
    }

    public function title(): string
    {
        return "Dashboard";
    }
}
