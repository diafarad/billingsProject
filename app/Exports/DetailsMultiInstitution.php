<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DetailsMultiInstitution implements WithMultipleSheets
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

    public function sheets(): array
    {
        $q = DB::select('SELECT DISTINCT(subscriber_name) as inst
                               FROM billing_stats
                               WHERE subscriber_name LIKE "%'.$this->pays.'"');
        $sheets = [];
        foreach ($q as $res) {
            $sheets[] = new DetailsInstitution($this->mois,$res->inst);
        }
        return $sheets;
    }
}
