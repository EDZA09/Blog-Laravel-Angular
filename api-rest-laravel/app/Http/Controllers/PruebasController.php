<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//Cargando modelos Post y Category - similar al using
use App\Post;
use App\Category;

class PruebasController extends Controller
{
    public function index() {
        $titulo = 'Animales';
        $animales = ['Perro','Gato','Tigre'];
        
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));//return view('nombrevista.métodoacceder', pasar datos a la vista);
    }
    
    public function testORM() {
        /*$posts = Post::all();//retorna todos los datos que hay en la tabla post
        foreach ($posts as $post) {//retornando solo los valores fila por fila
            echo "<h1>".$post -> title."</h1>";
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";//String con variables dentro {} similar a las fstrings
            echo "<p>".$post -> content."</p>";
            echo '<hr>';
            
        }*/
        //var_dump($posts);//Retorna los resultado con multiple información que tiene los objetos Eloquent
        
        $categories = Category::all();
        foreach ($categories as $category) {
            echo "<h1>".$category -> name."</h1>";
            
            foreach ($category -> posts as $post) {//retornando solo los valores fila por fila
            echo "<h2>".$post -> title."</h2>";
            echo "<span style='color:gray;'>{$post->user->name} - {$post->category->name}</span>";//String con variables dentro {} similar a las fstrings
            echo "<p>".$post -> content."</p>";           
        }
            echo '<hr>';
        }
        
        die();//corta la ejecución de la vista
    }
}
