<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Post;
use App\Helpers\JwtAuth;

class PostController extends Controller {

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index',
                'show',
                'getImage',
                'getPostsByCategory',
                'getPostsByUser']]);
    }

    public function pruebas(Request $request) {
        return "Acción pruebas de POST - CONTROLLER";
    }

    public function index() {
        //$posts = Post::all();//Obtiene todos los datos con FK
        //Obtiene todos los datos con FK de usuarios
        //y muestra su relación con la tabla categoría.
        $posts = Post::all()->load('category');

        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'posts' => $posts
                        ], 200);
    }

    public function show($id) {
        $post = Post::find($id)->load('category')
                               ->load('user');

        if (is_object($post)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'posts' => $post
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'La entrada no existe'
            ];
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //1. Recoger datos por Post
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //2. Conseguir usuario identificado
            $user = $this->getIdentity($request);

            //3. Validar los datos
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required',
                        'image' => 'required'
            ]);

            if ($validate->fails()) {
                $data = array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado el post, faltan datos'
                );
            } else {

                //4. Guardar el artículo (entradas)
                $post = new Post();
                //var_dump($user);
                //var_dump($user['sub']);
                //die();
                //seteo de atributos
                $post->user_id = $user->sub;
                $post->category_id = $params->category_id;
                $post->title = $params->title;
                $post->content = $params->content;
                $post->image = $params->image;
                $post->save();

                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post
                );
            }
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Envía los datos correctamente'
            );
        }

        //5. Retornar resultados
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        //1. Recoger los datos por POST
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        $data = array(
            'code' => 400,
            'status' => 'error',
            'message' => 'Datos enviados incorrecto'
        );

        if (!empty($params_array)) {

            //2. Validar los datos
            $validate = \Validator::make($params_array, [
                        'title' => 'required',
                        'content' => 'required',
                        'category_id' => 'required'
            ]);

            if ($validate->fails()) {
                $data['errors'] = $validate->errors();
                return response()->json($data, $data['code']);
            }

            //3. Seleccionar datos actualizables
            unset($params_array['id']);
            unset($params_array['user_id']);
            unset($params_array['created_at']);
            unset($params_array['user']);

            //0. Conseguir usuario identificado
            $user = $this->getIdentity($request);

            //1. Comprobar si existe el registro (Conseguir el post)
            $post = Post::where('id', $id)
                    ->where('user_id', $user->sub)
                    ->first();

            if (!empty($post) && is_object($post)) {
                //4.	Actualizar el registro en concreto
                $post->update($params_array);

                $data = array(
                    'code' => 200,
                    'status' => 'success',
                    'post' => $post,
                    'changes' => $params_array
                );
            }
        }
        //retornar un resultado

        return response()->json($data, $data['code']);
    }

    public function destroy($id, Request $request) {
        //0. Conseguir usuario identificado
        $user = $this->getIdentity($request);

        //1. Comprobar si existe el registro (Conseguir el post)
        //$post = Post::find($id); - cualquier usuario puede borrar post
        $post = Post::where('id', $id)
                ->where('user_id', $user->sub)
                ->first(); //- condiciones para que solo el usuario propietario del post pueda eliminarlo

        if (!empty($post)) {
            //2. Borrarlo
            $post->delete();

            //3. Retornar el resultado
            $data = array(
                'code' => 200,
                'status' => 'success',
                'post' => $post
            );
            return response()->json($data, $data['code']);
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El post no existe'
            );
        }
    }

    private function getIdentity($request) {
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);

        return $user;
    }

    public function upload(Request $request) {
        //1. Recoger la imagen de la peticion
        $image = $request->file('file0');

        //2. Validar imagen
        $validate = \Validator::make($request->all(), [
                    'file0.*' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        //3. Guardar la imagen
        if (!$image || $validate->fails()) {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            );
        } else {
            $image_name = time() . $image->getClientOriginalName();

            \Storage::disk('images')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'success',
                'image' => $image_name
            );
        }

        //4. Retornar un resultado
        return response()->json($data, $data['code']);
    }

    public function getImage($filename) {
        //1. Comprobar si existe el fichero
        $isset = \Storage::disk('images')->exists($filename);

        if ($isset) {
            //2. Obtener la imagen
            $file = \Storage::disk('images')->get($filename);
            //3. retornar la imagen
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'La imagen no existe'
            );
        }

        //4. Retornar un resultado
        return response()->json($data, $data['code']);
    }

    public function getPostsByCategory($id) {
        $posts = Post::where('category_id', $id)->get();

        return response()->json([
                    'status' => 'success',
                    'posts' => $posts
                        ], 200);
    }

    public function getPostsByUser($id) {
        $posts = Post::where('user_id', $id)->get();

        return response()->json([
                    'status' => 'success',
                    'posts' => $posts
                        ], 200);
    }

}
