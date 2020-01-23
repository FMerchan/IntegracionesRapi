<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Category extends Controller
{
	/**
	* @param  Request  $request
	* @return Response
	**/
    public function listar( Request $request )
    {
		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_CATEGORIAS') );
        $resultado['mensaje'] = json_decode($resultado['mensaje'],true);
        
        // Seteo los textos.
        $categorias = [];
        foreach ($resultado['mensaje']['categories'] as $key => $categoria) {
            $categorias[] = json_encode($categoria);
        }

        // Armo los headers
        $headers = array ( 'Content-Type: text/plain' ) ;

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_RESPONSE'), '' , $categorias, $headers );
        $resultado["mensaje"] = json_decode($resultado["mensaje"],true);

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Category Listar -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar la actualizacion del listado: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

        // Logueo el estado.
    	\Log::info("Category Listar Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true,
    						'categories' => $resultado['mensaje']['categories']] );
    }


    /**
    * @param  Request  $request
    * @return Response
    **/
    public function listarCategoriasMails( Request $request )
    {
        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_CATEGORIAS_EMAILS') );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Category listarCategoriasMails -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar al listar las categorias: ' . $resultado['mensaje'] ), 200);
        }

        // Logueo el estado.
        \Log::info("Category listarCategoriasMails Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'types' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
    }


    /**
    * @param  Request  $request
    * @return Response
    **/
    public function listarCiudades( Request $request )
    {
        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_CIUDADES') );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Category listarCiudades -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al listar las ciudades: ' . $resultado['mensaje'] ), 200);
        }

        // Logueo el estado.
        \Log::info("Category listarCiudades Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'cities' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
    }

    /**
     * Funcion que devuelve los tipos de modos de telefono.
     **/
    public function listarCategoriasTelefonos()
    {
        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_CATEGORIAS_TELEFONO') );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Category listarCategoriasTelefonos -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar los tipos de telefono: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

        // Logueo el estado.
        \Log::info("Category listarCategoriasTelefonos - Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'types' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
    }


    /**
     * Funcion que devuelve las zonas.
     **/
    public function listarZonas()
    {
        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( env('CATEGORY_URL_ZONAS') );

        // Verifico el resultado.
         if( $resultado['estado'] === false ) {
             \Log::info(" Category listarZonas -  Error: " . print_r($resultado["mensaje"],true) );
            return \Response::json(array( 'status' => false, 
                        'mensaje' => 'Error al solicitar las zonas: ' . json_encode( $resultado['mensaje'] ) ), 200);
        }

        // Logueo el estado.
        \Log::info("Category listarZonas - Curl Response: " . print_r($resultado,true) );
        $respuesta = array_merge( 
                                    [ 'zones' => json_decode($resultado['mensaje'], true) ], 
                                    [ 'status' => true ]
                                );
        return json_encode( $respuesta );
    }

}
