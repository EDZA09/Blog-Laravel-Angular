<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {

    public function __construct() {
        //Configurando las rutas resource para que todas no usen el middleware de autenticación.
        //Se hace en el constuctor del controlador por que se accede a la propiedad middleware
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    public function pruebas(Request $request) {
        return "Acción pruebas de CATEGORY - CONTROLLER";
    }

    public function index() {
        $categories = Category::all();

        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ], 200);
    }

    public function show($id) {
        $category = Category::find($id);

        if (is_object($category)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'category' => $category
            );
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function store(Request $request) {
        //1. Recoger datos por Post
        $json = $request->input('json', null);

        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //2. Validar los datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);

            //3. Guardar la Categoria
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                $category = new Category();
                //como este modelo solo tiene un campo rellenable, se indica cual es.
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        //4. Retornar resultado
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        //1.Recoger los datos por PUT
        $json = $request->input('json', null);

        $params_array = json_decode($json, true);


        if (!empty($params_array)) {
            //2. Validar los datos
            $validate = \Validator::make($params_array, [
                        'name' => 'required'
            ]);

            //if ($validate->fails()) {
                //$data = [
                    //'code' => 400,
                    //'status' => 'error',
                    //'message' => 'No se ha actualizado la categoria'
                //];
            //} else {
                //3.Quitar lo que no se desea actualizar
                unset($params_array['id']);
                unset($params_array['created_at']);

                //4.Actualizar Registro (Categoria)
                //->update(), retorna 1(true) o 0(false) si se realizaron los cambios y se encontro el registro
                //updateOrCreate(), retorna el objeto(registro) actualizado

                $category_update = Category::where('id', $id)->updateOrCreate($params_array);

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category_update,
                    'changes' => $params_array
                ];
            //}
        }else{
            $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha actualizado la categoria'
                ];
        }
        //5.Devolver resultados
        return response()->json($data, $data['code']);
    }

}
