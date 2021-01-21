<?php

namespace App\Http\Controllers;

use App\Models\billing_stats;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $report = DB::select('SELECT b.subscriber_name as name, rv.RapportVide as "RapportVide", rd.RapportData as "RapportData"
                            FROM billing_stats b,
                                 (SELECT subscriber_name, COUNT(usage_name) as "RapportVide"
                                     FROM billing_stats
                                     WHERE lower(usage_name) = "rapport de crédit bic civ vide"
                                  	 AND subscriber_name LIKE "%SN"
                                 	 AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                     GROUP BY subscriber_name) rv,
                                 (SELECT subscriber_name, COUNT(usage_name) as "RapportData"
                                     FROM billing_stats
                                     WHERE lower(usage_name) = "rapport de crédit bic civ plus"
                                  	 AND subscriber_name LIKE "%SN"
                                  	 AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                     GROUP BY subscriber_name) rd
                            WHERE b.subscriber_name = rv.subscriber_name AND b.subscriber_name=rd.subscriber_name
                            AND b.subscriber_name LIKE "%SN"
                            AND b.stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                            GROUP BY b.subscriber_name, rv.RapportVide, rd.RapportData');
        return view('index', compact('report'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function show(Request $request)
    {
        try {
            $this->validate($request, ['pays' => 'required|exists:countries,id']);
            $reports = DB::select('SELECT b.subscriber_name as name, rv.RapportVide as "RapportVide", rd.RapportData as "RapportData"
                                            FROM billing_stats b,
                                                 (SELECT subscriber_name, COUNT(usage_name) as "RapportVide"
                                                     FROM billing_stats
                                                     WHERE lower(usage_name) = "rapport de crédit bic civ vide"
                                                     AND subscriber_name LIKE "%SN"
                                                     AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                                     GROUP BY subscriber_name) rv,
                                                 (SELECT subscriber_name, COUNT(usage_name) as "RapportData"
                                                     FROM billing_stats
                                                     WHERE lower(usage_name) = "rapport de crédit bic civ plus"
                                                     AND subscriber_name LIKE "%SN"
                                                     AND stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                                     GROUP BY subscriber_name) rd
                                            WHERE b.subscriber_name = rv.subscriber_name AND b.subscriber_name=rd.subscriber_name
                                            AND b.subscriber_name LIKE "%SN"
                                            AND b.stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                            GROUP BY b.subscriber_name, rv.RapportVide, rd.RapportData');
            //you can handle output in different ways, I just use a custom filled array. you may pluck data and directly output your data.
            $data = array();
            foreach( $reports as $res )
            {
                $assujeti = $res->name;
                $rv = $res->RapportVide;
                $rd = $res->RapportData;

                $data[] = array("name" => $assujeti,
                    "rv" => $rv,
                    "rd" => $rd);
            }
            return $data;
        } catch (ValidationException $e) {
        }
    }
}
