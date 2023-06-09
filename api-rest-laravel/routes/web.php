<?php

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

// Cargando clases
use App\Http\Middleware\ApiAuthMiddleware;//importando middleware
//use App\Http\Middleware;

//RUTAS DE PRUEBAS
Route::get('/', function () {
    return view('welcome');
});

Route::get('/pruebas', function () {
    return '<h2>Texto desde una ruta</h2>';
});

/*Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '.$nombre;
    return $texto;
});*/

Route::get('/pruebas/{nombre?}', function ($nombre = null) {
    $texto = '<h2>Texto desde una ruta</h2>';
    $texto .= 'Nombre: '.$nombre;
    return view('pruebas',array(
        'texto' => $texto
            ));
});

Route::get('/animales', 'PruebasController@index');

Route::get('/testorm','PruebasController@testORM');

//RUTAS DEL API
//
//  Métodos HTTP comunes
//  GET: Conseguir datos o recursos
//  POST: Guardar datos o recurso o hacer lógica desde un formulario
//  PUT: Actualizar datos o recursos
//  DELETE: Eliminar datos o recursos
//
    //Rutas de pruebas
    Route::get('/usuario/pruebas','UserController@pruebas');
    Route::get('/entrada/pruebas','PostController@pruebas');
    Route::get('/categoria/pruebas','CategoryController@pruebas');

    //Rutas del controlador de Usuarios
    Route::post('/api/register','UserController@register');
    Route::post('/api/login','UserController@login');
    Route::put('/api/user/update','UserController@update');
    Route::post('/api/user/upload','UserController@upload')->middleware(\App\Http\Middleware\ApiAuthMiddleware::class);//indica que esta ruta utilizará un middleware
    //Route::post('api/user/upload',['middleware' => 'api.auth'],'UserController@upload');
    Route::get('/api/user/avatar/{filename}','UserController@getImage');
    Route::get('/api/user/detail/{id}','UserController@detail');

    //Rutas del controlador de Categorias
    #aquí se usarán otro tipo de rutas, llamada rutas resource
    #que son como automáticas.
    #se define una sola ruta y lo hace el resto de forma automática
    Route::resource('/api/category','CategoryController');

    //Rutas del controlador de Entradas
    Route::resource('/api/post','PostController');
    Route::post('/api/post/upload','PostController@upload');
    Route::get('/api/post/image/{filename}','PostController@getImage');
    Route::get('/api/post/category/{id}', 'PostController@getPostsByCategory');
    Route::get('/api/post/user/{id}', 'PostController@getPostsByUser');
