<?php
namespace App\Helpers;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth{

    //atributos
    public $key;

    //Metodos

    public function __construct(){
        $this->key = 'clave_secreta_para_el_token';
    }

    public function singup($email,$password,$getToken = null){
        //buscar si existe el usuario con credenciales (email,contraseña)
            $user = User::where([
                'email' => $email
            ])->first();

        //comprobar si son correctas
            $singup = false;
            if(is_object($user)){
                if(password_verify($password,$user->password)){
                    $singup = true;
                }
            }
        //generar el token con los datos del usuario
            if($singup){
                $token = array(
                    'sub' => $user->id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'surname' => $user->surname,
                    'iat' => time(), //la fecha en la que se creo el token
                    'exp' => time() + (7 * 24 * 60 * 60) //el token dura una semana
                );

                $jwt = JWT::encode($token,$this->key,'HS256');
                $decoded = JWT::decode($jwt,$this->key,['HS256']);
            //devolver los datos decodificados o token en funcion de un parametro
                if(is_null($getToken)){
                    $data = $jwt;
                }else{
                    $data = $decoded;
                }
            }else{
                $data = array(
                    'status' => 'error',
                    'message' => 'Login Incorrecto'
                );
            }

        return $data;
    }
    public function checkToken($jwt,$getIdentity = false){
        $auth = false;
        try{
            $jwt = \str_replace('"','',$jwt); //limpia las comillas del token
            $decoded = JWT::decode($jwt,$this->key,['HS256']);
        }catch(\UnexpectedValueException $e){
            $auth = false;
        }catch(\DomainException $e){
            $auth = false;
        }
        if(!empty($decoded && is_object($decoded) && isset($decoded->sub))){
            $auth = true;
        }else{
            $auth = false;
        }
        //combrobar si llega el flag -> getIdentity devolver el token 
        if($getIdentity){
            return $decoded;
        }
        return $auth;
       
    }

}