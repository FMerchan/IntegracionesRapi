<?php

namespace App\Http\Controllers;

use CurlHelper;
use ValidadorHelper;
use Illuminate\Http\Request;

class Store extends Controller
{
	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crear( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();
        \Log::info("Store crear - Informacion a enviar: " . print_r($information,true) );

        $information["brand_id"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $information["brand_id"] );

        $get = $request->query();

    	// Obtengo el ID.
    	if( isset($get['zohoid']) && $get['zohoid'] != ''  ){
    		$zohoid = $get['zohoid'];
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

        // Armo los headers
        $headers = array ( 
                            'Content-Type: application/json',
                            "webhook:" .env('STORE_URL_NOTIFICACION') . "?zohoid=$zohoid"
                        ) ;
        \Log::info("Store crear -  Headers Curl: " . print_r($headers,true) );

        // Verifico la informacion.
        if( isset( $information['menu'] ) and is_array($information['menu']) ) {
            	
  		// Agrego URL de confirmacion.
        	$information['menu']["webhook"] = env('STORE_URL_NOTIFICACION') . "?zohoid=$zohoid";
     
        }
         
        // Encodeo la informacion.
        $information = json_encode($information);
        \Log::info(" Store crear - Informacion a enviar: " . print_r($information,true) );

		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('STORE_URL_CREATE_STORE'), '' , $information , $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
             \Log::info(" Store crear -  Error estado: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del store: ' . json_encode($resultado['mensaje']) ), 200);
        }

        // Verifico la respuesta de Rappi.
        $manage = $resultado["mensaje"];

        //if ( $manage["phase_results"][0]["result"] === false ){
        //     \Log::info(" Store crear - Error Mensaje: " . print_r($resultado["mensaje"],true) );
         //   return \Response::json(array( 'status' => false, 
         //               'mensaje' => 'Error al solicitar la creacion del store: ' .$manage["phase_results"][0]["message"] ), 200);
       // }

        // Logueo el estado.
    	\Log::info("Store crear - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
       	$arr = $resultado['mensaje'];
		$arr['status'] =  true;
        // Agergo la nomesclatura del pais al ID.
        if( env('APP_NOMESCLATURA_PAIS') && isset($arr["store_id"]) ){
            $arr["store_id"] = env('APP_NOMESCLATURA_PAIS') . $arr["store_id"];
        }
		return json_encode($arr);
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function updateStore( Request $request )
    {    	
        // Cargo la informacion.
        $info = $request->input();

	   \Log::info("Store Update - Informacion a enviar: " . print_r($info,true) );

	   \Log::info("Store Update - Params: " . print_r($_GET,true) );

        // Obtengo el ID.
        if( isset($info['zohoid']) && $info['zohoid'] != ''  ){
            $zohoid = $info['zohoid'];
        }else{ // En caso de error lo logueo.
            http_response_code(400);
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'zohoid' es invalido" ), 400);
        }

        // Seteo el post.
        $information = [ 'zoho_store_id' => $zohoid,
                         'scraper_state' => 'Scraped' ];
        $information = json_encode($information);

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('STORE_URL_CONFIRM_STORE'), '' , $information , '' );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar al actualizar el store: ' .$resultado['mensaje'] ), 200);
        }
        // Logueo el estado.
        \Log::info("Store Update - Curl Response: " . print_r($resultado,true) );

    	// Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

    /**
     * Funcion que devuelve los tipos de modos de telefono.
     **/
    public function getTelefonos(Request $request)
    {
        // Armo la json.
        $parametros = $request->input();
        // Validaciones.
        $val = ValidadorHelper::existencia( ['storeid'] , $parametros , true );
        if( $val['resultado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error en la recepción de parametros, no se recibio el paremtro:' . $val['parametro']  ), 200);
        }

        // Realizo el Curl con el envio.
        $url = str_replace('[STOREID]', $parametros['storeid'], env('STORE_URL_TELEFONOS')) ;
        $resultado = CurlHelper::curl( $url );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Store getTelefonos -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la actualizacion del listado: ' . $resultado['mensaje'] ), 200);
        }

        // Logueo el estado.
        \Log::info("Store getTelefonos - Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'phones' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
    }


    /**
    * @param  Request  $request
    * @return Response
    **/
    public function agregarEmail( Request $request )
    {
        // Armo la json.
        $information = json_encode($request->post());
        \Log::info("Store agregarEmail - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('STORE_URL_AGREGAR_MAIL'), '' , $information, $headers );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
             \Log::info(" Store agregarEmail -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar agregar el mail al store: ' . $resultado['mensaje'] ), 200);
        }

        \Log::info("Store agregarEmail - Curl Response: " . print_r($resultado,true) );
        
        $respuesta = [ 'status' => true ];
        if( $resultado['mensaje'] !== '' ) {
            $respuesta = array_merge( $respuesta, json_decode($resultado['mensaje'], true) );
        }

        return json_encode( $respuesta );
    }

    /**
    * @param  Request  $request
    * @return Response
    **/
    public function borrarEmail( Request $request )
    {
        // Armo la json.
        $parametros = $request->input();
        // Validaciones.
        $val = ValidadorHelper::existencia( ['store_id','email','email_type_id'] , $parametros , true );
        if( $val['resultado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error en la recepción de parametros, no se recibio el parametro:' . $val['parametro']  ), 200);
        }
        // Obtengo los parametros.
        $params = ValidadorHelper::prepararParametros( ['store_id','email','email_type_id'] , $parametros );
        // Realizo el Curl con el envio.
        $url = env('STORE_URL_BORRAR_MAIL') . '?' . $params["serializado"];

        $resultado = CurlHelper::curl( $url , '' , '' , '' , true);

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
             \Log::info(" Store borrarEmail -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar borrar el email al store: ' . $resultado['mensaje'] ), 200);
        }

        \Log::info("Store borrarEmail - Curl Response: " . print_r($resultado,true) );
        
        $respuesta = [ 'status' => true ];
        if( $resultado['mensaje'] !== '' ) {
            $respuesta = array_merge( $respuesta, json_decode($resultado['mensaje'], true) );
        }

        return json_encode( $respuesta );
    }


    /**
     * Funcion que devuelve los tipos de modos de telefono.
     **/
    public function getEmails(Request $request)
    {
        // Armo la json.
        $parametros = $request->input();
        // Validaciones.
        $val = ValidadorHelper::existencia( ['storeid'] , $parametros , true );
        if( $val['resultado'] === false ) {
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error en la recepción de parametros, no se recibio el paremtro:' . $val['parametro']  ), 200);
        }

        // Realizo el Curl con el envio.
        $url = str_replace('[STOREID]', $parametros['storeid'], env('STORE_URL_EMAILS')) ;
        $resultado = CurlHelper::curl( $url );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Store getEmails -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la actualizacion del listado: ' . $resultado['mensaje'] ), 200);
        }

        // Logueo el estado.
        \Log::info("Store getEmails - Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'emails' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
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
    		|| array_diff($mensaje['delivery_methods'], ['delivery','marketplace','pickup','full service']) ){
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
