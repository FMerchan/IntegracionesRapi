<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CurlHelper;

class Usuario extends Controller
{
	const URL_CREATE_USUARIO = 'https://microservices.dev.rappi.com/api/rs-onboarding-support/users';

	const URL_ASOCIAR_USUARIO = 'https://microservices.dev.rappi.com/api/rs-onboarding-support/users/associate';


	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Cargo la informacion.
        $information = $request->post();

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Usuario Crear - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_USUARIO, '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Store crear -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Usuario: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Usuario Crear - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( [   'status' => true,
                                'mensaje' => json_encode( $resultado['mensaje'] )
                            ] );
    }


	/**
	* @param  Request  $request
	* @return Response
	**/
    public function asociar( Request $request )
    {
        // Cargo la informacion.
        $information = $request->post();

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Usuario asociar - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_ASOCIAR_USUARIO, '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Store crear -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Usuario: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Usuario asociar - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( [   'status' => true,
                                'mensaje' => json_encode( $resultado['mensaje'] )
                            ] );
    }

}
