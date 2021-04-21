<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return "Estas en el Index de post";
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * -----------------------------------------------------------------------------
     *                                  Guardar Imagen
     * -----------------------------------------------------------------------------
    */
    public function upload(Request $request){
        //recoger datos de la peticion
        $image = $request->file('file0');
        //Validacion de Imagen

        $validate = Validator::make($request->all(),[
            'file0' => 'required|image|mimes:jpg,jpeg,png'
        ]);
        //guardar imagen
        if(!$image || $validate->fails()){

            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No se ha podido subir la imagen',
            ];
        }else{

            $image_name = time().$image->getClientOriginalName();
            Storage::disk('posts')->put($image_name, File::get($image));

            $data = [
                'code' => 200,
                'status' => 'success',
                'image' => $image_name,
            ];
        }
        return response()->json($data,$data['code']);
    }

    /**
     * -----------------------------------------------------------------------------
     *                                  Recuperar Imagen
     * -----------------------------------------------------------------------------
    */
    public function getImage($filename){
        //verificar si el archivo existe
        $isset = Storage::disk('posts')->exists($filename);

        //envio el archivo
        if($isset){
            $file = Storage::disk('posts')->get($filename);

            return new Response($file,200);
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe',
            ];

        }
        return response()->json($data,$data['code']);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}