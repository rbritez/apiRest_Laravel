<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;
class CategoryController extends Controller
{
    
    public function __construct(){
        $this->middleware('api.auth',['except' => ['index','show']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::all();
        if(is_object($categories) && !empty($categories)){
            $data =[
                'code' => 200,
                'status' => 'success',
                'categories' => $categories
            ];
        }else{
            $data =[
                'code' => 404,
                'status' => 'error',
                'message' => 'No existen categorias para mostrar.'
            ];
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //extraigo los datos
        $jsonCategory = $request->input('json');
        $params = \json_decode($jsonCategory); //objeto
        $params_array = \json_decode($jsonCategory,true); //array
            
            //Limpiar espacios
            $params_array = array_map('trim',$params_array);
            
            //Validamos los datos
            $validate = \Validator::make($params_array,[
                'name'      => 'required|alpha',
            ]);

            if($validate->fails()){
                //existe fallos y envio resultado
                $message_error = $validate->errors();
                $data = array(
                    'status' =>'error',
                    'code' => 404,
                    'message' => 'La categoria no se ha creado',
                    'errors' => $message_error
                );
            }else{
                 //creo la categoria
                $category = Category::create($params_array);
                
                $data = array(
                    'status' =>'success',
                    'code' => '200',
                    'message' => 'La categoria se ha creado correctamente',
                    'category' => $category
                );
            }
            //devuelto resultado
            return response()->json($data,$data['code']);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {   
        //busco la categoria a mostrar
        $category = Category::find($id);
        
        //valido la categoria a mostrar
        if(is_object($category)){
            //la categoria existe
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        }else{
            //la categoria no existe
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe.' 
            ];
        }
        return response()->json($data, $data['code']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //extraigo los datos
        $jsonCategory = $request->input('json');
        $params = \json_decode($jsonCategory); //objeto
        $params_array = \json_decode($jsonCategory,true); //array

        if(!empty($params_array) && !empty($params)){
            //Valido los datos 
            $validate = \Validator::make($params_array,[
                'name' => 'required|alpha'
            ]);

            if(!$id){
                $data=[
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha seleccionado una categoria'
                ];
                return response()->json($data,$datA['code']);
            }

            if($validate->fails()){
                //existe fallos entonces retorno mensaje de error
                $data =[
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha podido actualizar la categoria'
                ];
            }else{
                //quitamos los campos que no se actualizaran
                unset($params_array['id']);
                unset($params_array['created_ad']);
                //Traigo la categoria a editar y la edito
                $category = Category::where('id',$id)->update($params_array);
                //creando mensaje de resupuesa
                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $params->name
                ];
            }
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        //retorno resultados
        return response()->json($data,$data['code']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        //valido el id de categoria
        if(!$id){
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha seleccionado ninguna categoria'
            ];
        }else{
            //verifico si existe la categoria
            $isset = Category::find($id);
            if(!$isset){
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'La categoria seleccionada no existe'
                ];  
                return response()->json($data,$data['code']);
            }
            //elimino la categoria
            $category = Category::destroy($id);
            
            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => 'La categoria ha sido eliminada'
            ];
        }
        //retorno resultado
        return response()->json($data,$data['code']);
    }
}
