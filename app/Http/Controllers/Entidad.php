<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Entidad extends Controller
{
    // Parametros a verificar.
    const PARAMETROS = [ 'type' , 'value' ];

    const DOCUMENTOS = [ 'CNPJ', 'CPF', 'RG' ];

    const URL_INFORMACION = 'https://services.rappi.com.br/api/rs-onboarding-support/background-check';

    /**
     * @param  Request  $request
     * @return Response
     **/
    public function verificarEntidad( Request $request )
    {
        $parametro = $request->only(['type', 'value']);

        $parametro['type'] = strtoupper( $parametro['type'] );

        $estado = $this->validarNoVacio( $parametro );
        if( $estado['estado'] === false ) {
                http_response_code(200);
                return \Response::json(array(
                                    'status'      =>  false,
                                    'mensaje'     => $estado['mensaje']
                                        ), 200);
        }
        $estado = $this->validarExistenciaParametros( $parametro, self::PARAMETROS );
        if( $estado['estado'] === false ) {
                http_response_code(200);
                return \Response::json(array(
                                    'status'      =>  false,
                                    'mensaje'     => $estado['mensaje']
                                        ), 200);
        }

        if( !in_array($parametro['type'], self::DOCUMENTOS) ) {
                http_response_code(200);
                return \Response::json(array(
                                    'status'      =>  false,
                                    'mensaje'     => 'El type es invalido'
                                        ), 200);
        }
        // Realizo la peticion.
        $getParam = '?type=' . $parametro['type'] . '&value=' . $parametro['value'];
        $resultado = $this->ejecutarCurl( self::URL_INFORMACION . $getParam , '');

        // Verifico el resultado.
        if( $resultado === false ) {

                http_response_code(200);
                return \Response::json(array(
                                    'status'      =>  false
                                        ), 200);
        }

        
        // Trasnformo el mensaje.
        $resultado = json_decode($resultado,true);
        
        //Verifico los parametros.
        if( $resultado['socialReason'] == '' || $resultado['socialReason'] == null )
        {
                http_response_code(200);
                return \Response::json(array(
                                            'status'      =>  false
                                        ), 200);
        }

        $resultado['status'] = true;

        //Retorno el resultado.
        return json_encode( $resultado );
    }

    /**
     *
     **/
    private function ejecutarCurl( $url , $paramsGet = '' )
    {
        // Creo el crul
        $ch = curl_init($url);
        // Seteo los parametros.
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        if( $paramsGet  != '' ) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $paramsGet);
        }

        // Ejecuto la accion.
        try{
                $result = curl_exec($ch);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                if (empty($result) || $httpcode == 404 || $httpcode == 400 ){
                        return false;
                }
        }catch( Exception $e )
        {
                return false;
        }


        // Devuelvo el resultado.
        return $result;
    }

    /**
     * Valida que el parametro no llegue vacio.
     **/
    private function validarNoVacio( $parametros )
    {
            $resultado = ['estado'=> true];
            $parametrosInvalidos = [];

            foreach ($parametros as $key => $parametro) {
                    if( $parametro === '' || $parametro === null ){
                            $parametrosInvalidos[] = $key ;
                    }
            }

            if( count( $parametrosInvalidos ) > 0 ){
                    $resultado = ['estado' => false, 'mensaje' => "Los siguentes parametros "
                                                                ."tienen valores invalidos: '"
                                                                . implode(', ', $parametrosInvalidos) . "'" ];
            }

            return $resultado;
    }

    /**
     * Funcion que valida la existencia de parametros.
     */
    private function validarExistenciaParametros( $get , $parametros )
    {
            $resultado = ['estado'=> true];
            $parametrosInvalidos = [];

            foreach ($parametros as $parametro) {
                    if( !in_array($parametro, array_keys($get)) ){
                            $parametrosInvalidos[] = $parametro;
                    }
            }

            if( count( $parametrosInvalidos ) > 0 ){
                    $resultado = ['estado' => false, 'mensaje' => "Los siguentes parametros "
                                                                ."obligatorios no han sido completados: '"
                                                                . implode(', ', $parametrosInvalidos) . "'" ];
            }
            return $resultado;
    }
}
