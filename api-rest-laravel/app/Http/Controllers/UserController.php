<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller {

    public function pruebas(Request $request) {
        return "Acción pruebas de USER - CONTROLLER";
    }

    public function register(Request $request) {
        /*
          $name = $request->input('name');
          $surname = $request->input('surname');
          return "Acción de registro usuarios: $name $surname"; */

        // 1. Recoger los datos del usuario por post
        $json = $request->input('json', null);
        //var_dump($json);
        //die();
        $params = json_decode($json); //lo decodifica en forma de objeto
        $params_array = json_decode($json, true); //lo decodifica en forma de array
        //var_dump($params);
        //var_dump($params_array);
        //die();

        if (!empty($params) && !empty($params_array)) {//validación si la decodificación JSON fue mal
            //1.1 Limpiar datos
            $params_array = array_map('trim', $params_array);

            //2. Validar datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        //3. Comprobar duplicado de usuario
                        'email' => 'required|email|unique:users',
                        'password' => 'required'
            ]);

            if ($validate->fails()) {
                //2.1 Validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                //2.2 Validacion pasada correctamente
                //4. Cifrar contraseña
                //$pwd = password_hash($params->password, PASSWORD_BCRYPT, ['cost' => 4]);//cifra la contraseña y cada vez es diferente
                $pwd = hash('sha256', $params->password);

                //5. Crear Usuario
                $user = new User();
                $user->name = $params_array['name'];
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd; //se usa la contraseña encriptada
                $user->role = 'ROLE_USER';

                //var_dump($user); die();
                //6. Guardar Usuario en BD
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            //Mensaje de prueba
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        //response() -> json(array,codeHttp); -- Convierte un array en JSON.
        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {


        /* $jwtAuth = new \JwtAuth();
          echo $jwtAuth->signup();
          return "Acción de login usuarios";
         */

        //1. Recibir datos por Post
        $json = $request->input('json', null);
        $params = json_decode($json); //decodificacion como objeto
        $params_array = json_decode($json, true); //decodificacion como arreglo
        //2. Validar esos datos
        $validate = \Validator::make($params_array, [
                    'email' => 'required|email',
                    'password' => 'required'
        ]);

        if ($validate->fails()) {
            //2.1 Validacion ha fallado
            $signup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido identificar',
                'errors' => $validate->errors()
            );
        } else {
            //3. Cifrar la password
            $pwd = hash('sha256', $params->password);

            //4. Devolver token o datos
            $jwtAuth = new \JwtAuth();
            $signup = $jwtAuth->signup($params->email, $pwd);

            if (!empty($params->gettoken)) {
                $signup = $jwtAuth->signup($params->email, $pwd, true);
            }
        }



        //prueba de logeuo manual
        //$jwtAuth = new \JwtAuth();
        //$email = 'eduar@zamora.com';
        //$password = 'eduar';
        //$pwd = hash('sha256', $password);
        //return $jwtAuth->signup($email, $pwd);//retorna el token del usuario
        //return $jwtAuth->signup($email, $pwd,true);//retorna el token del usuario

        return response()->json($signup, 200);
    }

    public function update(Request $request) {

        //1. Comprobar si el usuario está identificado
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        //2. Actualizar usuario
        //2.1 Recoger los datos por Post
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if ($checkToken && !empty($params_array)) {


            //2.1.1sacar usuario identificado
            $user = $jwtAuth->checkToken($token, true);

            //2.2 Validar los datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required|alpha',
                        'surname' => 'required|alpha',
                        //'email' => 'required|email|unique:users'.$user->sub
                        'email' => 'required'
            ]);

            //.....//

            if ($validate->fails()) {
                //2.2.1 Validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha actualizado',
                    'errors' => $validate->errors()
                );
            } else {

                //2.3 Quitar los campos que no quiero actualizar
                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['password']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);

                //var_dump($params_array['sub']);
                //die();
                //2.4 Actualizar usuario en bdd
                $user_update = User::where('id', $user->sub)->update($params_array);

                //var_dump($user);
                //var_dump($user_update->update($params_array));
                //die();
                //2.5 Retornar array con resultado
                /*$data = array(
                    'code' => 200,
                    'status' => 'success',
                    'user' => $user_update,
                    'changes' => $params_array
                );*/
                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'user' => $user ,
                    'changes' => $params_array
                );
            }
        } else {
            //mensaje error
            $data = array(
                'code' => 400,
                'message' => 'El usuario no está identificado'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        //1. Recoger datos de la peticion
        $image = $request->file('file0');
        //var_dump($image);

        //1.1 Validación de imagen
        $validate = \Validator::make($request->all(), [
                    'file0.*' => 'required|image|mimes:jpeg,jpg,png,gif'
        ]);

        //2. Guardar Imagen
        if (!$image || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir imagen'
            );
        } else {
            //time() -> fecha formato unix que es único e irrepetible
            //getClientOriginalName(); - > retorna el nombre original de la imagen
            $image_name = time().$image->getClientOriginalName();
            //las imágenes se guardan por disco, simulan a una carpeta.
            \Storage::disk('users')->put($image_name, \File::get($image)); //se da como parámetro el nombre de la imágen y el archivo como tal.
            //3. Devolver resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        //probando retorno del método.
        //return response($data, $data['code'])->header('Content-Type', 'text/plain');
        return response($data, $data['code']);
    }

    public function getImage($filename) {
        //validando existencia de imagen
        $isset = \Storage::disk('users')->exists($filename);

        if ($isset) {
            //obteniendo archivo de la carpeta user
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            return new Response($data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe'
                    ), $data['code']);
        }
    }

    public function detail($id) {
        $user = User::find($id);//simula un select from en la bd

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'El usuario no existe'
            );
        }

        return response()->json($data, $data['code']);
    }

}
