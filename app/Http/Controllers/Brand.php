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
    	\Log::info("Brand Crear - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = array ( 
                            'Content-Type: text/plain'
                        ) ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_BRAND, '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Store crear -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Brand: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Brand Crear - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        $arr = $resultado['mensaje'];
		$arr['status'] =  true;
        // Agergo la nomesclatura del pais al ID.
        if( env('APP_NOMESCLATURA_PAIS') && isset($arr["id"]) ){
            $arr["id"] = env('APP_NOMESCLATURA_PAIS') . $arr["id"];
        }
		return json_encode($arr);
    }

}
