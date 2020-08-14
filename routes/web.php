<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//rutas de prueba
Route::get('/', function () {
    return view('welcome');
});
/*
Segunda forma de ingresar a una vista
  Route::get('/{ruta}', function ($ruta) {
    return view($ruta);
    }); 
*/
//Rutas del API

Route::get('/usuario/','UserController@index');
Route::get('/categoria/','CategoryController@index');
Route::get('/posteo/','PostController@index');

//Rutas del usuario
Route::post('api/register','UserController@register');
Route::post('api/login','UserController@login');
Route::put('api/user/update','UserController@update');
Route::post('api/user/upload','UserController@upload')->middleware('api.auth');
Route::get('/api/user/avatar/{filename}','UserController@getImage');
Route::get('api/user/detail/{user}','UserController@detail');

//Rutas del categorias