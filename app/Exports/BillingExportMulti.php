<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BillingExportMulti implements WithMultipleSheets
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
        switch ($this->mois){
            case 5:
            case 3:
            case 7:
            case 8:
            case 10:
            case 12:
            case 1:
                $sheets[] = new MoisReport($this->mois,$this->pays);
                for ($jour = 1 ; $jour<=31;$jour++){
                    $sheets[] = new BillingsExport($this->mois,$jour,$this->pays);
                }
                break;
            case 2:
                $an = date('Y');
                if ($an % 400 == 0){
                    $sheets[] = new MoisReport($this->mois,$this->pays);
                    for ($jour = 1 ; $jour<=29;$jour++){
                        $sheets[] = new BillingsExport($this->mois,$jour,$this->pays);
                    }
                }
                else{
                    $sheets[] = new MoisReport($this->mois,$this->pays);
                    for ($jour = 1 ; $jour<=28;$jour++){
                        $sheets[] = new BillingsExport($this->mois,$jour,$this->pays);
                    }
                }
                break;
            case 6:
            case 9:
            case 11:
            case 4:
                $sheets[] = new MoisReport($this->mois,$this->pays);
                for ($jour = 1 ; $jour<=30;$jour++){
                    $sheets[] = new BillingsExport($this->mois,$jour,$this->pays);
                }
                break;
        }
        return $sheets;
    }
}
