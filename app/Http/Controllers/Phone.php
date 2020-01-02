<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Phone extends Controller
{
    const URL_VALICACION = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/phone/validation';

    const PHONE_TYPE_VALIDATE = [ PhoneTypeEnum::OWNER, PhoneTypeEnum::NOTIFICATION, 
    								PhoneTypeEnum::MANAGER, PhoneTypeEnum::RESTAURANT, 
    								PhoneTypeEnum::FRANCHISEE, PhoneTypeEnum::FRANCHISEE_OWNER 
    							]; 

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function validar( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

        // Modifico la ubicacion de los paramtros del post.
        if( isset($information[0]) 
        	&& count( $information[0] ) > 1 
        	&& isset($information[0]['phone_number']) ) {
        	$infoAux = $information[0];
        }else{
        	$infoAux = $information;
        }

        // Verifico los parametros
        $resulVal = $this->validarParametros( $infoAux );
        if( $resulVal['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error de parametros de entrada: ' . $resulVal['mensaje'] ) , 200);
        }

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

    /**
     * Funcion que valida el tipo de telefono.
     **/
    private function validarTypoTelefono( $phoneType ){
    	$resultado = [ 'estado' => true ];

    	if( !is_int($phoneType) ) {
    		$resultado = [ 'estado' => false, 
    						'mensaje' => 'El valor de "phone_type_id" tiene un formato invalido, solo puede ser numerico ' ];
    	}

    	if( ! in_array($phoneType, self::PHONE_TYPE_VALIDATE) ){
    		$resultado = [ 'estado' => false, 
    						'mensaje' => 'El valor de "phone_type_id" tiene un formato invalido, solo puede ser numerico ' ];
    	}

    	return $resultado;
    }

	/**
	* Valida parametros de la entiad a crear.
	**/
    private function validarParametros( $parametros )
    {
    	$resultado = ['estado'=> true];

    	if( count($parametros) <= 2 ){
    		return ['estado' => false, 'mensaje' => "El json enviado esta mal formado." ];
    	}

    	if( !isset($parametros['store_id']) || $parametros['store_id'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'store_id' es invalido" ];
    	}

    	if( !isset($parametros['phone_number']) || $parametros['phone_number'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'phone_number' es invalido" ];
    	}

		if( !isset($parametros['country_code']) || $parametros['country_code'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'country_code' es invalido" ];
    	}

		if( !isset($parametros['phone_type_id']) || $parametros['phone_type_id'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'phone_type_id' es invalido" ];
    	}

    	$resulVal = $this->validarTypoTelefono( $parametros['phone_type_id'] );

    	if( $resulVal['estado'] === false ) {
            $resultado = [ 'estado' => false, 
            				'mensaje' => 'Error de parametros de entrada: ' . $resulVal['mensaje'] ];
        }

    	return $resultado;
    }
}

/**
 * Typos de numero de telefono disponible.
**/
abstract class PhoneTypeEnum
{
    const OWNER 			= 1;
    const NOTIFICATION 		= 2;
    const MANAGER 			= 3;
    const RESTAURANT 		= 4;
    const FRANCHISEE 		= 5;
    const FRANCHISEE_OWNER 	= 6;
}
