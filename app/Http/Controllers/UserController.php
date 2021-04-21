<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\User;

class UserController extends Controller
{
    public function index(){
        return "Estas en el Index de usuario";
    }

    /**
     * -----------------------------------------------------------------------------
     *                                Registrar Usuario
     * -----------------------------------------------------------------------------
    */
    public function register(Request $request){

        //obtenemos los datos del usuario
        $json = $request->input('json', null);
        $params = \json_decode($json); //crea un objeto
        $params_array = \json_decode($json,true); //crea un array
        //dd($params_array);
        if(!empty($params_array) && !empty($params)){
            //Limpiar espacios
            $params_array = array_map('trim',$params_array);

            //validamos datos
            $validate = Validator::make($params_array,[
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users',
                'password'  => 'required'
            ]);

            if($validate->fails()){
                $message_error = $validate->errors();
                $data = array(
                    'status' =>'error',
                    'code' => '404',
                    'message' => 'El usuario no se ha creado',
                    'errors' => $message_error
                );
            }else{

                //ciframos de contraseña
                $pass_hash = \password_hash($params->password,PASSWORD_BCRYPT,['cost'=> 4]);

                //Creamos el usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pass_hash;
                $user->role = 'ROLE_USER';
                $user->save();
                //dd($user);
                $data = array(
                    'status' =>'success',
                    'code' => '200',
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }

        }else{
            $data = array(
                'status' =>'error',
                'code' => '403',
                'message' => 'Los datos enviados no son correctos'
            );
        }
        return \response()->json($data,$data['code']);
    }

    /**
     * -----------------------------------------------------------------------------
     *                                  Loguear Usuario
     * -----------------------------------------------------------------------------
    */
    public function login(Request $request){
        //recibiendo el post
        $json = $request->input('json',null);
        $params = \json_decode($json);
        $params_array = \json_decode($json,true);

        //validar datos
        $validate = Validator::make($params_array,[
            'email'     => 'required|email',
            'password'  => 'required'
        ]);

        if($validate->fails()){

            $data = array(
                'status' =>'error',
                'code' => '404',
                'message' => 'El usuario no se ha podido logear',
                'errors' => $validate->errors()
            );
            return \response()->json($data,$data['code']);
        }else{
            //devolver datos con token
            $jwtAuth = new JwtAuth();
            $singup = $jwtAuth->singup($params->email,$params->password);
            if(isset($params->gettoken) && !empty($params->gettoken)){
                $singup = $jwtAuth->singup($params->email,$params->password,true);
            }
            return \response()->json($singup,200);
        }

    }

    /**
     * -----------------------------------------------------------------------------
     *                                Actualizar Usuario
     * -----------------------------------------------------------------------------
    */
    public function update(Request $request){

        //Actualizamos el usuario
        $token = $request->header('authorization');
        $jwtAuth = new JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //Recogemos datos por post del usuario
        $json = $request->input('json',null);
        $params = \json_decode($json);
        $params_array = \json_decode($json,true);
        if($checkToken && !empty($params_array)){
            //obtener datos del usuario identificado
            $user = $jwtAuth->checkToken($token,true);
            //validamos datos
            $validate = Validator::make($params_array,[
                'name'      => 'required|alpha',
                'surname'   => 'required|alpha',
                'email'     => 'required|email|unique:users'.$user->sub,
            ]);

            //quitamos los campos que no se actualizaran
            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_ad']);
            unset($params_array['remember_token']);
            //actualizamos usuario en la db
            $user_update = User::where('id',$user->sub)->update($params_array);
            //devolver array con resultado
                $data = array(
                    'code'=>200,
                    'status'=>'success',
                    'user' => $user,
                    'changes' => $params_array
                );
        }else{
            $data = array(
                'code'      => 400,
                'status'    => 'error',
                'message'   => 'El usuario no esta identificado.'
            );
        }
        return response()->json($data,$data['code']);
    }

    /**
     * -----------------------------------------------------------------------------
     *                           Recuperar datos del usuario
     * -----------------------------------------------------------------------------
    */
    public function detail ($id){
        $user = User::find($id);

        if(is_object($user)){
            $data = [
                'code' => 200,
                'status' => 'success',
                'user' => $user
            ];
        }else{
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe.'
            ];
        }
        return response()->json($data, $data['code']);
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
            Storage::disk('users')->put($image_name,File::get($image));

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
        $isset = Storage::disk('users')->exists($filename);

        //envio el archivo
        if($isset){
            $file = Storage::disk('users')->get($filename);

            return new Response($file,200);
        }else{
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe',
            ];

        }
        return response()->json($data,$data['code']);
    }
}