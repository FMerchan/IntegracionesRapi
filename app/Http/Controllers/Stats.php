<?php

namespace App\Http\Controllers;

use CurlHelper;
use ValidadorHelper;
use Illuminate\Http\Request;

class Stats extends Controller
{
    /**
     * URL para confirmar la primer venta de un store
     **/
    const URL_CONFIR_PRIMER_VENTA = 'https://www.zohoapis.com/crm/v2/functions/zint_618_primera_venta/actions/execute?auth_type=apikey&zapikey=1003.7c02a5b10f13810ff9499d39a02c0d43.605b9c5964f99646e4da05c6b3e3afdc';


	/**
	* @param  Request  $request
	* @return Response
	**/
    public function store( Request $request )
    {
 		// Cargo la informacion.
    	$information = $request->post();

        // Valido el json.
        if(!ValidadorHelper::jsonValido($information)){
            // \Log::info("Stats Store - Informacion a guardar: " . print_r($information,true) );
            return \Response::json(array( 'status' => false , 'mensaje' => 'El formato del Json es invalido.'), 200);
        }

        $resultado = $this->validarInformacionStore( $information );

        // Verifico el resultado.
        if( $resultado['estado'] === false ) {
             \Log::info(" Stats Store -  Error estado: " . print_r( $resultado["mensaje"] , true ) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error : ' . $resultado['mensaje'] ), 200);
        }

        // Retonro el resultado.
       	return \Response::json( array( 'status' => true , 'stats' => $resultado["mensaje"] ), 200);
    }

    //----------------------------------------------------------------------------------
    //---- Funciones privadas.
    //----------------------------------------------------------------------------------
    /**
     * Funcion que valida la informacion recibida del store.
     * @var Array informacion
     * @return Array, devuelve un array con el estado y un mensje informativo.
     **/
    private function validarInformacionStore( $informacion )
    {
        // respuesta.
        $respuesta = [ 'estado' => true, 'mensaje' => [] ];

        // Recorro la informacion.
        foreach ($informacion as $info) {
            // Verifico si es la primer venta.
            if( isset($info["store_id"]) 
                && isset($info["date"])
                && isset($info["first_order_date"]) ){
                if( $info["first_order_date"] === $info["date"] ){
                    // De ser la primer venta notifico.
                    $callPrimerMetrica = $this->storePrimerMetrica( $info["store_id"], $info["first_order_date"] );
                    // Almaceno la informacion de la primer venta.
                    if( $callPrimerMetrica['estado'] === false ){
                        $mensajeError = "La primera venta '" . $info["store_id"] 
                                        . "' no se pudo realizar, Error: " . $callPrimerMetrica['mensaje'];
                        $respuesta['mensaje'][] = ['status' => false,'message'=>$mensajeError] ;

                    }else{
                        $respuesta['mensaje'][] = array_merge(['status' => true],$callPrimerMetrica['mensaje']) ;
                    }
                }
            }elseif( isset($info["store_id"]) ){
                $mensajeError = "La primera venta '" . $info["store_id"] . "' no se pudo realizar, "
                                . "nose encontraron los parametros de date o first_order_date ";
                $respuesta['mensaje'][] = ['status' => false,'message'=>$mensajeError] ;
            }
        }

        return $respuesta;
    }

    /**
     * Funcion que confirma que es la primera venta contra rappi..
     * @var Array informacion
     * @return Array, devuelve un array con el estado y un mensje informativo.
     **/
    private function storePrimerMetrica( $storeId, $firstOrderDate )
    {
        $storeId = env('APP_NOMESCLATURA_PAIS').$storeId;
	
        // Armo la URL
        $url = self::URL_CONFIR_PRIMER_VENTA . "&vRappiStoreId=$storeId&vPrimeraVenta=$firstOrderDate";

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( $url );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);
        // Verifico la respuesta.
        if( $resultado["estado"] === false ){
            return ["estado" => false, "mensaje" => $resultado["mensaje"]["message"] ];
        }
        // Verifico que exista un detalle.
        if(isset($resultado["mensaje"]["details"])){
            // Retorno el resultado.
            return ["estado" => true, "mensaje" => $resultado["mensaje"]["details"] ];
        }
        return ["estado" => true];
    }
}
