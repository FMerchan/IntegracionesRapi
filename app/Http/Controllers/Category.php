<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Category extends Controller
{
    const URL_CATEGORIAS = 'http://microservices.dev.rappi.com/api/rests-taxonomy/taxonomy/terms?scheduled=false&is_subterm=false';

    const URL_RESPONSE = 'https://www.zohoapis.com/crm/v2/functions/zint_114_taxonomias/actions/execute?auth_type=apikey&zapikey=1003.7c02a5b10f13810ff9499d39a02c0d43.605b9c5964f99646e4da05c6b3e3afdc';

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function listar( Request $request )
    {
		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CATEGORIAS );
        $resultado['mensaje'] = json_decode($resultado['mensaje'],true);
        
        // Seteo los textos.
        $categorias = [];
        foreach ($resultado['mensaje']['categories'] as $key => $categoria) {
            $categorias[] = json_encode($categoria);
        }

        // Armo los headers
        $headers = array ( 'Content-Type: text/plain' ) ;

        // Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_RESPONSE, '' , $categorias, $headers );
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
}
