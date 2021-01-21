<?php

namespace App\Console\Commands;

use App\Exports\BillingExportMulti;
use App\Exports\DetailsInstitution;
use App\Exports\DetailsMultiInstitution;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class TestCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'req:test {mois} {pays}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $from = date('2020-'.$mois.'-01');
        $to = date('2020-'.$mois.'-31');
        $this->info('De : '.$from.', A : '.$to.', Pays : '.$pays);
        $today = gmdate('d-m-Y-H-i');

        if (Excel::store(new DetailsMultiInstitution($mois,$pays),  'test_export_'.$pays.''.$mois.'_'.$today.'.xlsx')){
            $this->info('Export successfuly');
        }
        else{
            $this->error('Error! Something went wrong');
        }
    }
}
