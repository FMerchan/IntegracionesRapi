<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Phone extends Controller
{
    const URL_VALICACION = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/phone/validation';

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function validar( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Phone validar - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = array ( 'Content-Type: application/json' ) ;
        \Log::info("Phone validar - Headers Curl: " . print_r($headers,true) );
		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_VALICACION, '' , $information , $headers );

        // Verifico el resultado.
		$resultado["mensaje"] = json_decode($resultado["mensaje"], true);
        if( $resultado['estado'] === false ) {
             \Log::info(" Phone validar -  Error estado: " . print_r($resultado["mensaje"],true) );
        	$aux = $resultado["mensaje"];
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al validar el numero telefonico: ' . json_encode( $aux["validation_results"][0]["detail"] ) ), 200);
        }

        // Verifico que exista la variable.
        if( isset($resultado["mensaje"]) ){
             \Log::info(" Phone validar -  Error estadoo: " . print_r($resultado["mensaje"],true) );
        	$aux = $resultado["mensaje"];
	        // Verifico la respuesta de Rappi.
	        if ( $aux["status"] >= 400 && $aux["status"] <= 500 ){
	            return \Response::json(array( 'status' => false, 
	                        'mensaje' => 'Error al validar el numero telefonicoo: ' . json_encode( $aux["validation_results"][0]["detail"] )), 200);
	        }
        }

        // Logueo el estado.
    	\Log::info("Crear Store Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }
}
