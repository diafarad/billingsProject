<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RapportExport implements WithMultipleSheets
{
    /**
     * @var int
     */
    private $mois;
    /**
     * @var string
     */
    private $pays;

    public function __construct(int $mois,string $pays)
    {
        $this->mois = $mois;
        $this->pays = $pays;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new Dashboard($this->mois,$this->pays);
        $sheets[] = new MyExport($this->mois,$this->pays);
        return $sheets;
    }
}
