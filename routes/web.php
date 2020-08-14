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

//Rutas de registro y acceso
//Creacion de Usuario
Route::post('api/register','UserController@register');
//Logeo del Usuario
Route::post('api/login','UserController@login');

//Rutas del usuario
Route::group(['prefix'=>'/api/user/'],function(){
  //Editar Usuario
  Route::put('update','UserController@update');
  //Guardar Imagen
  Route::post('upload','UserController@upload')->middleware('api.auth');
  //Mostrar Imagen
  Route::get('avatar/{filename}','UserController@getImage');
  //Mostrar Datos de Usuario
  Route::get('detail/{user}','UserController@detail');
});

//Rutas del categorias
Route::resource('/api/category','CategoryController');
