<?php

namespace App\Helpers;

use Firebase\JWT\JWT; //indicando que se va a usar JWT
use Illuminate\Support\Facades\DB; //Paquete para realizar operaciones en la BD
use App\User; //Importando el modelo de Usuario

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = 'esto_es_una_clave_super_secreta-071189729';
    }

    public function signup($email, $password, $getToken = null) {
        //1. Buscar si existe el usuario con sus credenciales
        $user = User::where([
                    'email' => $email,
                    'password' => $password
                ])->first(); //selecciona el primer objeto que cumpla con las validaciones
        //2. Comprobar si son Correctos(objeto)
        $signup = false;
        if (is_object($user)) {
            $signup = true;
        }

        //3. Generar el token con los datos del usuario identificado
        if ($signup) {
            $token = array(
                //en JWT usualmente se usa:
                //sub para indicar el id de la identidad
                'sub' => $user->id,
                //Los atributos necesarios para la lógica
                //y estos concuerdan con los de la tabla de la Bd
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'description'   => $user->description,
                'image'   => $user->image,
                //iat para indicar cuando fue creado el token
                'iat' => time(),
                //exp para indicar cuando se expira el token
                'exp' => time() + (7 * 24 * 60 * 60)#(en una semana)
            );

            #La key es unica y solo lo sabe el programador
            #Sintaxis. $jwt = JWT::encode(token, key, AlgoritmoCodificador);
            $jwt = JWT::encode($token, $this->key, 'HS256'); #la key es unica y solo lo sabe el programador
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            //4. Devolver los datos decodificados o el token, en funcion de un parámetro    }else{
            if (is_null($getToken)) {
                $data = $jwt;
            } else {
                $data = $decoded;
            }
        } else {
            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }


        return $data;
    }

    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;//autenticación por defecto falsa

        try{
            $jwt = str_replace('"', '',$jwt);//quitando " en el token
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);//decodificandolo
        } catch (\UnexpectedValueException $ex) {
            $auth = false;//autenticacion fallida por exceptions
        }catch (\DomainException $ex) {
            $auth = false;//autenticacion fallida por exceptions
        }

        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)){
            //la utenticacion se da si el token decodificado:
            //NO está vacío, es un objeto y tiene el atributo sub
            $auth = true;
        }else{
            $auth = false;
        }

        if ($getIdentity){
            //se da el token decodificado si viene el parámetro get Identity
            return $decoded;
        }

        return $auth;
    }

}
