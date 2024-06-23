<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$router->get('/', function () use ($router) {
//    return $router->app->version();
//});


use Illuminate\Http\Request;

$router->group(['prefix' => 'api'], function () use ($router) {
   $router->group(['prefix' => 'user'], function () use ($router) {
       $router->post('login', 'AuthController@login');
   });

   $router->group(['prefix' => 'admin'], function () use ($router) {
       $router->post('generate-tanggal', 'AdminController@generate_tanggal');

       $router->post('get-tanggal', 'AdminController@get_tanggal');

       $router->get('get-all-tanggal', 'AdminController@getAllTanggal');

       $router->get('get-list-tahun-ajaran', 'AdminController@getListTahunAjaran');

       $router->get('get-eligible-asesor', 'AdminController@get_eligible_asesor');

       $router->post('assign-role','AdminController@post_assign');

       $router->post('delete-assign-role','AdminController@delete_assign');

       $router->get('get-asesor', 'AdminController@get_asesor');

       $router->get('check-asesor/{idPegawai}', 'AdminController@checkAsesor');
   });
});
