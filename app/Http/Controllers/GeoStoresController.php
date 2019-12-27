<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeoStoresController extends Controller
{
    public function hasCoverage(Request $request, $lng, $lat)
    {
        return '{
            "has_coverage": true,
            "lng" : '.$lng.',
            "lat" : '.$lat.'
        }
        ';
    }
}                                                                                                                                                                                                                                                                                                                                                                                                                                                   