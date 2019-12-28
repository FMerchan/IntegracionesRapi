<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Contract extends Controller
{
	// Constaten 
	const URL_CREATE_CONTRACT = 'https://microservices.dev.rappi.com/api/rs-onboarding-support/terms/send-contract';

    const URL_CONFIRM_CONTRACT = 'https://www.zohoapis.com/crm/v2/functions/zint_110_contract_creation_response/actions/execute?auth_type=apikey&zapikey=1003.7c02a5b10f13810ff9499d39a02c0d43.605b9c5964f99646e4da05c6b3e3afdc';

    const URL_NOTIFICACION = 'http://ec2-18-220-204-101.us-east-2.compute.amazonaws.com/rappidev/public/api/v1/contract/signed';

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();
    	// Obtengo el ID.
    	if( isset($information['zohoid']) && $information['zohoid'] != ''  ){
    		$zohoid = $information['zohoid'];
    	}else{ // En caso de error lo logueo.
    		http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'zohoid' es invalido" ), 400);
    	}

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Contract Crear - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = array ( 
                            'Content-Type: application/json',
                            "Cookie: location=" .self::URL_NOTIFICACION . "?zohoid=$zohoid"
                        ) ;
        \Log::info("Contract Crear - Headers Curl: " . print_r($headers,true) );

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_CONTRACT, '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"], true);
        
        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Contract: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

        // Verifico la respuesta de Rappi.
        $manage = $resultado["mensaje"];
        if ( $manage[0]["result"] === false ){
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la creacion del Contract: ' .$manage[0]["message"] ), 200);
        }

    	\Log::info("Contract Crear - Crear Contract Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }


	/**
	* @param  Request  $request
	* @return Response
	**/
    public function updateContract( Request $request )
    {
                // Cargo la informacion.
        $information = $request->input();

    	\Log::info("Contract updateContract - Parametros recividos: " . print_r($information,true) );

        // Obtengo el ID.
        if( isset($information['zohoid']) && $information['zohoid'] != ''  ){
            $zohoid = $information['zohoid'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'zohoid' es invalido" ), 400);
        }

        // Seteo el post.
        $information = [ 'zoho_oportunidad_id' => $zohoid,
                         'contrato_estado' => 'true' ];
        $information = json_encode($information);

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CONFIRM_CONTRACT, '' , $information , '' );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la creacion del Contract: ' .$resultado['mensaje'] ), 200);
        }
        // Logueo el estado.
        \Log::info("Contract updateContract - Curl Response: " . print_r($resultado,true) );

        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

}
