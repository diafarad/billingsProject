<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ReportDataController extends Controller
{
    function reports(Request $request ): array
    {
        try {
            $this->validate($request, ['id' => 'required|exists:countries,id']);
            $states = State::where('country_id', $request->get('id') )->get();
            //you can handle output in different ways, I just use a custom filled array. you may pluck data and directly output your data.
            $output = [];
            foreach( $states as $state )
            {
                $output[$state->id] = $state->name;
            }
            return $output;
        } catch (ValidationException $e) {
        }


    }
}
