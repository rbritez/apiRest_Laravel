<?php

use App\Http\Controllers\Api\InventoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*
 * -----------------------------------------------------------
 *                  Rutas de Posteo
 * -----------------------------------------------------------
 */
  //Guardar Imagen
  Route::post('post/upload','PostController@upload')->middleware('api.auth');
  //Mostrar Imagen
  Route::get('post/image/{filename}','PostController@getImage');

  Route::get('logistics/pvt/inventory/items/{id}/warehouses/{warehouseId}',[InventoryController::class,'show']);

  Route::put('logistics/pvt/inventory/skus/{id}/warehouses/{warehouseId}',[InventoryController::class,'update']);