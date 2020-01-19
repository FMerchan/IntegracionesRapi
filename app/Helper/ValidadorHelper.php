<?php

namespace App\Helper;

class ValidadorHelper
{
	/**
	 * Funcion que valida la existencia de parametros.
	 **/
	static function existencia( array $valores, array &$parametros, $keyUnsencitive = false )
	{
		// Verifico si es key sensitive.
		if( $keyUnsencitive ){
				$parametros = array_change_key_case($parametros);
		}
		// Verifico la existencia.
		foreach ($valores as $valor) {
			if( !isset( $parametros[$valor] ) 
				|| $parametros[$valor] === '' ) {
				return ['resultado' => false, 'parametro' => $valor];
			}
		}
		return ['resultado' => true];
	}

	/**
	 * Funcion que prepara parametros para ser enviados.
	 **/
	static function prepararParametros( array $valores, array $parametros )
	{
		// Preparo los valores.
		$ret = [ 
					"serializado" => [],
					"no-serializado" => [],
					"no-encontrado" => [] 
				];

		// Armo los parametros.
		foreach ($valores as $valor) {
			// Busco el parametro.
			$val = $parametros[$valor]; // TODO MEJORAR

			// Setep el resultado.
			if( $val ) {
				$ret["serializado"][] = $valor . "=" . $val;
				$ret["no-serializado"][$valor] = $val;
			}else{
				$ret["no-encontrado"][] = $valor;
			}
		}
		// Serealizo los valores.
		$ret["serializado"] = implode("&", $ret["serializado"]);

		return $ret;
	}

	/**
	 * Valida que un Json tenga un formato valido.
	 **/
	static function jsonValido( $info )
	{
		if(is_array($info) && count($info) == 0){
			return false;
		}
		if( is_array($info) && count($info) > 0 )
		{
			return true;
		}else{
			$result = json_decode($info,true);
	 		return (json_last_error() == JSON_ERROR_NONE);
		}
	}
}

