<?php

namespace App\Helper;

class CurlHelper
{
	/**
	 * Ejecuta los curl.
	 **/
    static function curl( $url , $get ='' , $post = '', $headers = '' )
    {
        // Creo el crul
        $ch = \curl_init($url);
        // Seteo los parametros.
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        // Seteo Parametros del get.
        if( $get  != '' ) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $get);
        }
        // Seteo Parametros del post.
        if( $post !== '' ){
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        // Seteo los headers en caso de existir.
        if( $headers !== '' && is_array( $headers )) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }else{
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));            
        }
        // Ejecuto la accion.
        try{
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            // verifico el resultado.
            if ( $httpcode != 200 && $httpcode != 201 ){
                \Log::warning("Curl Invalido, respuesta: $httpcode, Mensaje: $result");
                return [ "estado" => false, "mensaje" => $result , "codigo" => $httpcode ];
            }
        }catch( Exception $e )
        {
            \Log::error('Error al ejecutar el CURL' . $e->getMessage());
            return [ "estado" => false, "mensaje" => $e->getMessage() ];
        }

        // Devuelvo el resultado.
        return [ "estado" => true, "mensaje" => $result, "codigo" => $httpcode];
    }
}
