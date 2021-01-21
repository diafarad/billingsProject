<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class BillingsExport implements
        FromCollection,
        WithHeadings,
        ShouldAutoSize,
        WithEvents,
        WithTitle
{
    private $mois;
    private $jour;
    private $pays;

    /**
     * BillingsExport constructor.
     * @param $mois
     * @param $jour
     * @param $pays
     */
    public function __construct($mois,$jour,$pays)
    {
        $this->jour = $jour;
        $this->mois = $mois;
        $this->pays = $pays;
    }


    /**
    * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        /*$from = date('2020-'.$this->mois.'-01');
        $to = date('2020-'.$this->mois.'-31');*/

        $date = date('2020-'.$this->mois.'-'.$this->jour);

        $result = DB::select('SELECT b.subscriber_name as name, rv.RapportVide as "RapportVide", rd.RapportData as "RapportData", rv.RapportVide+rd.RapportData as "Total"
                                        FROM billing_stats b,
                                         (SELECT subscriber_name, COUNT(usage_name) as "RapportVide"
                                             FROM billing_stats
                                             WHERE lower(usage_name) = "rapport de crédit bic civ vide"
                                             AND subscriber_name LIKE "%'.$this->pays.'"
                                             AND stats_date = "'.$date.'"
                                             GROUP BY subscriber_name) rv,
                                         (SELECT subscriber_name, COUNT(usage_name) as "RapportData"
                                             FROM billing_stats
                                             WHERE lower(usage_name) = "rapport de crédit bic civ plus"
                                             AND subscriber_name LIKE "%'.$this->pays.'"
                                             AND stats_date = "'.$date.'"
                                             GROUP BY subscriber_name) rd
                                        WHERE b.subscriber_name = rv.subscriber_name AND b.subscriber_name=rd.subscriber_name
                                        AND b.subscriber_name LIKE "%'.$this->pays.'"
                                        AND b.stats_date = "'.$date.'"
                                        GROUP BY b.subscriber_name, rv.RapportVide, rd.RapportData');

        $data= array();
        foreach ($result as $res) {
            $assujeti = $res->name;
            $rv = $res->RapportVide;
            $rd = $res->RapportData;
            $total = $res->Total;

            $data[] = array("name" => $assujeti,
                            "rv" => $rv,
                            "rd" => $rd,
                            "total" => $total);
        }

        return collect($data);
    }

    public function headings(): array
    {
        return [
            'INSTITUTIONS',
            'Rapport vide',
            'Rapport avec données',
            'TOTAL',
        ];
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

    public function title(): string
    {
        return 'Jour '.$this->jour;
    }
}
