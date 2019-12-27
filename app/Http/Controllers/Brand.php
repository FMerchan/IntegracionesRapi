<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CurlHelper;


class Brand extends Controller
{
    const URL_CREATE_BRAND = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/store/brand';

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Cargo la informacion.
        $information = $request->getContent();

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = array ( 
                            'Content-Type: text/plain'
                        ) ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_BRAND, '' , $information, $headers );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Brand: ' . $resultado['mensaje'] ), 200);
        }

    	\Log::info("Crear Brand Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

}
