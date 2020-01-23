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
		$information = json_encode($request->post());
    	\Log::info("Partner crear - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('PARTNER_URL_CREAR_PARNER'), '' , $information, $headers );

        // Verifico el resultado.
		if( $resultado['estado'] === false ) {
             \Log::info(" Partner crear -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

		\Log::info("Partner crear - Curl Response: " . print_r($resultado,true) );

		$result = json_decode($resultado['mensaje'], true);
		$result['status'] =  true;		

		return json_encode( $result );
    }
}
