<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CurlHelper;

class Payment extends Controller
{
	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crearNegocio( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

        // Armo la json.
	$information = json_encode($request->post());
    	\Log::info("Payment crearNegocio - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('PAYMENT_URL_CREAR_NEGOCIO'), '' , $information, $headers );
      
        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Payment crearNegocio -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Payment crearNegocio - Curl Response: " . print_r($resultado,true) );

	$arr = json_decode($resultado['mensaje'], true);
	$arr['status'] =  true;		
		
        // Agergo la nomesclatura del pais al ID.
        if( env('APP_NOMESCLATURA_PAIS') && isset($arr["id"]) ){
            $arr["id"] = env('APP_NOMESCLATURA_PAIS') . $arr["id"];
        }

		return json_encode($arr);
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crearCuentaBancaria( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

	$information["businessid"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $information["businessid"] );

        // Obtengo el ID.
    	if( isset($information['businessid']) 
    		&& $information['businessid'] != '' 
    		&& intval($information['businessid']) !== 0 ) {
    		$businessid = $information['businessid'];
    	}else{ // En caso de error lo logueo.
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'businessid' es invalido" ), 400);
    	}

        // Armo la json.
		$information = json_encode($request->post());
    	\Log::info("Payment crearCuentaBancaria - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = str_replace('[BUSSINES-ID]', $businessid, env('PAYMENT_URL_CREAR_CUENTA_BANCARIA'));

     	$resultado = CurlHelper::curl( $url , '' , $information, $headers );
        
        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Payment crearCuentaBancaria -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Payment crearCuentaBancaria - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        $arr = json_decode($resultado['mensaje'], true);
		$arr['status'] =  true;		
        // Agergo la nomesclatura del pais al ID.
        if( env('APP_NOMESCLATURA_PAIS') && isset($arr["payment_reference_id"]) ){
            $arr["payment_reference_id"] = env('APP_NOMESCLATURA_PAIS') . $arr["payment_reference_id"];
        }
		return json_encode($arr);
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function asociarTienda( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

	$information["accountid"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $information["accountid"] );
	$information["store_id"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $information["store_id"] ); //el primer store

    	// Obtengo el ID.
    	if( isset($information['accountid']) 
    		&& $information['accountid'] != '' 
    		&& intval($information['accountid']) !== 0 ) {
    		$accountid = $information['accountid'];
    	}else{ // En caso de error lo logueo.
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'accountid' es invalido" ), 400);
    	}

        // Armo la json.
	$information = "[".json_encode($information)."]";
    	\Log::info("Payment asociarTienda - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = str_replace('[ACCOUNT-ID]', $accountid, env('PAYMENT_URL_ASOCIAR_CUENTA'));

        $resultado = CurlHelper::curl( $url , '' , $information, $headers );

        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Payment asociarTienda -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Payment asociarTienda - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( [
        						'status' => true,
    							'mensaje' => json_encode( $resultado['mensaje'] )
    						]);
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function crearContrato( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();

	$information["store_ids"] = str_replace ( env('APP_NOMESCLATURA_PAIS') , "" , $information["store_ids"][0]);

     	\Log::info("Payment crearContrato - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = env('PAYMENT_URL_CREAR_CONTRATO');

	$information["store_ids"] = array($information["store_ids"]);


	// Armo la json.
	$information = "[".json_encode($information)."]";
    	\Log::info("Payment asociarTienda - Informacion a enviar: " . print_r($information,true) );

        $resultado = CurlHelper::curl( $url , '' , $information, $headers );

        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
			\Log::info(" Payment crearContrato -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Payment crearContrato - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function validarCuentaBancaria( Request $request )
    {
        // Cargo la informacion.
        $information = $request->input();
    	// Obtengo el ID.
    	if( isset($information['apikey']) 
    		&& $information['apikey'] != '' 
    		&& intval($information['apikey']) !== 0 ) {
    		$apikey = $information['apikey'];
    	}else{ // En caso de error lo logueo.
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'apikey' es invalido" ), 400);
    	}

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = env('PAYMENT_URL_VALIDAR_CUENTA_BANCARIA') . "auth_type=apikey&zapikey=" . $apikey ;
        $resultado = CurlHelper::curl( $url , '' , '' , $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Payment validarCuentaBancaria -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Payment validarCuentaBancaria - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }
}
