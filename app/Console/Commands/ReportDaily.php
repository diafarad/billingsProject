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
    protected $signature = 'report:daily {date : Enter the date in the format DD/MM/YYYY Example : 31/12/2020 or 05/10/2020 } {pays : Example : SN or BF or CI or ML or TG or GW or BJ or NE}';

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
        $date = $this->argument('date');
        $pays = $this->argument('pays');

        $pays = strtoupper($pays);
        if ($pays != 'SN' and $pays != 'BF' and $pays != 'BJ' and $pays != 'ML' and $pays != 'TG' and $pays != 'NE' and $pays != 'GW' and $pays != 'CI')
        {
            $this->error('Erreur! Le pays saisi est incorrect');
            return 0;
        }

        $this->info('Jour : '.$date. ', Pays : '. $pays);
        $d = explode('/',$date);
        $jour = $d[0];
        $mois = $d[1];
        $annee = $d[2];
        $heure = gmdate('H-i');
        $today = $jour.'-'.$mois.'-'.$annee;

        if (Excel::store(new RapportExport($date,$pays), 'Rapport_'.$pays.'_'.$today.'_'.$heure.'.xlsx') /*Excel::store(new BillingExportMulti($mois,$pays),  'rapport_export_'.$pays.''.$mois.'_'.$today.'.xlsx') AND Excel::store(new DetailsMultiInstitution($mois,$pays),  'Details_Institutions_'.$pays.''.$mois.'_'.$today.'.xlsx')*/){
            $this->info('Export successfully');
        }
        else{
            $this->error('Error! Something went wrong');
        }
    }
}
