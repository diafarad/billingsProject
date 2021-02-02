<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class MyExport implements
    FromView,
    WithEvents,
    WithTitle
{

    public function view(): View
    {
        $intitutions = DB::select('SELECT DISTINCT subscriber_name as name
                                            FROM billing_stats
                                            WHERE subscriber_name LIKE "%SN"
                                            ORDER BY subscriber_name ASC');
        $lesDates = DB::select('SELECT DISTINCT stats_date as d
                                        FROM billing_stats
                                        WHERE stats_date BETWEEN "2020-12-01" AND "2020-12-31"
                                        ORDER BY stats_date ASC');

        return view('myexport', [
            'institutions' => $intitutions,
            'lesdates' => $lesDates,
        ]);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event){
                $event->sheet->getDelegate()->getStyle('A1:DR1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $event->sheet->getDelegate()->getDefaultColumnDimension()->setWidth(12);
                $event->sheet->getStyle("A1:DR1")->applyFromArray([
                    'text-align' => [
                        'center' => 'true'
                    ]
                ]);
                $event->sheet->getStyle('A1:DR1')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
                $event->sheet->getStyle('A2:DR2')->applyFromArray([
                    'font' => [
                        'bold' => 'true'
                    ]
                ]);
            }
        ];
    }

    public function title(): string
    {
        return "Details";
    }
}
