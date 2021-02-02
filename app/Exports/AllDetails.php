<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class AllDetails implements
    FromCollection,
    WithHeadings,
    ShouldAutoSize,
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

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $q = DB::select('SELECT DISTINCT(subscriber_name) as inst
                               FROM billing_stats
                               WHERE subscriber_name LIKE "%'.$this->pays.'"');
        $sheets = array();
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
        $rv = 'rapport de crédit bic civ vide';
        $rd = 'rapport de crédit bic civ plus';
        foreach ($q as $res) {
            $r = DB::select('SELECT b.stats_date as dateSt, rv.n as "RapportVide", rd.n as "RapportData", COUNT(DISTINCT(b.user_name)) as "NbUser", b.subscriber_name
                                    FROM billing_stats b, (SELECT stats_date, COUNT(usage_name) as "n"
                                                           FROM billing_stats
                                                           WHERE lower(usage_name) = "'.$rv.'" AND subscriber_name = "'.$res->inst.'"
                                                           AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                           GROUP BY stats_date) rv,
                                                          (SELECT stats_date, COUNT(usage_name) as "n"
                                                           FROM billing_stats
                                                           WHERE lower(usage_name) = "'.$rd.'" AND subscriber_name = "'.$res->inst.'"
                                                           AND stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                                           GROUP BY stats_date) rd
                                    WHERE b.stats_date = rv.stats_date AND b.stats_date=rd.stats_date
                                    AND b.subscriber_name = "'.$res->inst.'"
                                    AND b.stats_date BETWEEN "'.$from.'" AND "'.$to.'"
                                    GROUP BY b.stats_date, rv.n, rd.n, b.subscriber_name');
            $sheets[] = $r;
        }
        return collect($sheets)->collapse();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getStyle('A1:Z1')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
            }
        ];
    }

    public function headings(): array
    {
        return [
            ['OKAY'],
            ['YES', 'GOOD'],
            ['Date',
            'Rapport vide',
            'Rapport avec données',
            'Nbre User',
            'Institution',]
        ];
    }

    public function title(): string
    {
        return 'Details';
    }
}
