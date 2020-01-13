<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CurlHelper;

class Pagos extends Controller
{
    const URL_CREAR_NEGOCIO = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/payments/business';

    const URL_CREAR_CUENTA_BANCARIA = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/payments/business/[BUSSINES-ID]/account';

    const URL_ASOCIAR_CUENTA = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/payments/account/[ACCOUNT-ID]/stores';

    const URL_CREAR_CONTRATO = 'http://microservices.dev.rappi.com/api/rs-onboarding-support/payments/contract';

    const URL_VALIDAR_CUENTA_BANCARIA = 'https://www.zohoapis.com/crm/v2/functions/zint_107_baccount_creation_response/actions/execute?';

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
    	\Log::info("Pagos crearNegocio - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CREAR_NEGOCIO, '' , $information, $headers );
        //$resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Pagos crearNegocio -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Pagos crearNegocio - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        //return json_encode( [
        //						'status' => true,
    	//						'mensaje' => json_encode( $resultado['mensaje'], true )
    	//					]);

		$arr = json_decode($resultado['mensaje'], true);
		$arr['status'] =  true;		
		
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

        // Cargo la informacion.
        $information = $request->input();
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
    	\Log::info("Pagos crearCuentaBancaria - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = str_replace('[BUSSINES-ID]', $businessid, self::URL_CREAR_CUENTA_BANCARIA);
        $resultado = CurlHelper::curl( $url , '' , $information, $headers );
        //$resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Pagos crearCuentaBancaria -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Pagos crearCuentaBancaria - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        	$arr = json_decode($resultado['mensaje'], true);
		$arr['status'] =  true;		
		
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

        // Cargo la informacion.
        $information = $request->input();
    	// Obtengo el ID.
    	if( isset($information['accountid']) 
    		&& $information['accountid'] != '' 
    		&& intval($information['accountid']) !== 0 ) {
    		$accountid = $information['accountid'];
    	}else{ // En caso de error lo logueo.
            return \Response::json(array( 'status' => false, 'mensaje' => "El parametro 'accountid' es invalido" ), 400);
    	}

        // Armo la json.
		$information = json_encode($request->post());
    	\Log::info("Pagos asociarTienda - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = str_replace('[ACCOUNT-ID]', $accountid, self::URL_ASOCIAR_CUENTA);
        $resultado = CurlHelper::curl( $url , '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Pagos asociarTienda -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Pagos asociarTienda - Curl Response: " . print_r($resultado,true) );
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
    	\Log::info("Pagos crearContrato - Informacion a enviar: " . print_r($information,true) );

        // Armo los headers
        $headers = [ 'Content-Type: application/json' ] ;

    	// Realizo el Curl con el envio.
    	$url = self::URL_CREAR_CONTRATO;
        $resultado = CurlHelper::curl( $url , '' , $information, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
			\Log::info(" Pagos crearContrato -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Pagos crearContrato - Curl Response: " . print_r($resultado,true) );
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
    	$url = self::URL_VALIDAR_CUENTA_BANCARIA . "auth_type=apikey&zapikey=" . $apikey ;
        $resultado = CurlHelper::curl( $url , '' , '' , $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Pagos validarCuentaBancaria -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
            			'mensaje' => 'Error al solicitar la creacion del negocio: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

    	\Log::info("Pagos validarCuentaBancaria - Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true] );
    }
}