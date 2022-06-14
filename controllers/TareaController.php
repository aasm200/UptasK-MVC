<?php  //cuando consultas una api no necesitas llamar el router

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController {
    
    public static function index() {

  
        $proyectoId = $_GET['id']; // tomamos la id de la url , es decir leemos la url y vemos que tiene dentro 
       
        if(!$proyectoId) header('Location: /dashboard');  // requerimos que exista un proyecto id en el index si no existe reedirecionamos al dashboard
       
        $proyecto = Proyecto::where('url', $proyectoId); // consulta base de datos para traer los proyectos
       
        session_start();

        if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) header('Location: /404');  /* si no encuentra un proyecto o su id de session no coincide con el de proyecto reedirecionamos a 404, sirve para verificar que un usuario no pueda ingresar a otros proyectos de otros usuarios, es decir si se pasan las validaciones es por que el proyecto existe y yo soy la persoba que lo creo*/

        $tareas = Tarea::belongsTo('proyectoId', $proyecto->id);

        echo json_encode(['tareas' => $tareas]); //pasamos el arreglo a json para poder comunicarnos con javascrit mediante un Json y posteriormente podamos mostrar los resultados en la vista
    }

    public static function crear() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();
            // busco y valido que la tarea agregada sea de un proyecto que exista en mi base de datos y sea del usuario   
            $proyectoId = $_POST['proyectoId'];

            $proyecto = Proyecto::where('url', $proyectoId);
             
            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) {
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un Error al agregar la tarea'
                ];
                echo json_encode($respuesta);
                return;
            } 
            //todo bien instacinar ya agregar la tarea a nuestra base de datos
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo'=>'exito',
                'id'=>$resultado['id'],
                'mensaje'=>'Tarea creada correctamente',
                'proyectoId'=>$proyecto->id
            ];
           
            echo json_encode($respuesta);
        }
    }
    public static function actualizar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
           //validar que el proyecto exista
           $proyecto = Proyecto::where('url',$_POST['proyectoId']); //buscamos que exista
            
           session_start();

           if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) { // la misma validacion de crear
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'Hubo un Error al actulizar la tarea'
            ];
            return;
        } 
            
            $tarea = new Tarea($_POST);
            $tarea->proyectoId = $proyecto->id;
            $resultado = $tarea->guardar();
                if($resultado) {
                    $respuesta = [
                        'tipo'=>'exito',
                        'id'=>$tarea->id,
                        'proyectoId'=>$proyecto->id,
                        'mensaje' =>'Actulizado correctamente'
                    ];
                    echo json_encode(['respuesta'=>$respuesta]);
                }

        }
    }
    public static function eliminar() {
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
             //validar que el proyecto exista
           $proyecto = Proyecto::where('url',$_POST['proyectoId']); //buscamos que exista
            
           session_start();

            if(!$proyecto || $proyecto->propietarioId !== $_SESSION['id']) { // la misma validacion de crear
            $respuesta = [
                'tipo' => 'error',
                'mensaje' => 'Hubo un Error al eliminar la tarea'
                ];
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();

            $resultado = [
                'resultado'=>$resultado,
                'mensaje'=> 'Eliminado Correctamente',
                'tipo'=>'exito'
            ];

        }
        echo json_encode($resultado);
    }
}