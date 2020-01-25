<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Contract extends Controller
{
	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

        $get = $request->query();
    	// Obtengo el ID.
    	if( isset($get['zohoid']) 
            && $get['zohoid'] != '' 
            && $get['zohoid'] != "null"){
    		$zohoid = $get['zohoid'];
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
                            "webhook:" .env('CONTRACT_URL_NOTIFICACION') . "?zohoid=$zohoid"
                        ) ;
        \Log::info("Contract Crear - Headers Curl: " . print_r($headers,true) );

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CONTRACT_URL_CREATE_CONTRACT'), '' , $information, $headers );
       
        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Contract: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Contract Crear - Crear Contract Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( [   'status' => true,
                                'mensaje' => $resultado['mensaje']
                            ] );
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

	// Obtengo el ID.
        if( isset($information['status']) && $information['status'] != ''  ){
            $strstatus = $information['status'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'status' es invalido" ), 400);
        }

	// Obtengo la url.
        if( isset($information['url']) && $information['url'] != ''  ){
            $strurl = $information['url'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'url' es invalido" ), 400);
        }


	
switch (trim($strstatus)) {
    case "SIGNED_BY_PARTNER":
       $url = env('CONTRACT_URL_CONFIRM_CONTRACT_PARTNER')."&contrato_estado=true&zoho_oportunidad_id=$zohoid&vUrl=$strurl";
	    break;
    case "SIGNED_BY_RAPPI":
        $url = env('CONTRACT_URL_CONFIRM_CONTRACT_RAPPI')."&contrato_estado=true&zoho_oportunidad_id=$zohoid&vUrl=$strurl";
        break;
    default:
            \Log::info("Contract updateContract - Error: no es un estado reconocido" );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al actualizar la firma del Contract'), 200);
        break;
}

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( $url );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            \Log::info("Contract updateContract - Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al actualizar el Contract: ' .$resultado['mensaje'] ), 200);
        }
        // Logueo el estado.
        \Log::info("Contract updateContract - Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    json_decode($resultado["mensaje"],true) , 
                                    [ 'status' => true ]
                                );
        // Retorno el estado del resultado.
        return json_encode($respuesta);
    }

}
