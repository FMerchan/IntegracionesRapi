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
    	// Obtengo el ID.
    	if( isset($_GET['sohoid']) && $_GET['sohoid'] != ''  ){
    		$sohoid = $_GET['sohoid'];
    	}else{ // En caso de error lo logueo.
    		http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'sohoid' es invalido" ), 400);
    	}
    	// Cargo la informacion.
    	$information = $request->input();

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = "";//$headers = [ 'location' => self::URL_NOTIFICACION . "?sohoid=$sohoid" ] ;

        \Log::info("Headers Curl: " . print_r($headers,true) );

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_CONTRACT, '' , $information, $headers );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
            http_response_code(400);
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del Contract: ' . $resultado['mensaje'] ), 400);
        }

    	\Log::info("Crear Contract Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }


	/**
	* @param  Request  $request
	* @return Response
	**/
    public function updateContract( Request $request )
    {
    	\Log::info("Contract Update Params: " . print_r($_GET,true) );

        // Obtengo el ID.
        if( isset($_GET['sohoid']) && $_GET['sohoid'] != ''  ){
            $sohoid = $_GET['sohoid'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'sohoid' es invalido" ), 400);
        }

        // Seteo el post.
        $information = [ 'zoho_oportunidad_id' => $sohoid,
                         'contrato_estado' => 'true' ];        

	$information = json_encode($information);

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CONFIRM_CONTRACT, '' , $information , '' );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            http_response_code(400);
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la creacion del Contract: ' .$resultado['mensaje'] ), 400);
        }
        // Logueo el estado.
        \Log::info("Crear Contract Curl Response: " . print_r($resultado,true) );

        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }


	/**
     * Valida parametros de la entiad a crear.
     **/
    private function validarParametros( $mensaje )
    {
    	$resultado = ['estado'=> true];
    	if( !isset($mensaje['name']) || $mensaje['name'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'name' es invalido" ];
    	}

    	if( isset($mensaje['rappi_id']) && !is_int( $mensaje['rappi_id'] ) ){
    		return ['estado' => false, 'mensaje' => "El parametro 'rappi_id' es invalido" ];
    	}

    	if( !isset($mensaje['place_information']) || $mensaje['place_information'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'place_information' es invalido" ];
    	}

    	if( !isset($mensaje['place_information']['latitude']) 
    		|| $mensaje['place_information']['latitude'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'latitude' es invalido" ];
    	}

    	if( !isset($mensaje['place_information']['longitude']) 
    		|| $mensaje['place_information']['longitude'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'longitude' es invalido" ];
    	}

    	if( !isset($mensaje['place_information']['city']) 
    		|| $mensaje['place_information']['city'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'city' es invalido" ];
    	}

		if( !isset($mensaje['place_information']['city_address_id']) 
    		|| $mensaje['place_information']['city_address_id'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'city_address_id' es invalido" ];
    	}

		if( !isset($mensaje['place_information']['address']) 
    		|| $mensaje['place_information']['address'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'address' es invalido" ];
    	}

		if( !isset($mensaje['place_information']['type']) 
    		|| $mensaje['place_information']['type'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'type' es invalido" ];
    	}


		if( !isset($mensaje['place_information']['google_place_id']) 
    		|| $mensaje['place_information']['google_place_id'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'google_place_id' es invalido" ];
    	}

		if( !isset($mensaje['place_information']['google_place_id']) 
    		|| $mensaje['place_information']['google_place_id'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'google_place_id' es invalido" ];
    	}

		if( !isset($mensaje['legal_relationship']) 
    		|| $mensaje['legal_relationship'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'legal_relationship' es invalido" ];
    	}

		if( !isset($mensaje['legal_relationship']['legal_agent']) 
    		|| $mensaje['legal_relationship']['legal_agent'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'legal_agent' es invalido" ];
    	}

		if( !isset($mensaje['legal_relationship']['legal_agent']['name']) 
    		|| $mensaje['legal_relationship']['legal_agent']['name'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'legal_agent.name' es invalido" ];
    	}

    	if( !isset($mensaje['contact']) 
    		|| $mensaje['contact'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'contact' es invalido" ];
    	}

    	if( !isset($mensaje['contact']['email']) 
    		|| $mensaje['contact']['email'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'email' es invalido" ];
    	}

		if( !isset($mensaje['contact']['telephone']) 
    		|| $mensaje['contact']['telephone'] == ''  ){
    		return ['estado' => false, 'mensaje' => "El parametro 'telephone' es invalido" ];
    	}

    	if( !isset($mensaje['delivery_methods']) 
    		|| $mensaje['delivery_methods'] == '' 
    		|| array_diff($mensaje['delivery_methods'], ['delivery','marketplace','pickup']) ){
    		return ['estado' => false, 'mensaje' => "El parametro 'delivery_methods' es invalido" ];
    	}


    	if( !isset($mensaje['category']) 
    		|| $mensaje['category'] == '' 
    		|| !is_int( $mensaje['category'] ) ){
    		return ['estado' => false, 'mensaje' => "El parametro 'category' es invalido" ];
    	}

    	if( !isset($mensaje['category']) 
    		|| $mensaje['category'] == '' ) {
    		return ['estado' => false, 'mensaje' => "El parametro 'category' es invalido" ];
    	}

		if( !isset($mensaje['delivery_conditions']) 
    		|| $mensaje['delivery_conditions'] == '' ) {
    		return ['estado' => false, 'mensaje' => "El parametro 'delivery_conditions' es invalido" ];
    	}

    	if( !isset($mensaje['delivery_conditions']['has_no_pay']) 
    		|| $mensaje['delivery_conditions']['has_no_pay'] == '' ) {
    		return ['estado' => false, 'mensaje' => "El parametro 'has_no_pay' es invalido" ];
    	}

    	return $resultado;
    }
}

