<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Api verificacion de Entidades.
Route::get('v1/support/checkentity', 'Entidad@verificarEntidad');

// APi creacion de Stores.
Route::post('v1/store/create/', 'Store@crear');

// APi Actualizacion de Stores.
Route::get('v1/store/menu/scraped/', 'Store@updateStore');

// APi creacion de Contract.
Route::post('v1/contract/create/', 'Contract@crear');

// APi Actualizacion de Contract.
Route::get('v1/contract/signed/', 'Contract@updateContract');

// APi creacion de Brand.
Route::post('v1/brand/create/', 'Brand@crear');

// Api validacion de telefono
Route::post('v1/phone/validation/', 'Phone@validar');

// Api validacion de telefono
Route::get('v1/category/list/', 'Category@listar');

// -----------------------------------------------
// --------- Seccion de Pagos.
// -----------------------------------------------
// Api creacion de pagos.
Route::post('v1/payment/create-business/', 'Pagos@crearNegocio');

// Api creacion de pagos.
Route::post('v1/payment/create-bank-account/', 'Pagos@crearCuentaBancaria');

// Api creacion de pagos.
Route::get('v1/payment/bank-acount-validate/', 'Pagos@validarCuentaBancaria');

// Api creacion de tiendas
Route::post('v1/payment/associate-store/', 'Pagos@asociarTienda');

// Api creacion contratos asociados a las tiendas..
Route::post('v1/payment/create-contract/', 'Pagos@crearContrato');

// --------- Fin.
// -----------------------------------------------

// -----------------------------------------------
// --------- Seccion de Usuario.
// -----------------------------------------------
// APi creacion de Contract.
Route::post('v1/user/create/', 'Usuario@crear');

// APi asociacion de usuario.
Route::post('v1/user/association/', 'Usuario@asociar');

// --------- Fin.
// -----------------------------------------------

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
