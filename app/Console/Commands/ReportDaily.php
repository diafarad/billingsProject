<?php

namespace App\Console\Commands;

use App\Exports\Dashboard;
use App\Exports\MyExport;
use App\Exports\RapportExport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ReportDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:daily {mois : two digits like 01, 07, 11...} {pays : example : SN, BF, CI, ML....}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command allows to generate a report for billing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $mois = $this->argument('mois');
        $pays = $this->argument('pays');

        $m= (int)$mois;
        if ($m <= 0 or $m >12)
        {
            $this->error('Erreur! Le mois saisi est incorrect');
            return 0;
        }
        $pays = strtoupper($pays);
        if ($pays != 'SN' and $pays != 'BF' and $pays != 'BJ' and $pays != 'ML' and $pays != 'TG' and $pays != 'NE' and $pays != 'GW' and $pays != 'CI')
        {
            $this->error('Erreur! Le pays saisi est incorrect');
            return 0;
        }

        $this->info('Mois : '.$mois. ', Pays : '. $pays);
        $jourMax = 1;
        switch ($mois){
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
        $from = date('2020-'.$mois.'-01');
        $to = date('2020-'.$mois.'-'.$jourMax);
        $this->info('De : '.$from.', A : '.$to.', Pays : '.$pays);
        $today = gmdate('d-m-Y-H-i');

        if (Excel::store(new RapportExport($mois,$pays), 'Rapport_'.$today.'_'.$pays.'_'.$mois.'.xlsx') /*Excel::store(new BillingExportMulti($mois,$pays),  'rapport_export_'.$pays.''.$mois.'_'.$today.'.xlsx') AND Excel::store(new DetailsMultiInstitution($mois,$pays),  'Details_Institutions_'.$pays.''.$mois.'_'.$today.'.xlsx')*/){
            $this->info('Export successfully');
        }
        else{
            $this->error('Error! Something went wrong');
        }
    }
}
