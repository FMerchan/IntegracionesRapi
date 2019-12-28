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

/*
Route::get('/asignar_leads', 'AssignLeadsController@assignLeads');
*/


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

