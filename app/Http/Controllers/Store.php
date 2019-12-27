<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Store extends Controller
{
    const URL_CREATE_STORE = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/store';

    const URL_CONFIRM_STORE = 'https://www.zohoapis.com/crm/v2/functions/zint_221_menu_scrapper_response/actions/execute?auth_type=apikey&zapikey=1003.7c02a5b10f13810ff9499d39a02c0d43.605b9c5964f99646e4da05c6b3e3afdc';

    const URL_NOTIFICACION = 'http://ec2-18-220-204-101.us-east-2.compute.amazonaws.com/rappidev/public/api/v1/store/menu/scraped';

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

    	// Valido los parametros.
  		$estado = $this->validarParametros( $information );
  		if( $estado['estado'] === false ) {
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => $estado['mensaje'] ), 400);
        }

        // Armo la json.
		$information = json_encode($information);
    	\Log::info("Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = array ( 
                            'Content-Type: application/json',
                            "Cookie: location=" .self::URL_NOTIFICACION . "?zohoid=$zohoid"
                        ) ;
        \Log::info("Headers Curl: " . print_r($headers,true) );
		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREATE_STORE, '' , $information , $headers );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del store: ' .$resultado['mensaje'] ), 200);
        }

        // Verifico la respuesta de Rappi.
        $manage = json_decode($resultado["mensaje"], true);
        if ( $manage[0]["result"] === false ){
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la creacion del store: ' .$manage[0]["message"] ), 200);
        }

        // Logueo el estado.
    	\Log::info("Crear Store Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function updateStore( Request $request )
    {
    	\Log::info("Store Update Params: " . print_r($_GET,true) );

        // Obtengo el ID.
        if( isset($_GET['zohoid']) && $_GET['zohoid'] != ''  ){
            $zohoid = $_GET['zohoid'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'zohoid' es invalido" ), 400);
        }

        // Seteo el post.
        $information = [ 'zoho_store_id' => $zohoid,
                         'scraper_state' => 'Scraped' ];
        $information = json_encode($information);

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CONFIRM_STORE, '' , $information , '' );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar al actualizar el store: ' .$resultado['mensaje'] ), 200);
        }
        // Logueo el estado.
        \Log::info("Crear Store Curl Response: " . print_r($resultado,true) );

    	// Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

    /**
     * Valida parametros de la entiad a crear.
     **/
    private function validarParametros( $mensaje )
    {
    	$resultado = ['estado'=> true];

    	if( count($mensaje) <= 2 ){
    		return ['estado' => false, 'mensaje' => "El json enviado esta mal formado." ];
    	}

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

