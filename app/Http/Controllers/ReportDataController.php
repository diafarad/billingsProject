<?php

namespace App\Http\Controllers;

use App\Exports\MyExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportDataController extends Controller
{
    function importExportView (){
        $institutions = DB::select('SELECT DISTINCT subscriber_name as name
                                            FROM billing_stats
                                            where subscriber_name like "%CI"
                                            ORDER BY subscriber_name ASC');
        $lesDates = DB::select('SELECT DISTINCT stats_date as d
                                        FROM billing_stats
                                        WHERE stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                        ORDER BY stats_date ASC');
        $from = date('2020-12-01');
        $to = date('2020-12-31');

        $nbBEF = DB::select('SELECT COUNT(DISTINCT b.subscriber_name) as n
                             FROM billing_stats b, subscribers s
                             WHERE b.subscriber_name = s.name
                             AND lower(s.sector) = "banque"
                             AND b.subscriber_name like "%CI"');

        $nbSFD = DB::select('SELECT COUNT(b.subscriber_name) as n
                             FROM billing_stats b, subscribers s
                             WHERE b.subscriber_name = s.name
                             AND lower(s.sector) = "autre sfd"
                             AND b.subscriber_name like "%CI"');


        return view('myexport', [
            'institutions' => $institutions,
            'lesdates' => $lesDates,
            'from' => $from,
            'to' => $to,
            'BEF' => $nbBEF,
            'SFD' => $nbSFD,
        ]);
    }

    function export (){
        return Excel::download(new MyExport(12,"GW"), 'Details.xlsx');
    }

}
