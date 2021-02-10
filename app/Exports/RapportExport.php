<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RapportExport implements WithMultipleSheets
{
    /**
     * @var string
     */
    private $date;
    /**
     * @var string
     */
    private $pays;

    public function __construct(string $date,string $pays)
    {
        $this->date = $date;
        $this->pays = $pays;
    }

    public function sheets(): array
    {
        $sheets = [];
        $sheets[] = new Dashboard($this->date,$this->pays);
        $sheets[] = new MyExport($this->date,$this->pays);
        return $sheets;
    }
}
