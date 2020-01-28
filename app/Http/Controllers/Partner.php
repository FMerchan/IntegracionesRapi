<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CurlHelper;

class Partner extends Controller
{
	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Armo la json.
	$information = $request->post();
    	\Log::info("Partner crear - Informacion a enviar: " . print_r($information,true) );

	$store = [];
	$storearray = [];
	foreach ($information["stores"] as $valor){
    		$store["id"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $valor["id"] );
		$store["name"] = $valor["name"];
	
		$storearray[] = $store;
	}

	$information["stores"] = $storearray;

	$information = json_encode($information);

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('PARTNER_URL_CREAR_PARNER'), '' , $information, $headers );

        // Verifico el resultado.
		if( $resultado['estado'] === false ) {
             \Log::info(" Partner crear -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del partner: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

		\Log::info("Partner crear - Curl Response: " . print_r($resultado,true) );

	
		$result = json_decode($resultado['mensaje'], true);


 	$store2 = [];
	$storearray2 = [];
	foreach ($result["stores"] as $valor){
    		$store2["id"] =  env('APP_NOMESCLATURA_PAIS') . $valor["id"] ;
		$store2["restaurant_name"] = $valor["restaurant_name"];
		$store2["loginaliados"] = $valor["loginaliados"];
		$store2["passwordaliados"] = $valor["passwordaliados"];
	
		$storearray2[] = $store2;
	}

		$result["stores"] = $storearray2;

		$result['status'] =  true;		

		return json_encode( $result );
    }
}
