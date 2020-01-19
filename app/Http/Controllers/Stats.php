<?php

namespace App\Http\Controllers;

use CurlHelper;
use ValidadorHelper;
use Illuminate\Http\Request;

class Stats extends Controller
{

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function store( Request $request )
    {

 		// Cargo la informacion.
        	$information = $request->input();

     		http_response_code(200);
     		\Log::info("Stats Store - Informacion a guardar: " . print_r($information,true) );
            	return \Response::json(array( 'status' => true), 200);
    }
	
}
