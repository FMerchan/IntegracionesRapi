<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|


Route::get('/', function () {
    #return view('welcome');
	echo "Este es el index";
});
*/

Route::post('/webhook/payments/partner/{external_id}', 'WebhookController@notifyPaymentsPartner');

Route::get('/geo-stores/has-coverage/{lng}/{lat}', 'GeoStoresController@hasCoverage');