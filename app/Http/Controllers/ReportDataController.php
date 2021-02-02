<?php

namespace App\Http\Controllers;

use App\Exports\MyExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ReportDataController extends Controller
{
    function importExportView (){
        $intitutions = DB::select('SELECT DISTINCT subscriber_name as name
                                            FROM billing_stats
                                            where subscriber_name like "%SN"
                                            ORDER BY subscriber_name ASC');
        $lesDates = DB::select('SELECT DISTINCT stats_date as d
                                        FROM billing_stats
                                        WHERE stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                        ORDER BY stats_date ASC');
        return view('myexport', [
            'institutions' => $intitutions,
            'lesdates' => $lesDates
        ]);
    }

    function export (){
        return Excel::download(new MyExport, 'Details.xlsx');
    }

}
