<?php

namespace App\Http\Controllers;

use CurlHelper;
use Illuminate\Http\Request;

class Category extends Controller
{
    const URL_CATEGORIAS= 'http://microservices.dev.rappi.com/api/rests-taxonomy/taxonomy/terms?scheduled=false&is_subterm=false';

	/**
	* @param  Request  $request
	* @return Response
	**/
    public function listar( Request $request )
    {
		// Realizo el Curl con el envio.
        $resultado = CurlHelper::curl( self::URL_CATEGORIAS );
        $resultado['mensaje'] = json_decode($resultado['mensaje'],true);

        // Logueo el estado.
    	\Log::info("Crear Store Curl Response: " . print_r($resultado,true) );
        // Retorno el estado del resultado.
        return json_encode( ['status' => true,
    						'categories' => $resultado['mensaje']['categories']] );
    }
}
