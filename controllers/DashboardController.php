<?php 

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController {
    public static function index(Router $router) {
        
        session_start();
        isAuth();
        
        $id=$_SESSION['id'];

        $proyectos = Proyecto::belongsTo('propietarioId',$id);

       
        
        $router->render('dashboard/index',[
            'titulo'=>'Proyectos',
            'proyectos'=> $proyectos
        ]);
    }


     public static function crear_proyecto(Router $router) {
        session_start();
        isAuth();
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $proyecto = new Proyecto($_POST);

            // validación
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)) {
                // Generar una URL única 
                $hash = md5(uniqid());
                $proyecto->url = $hash;

                // Almacenar el creador del proyecto
                $proyecto->propietarioId = $_SESSION['id'];

                // Guardar el Proyecto
                $proyecto->guardar();

                // Redireccionar
                header('Location: /proyecto?id=' . $proyecto->url);

            }
        }
        $alertas = Proyecto::getAlertas();

        $router->render('dashboard/crear-proyecto',[
            'titulo'=>'Crear Proyecto',
            'alertas'=>$alertas
        ]);
    }

    public static function proyecto(Router $router) {
        session_start();
        isAuth();

        $token=$_GET['id'];

        if(!$token) header('Location: /dashboard');
        //revisar que la personas que visita el proyecto es quien lo creo
            $proyecto = Proyecto::where( 'url',$token);
            if($proyecto->propietarioId !== $_SESSION['id']) {
                header('Location: /dashboard');
            };
         

        $router->render('dashboard/proyecto',[
            'titulo'=>$proyecto->proyecto
        ]);
    }


    public static function perfil(Router $router){
        session_start();
        isAuth();

        $usuario = Usuario::find($_SESSION['id']);
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validar_perfil();
            
            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email',$usuario->email);

                if($existeUsuario && $existeUsuario->id !== $usuario->id) {
                    Usuario::setAlerta('error','Ya Existe un Usuarion Con ese Email'); 
                    $alertas = $usuario->getAlertas();
                }else {
                    $usuario->guardar();

                    Usuario::setAlerta('exito','Guardado Correctamente'); 
                    $alertas = $usuario->getAlertas();
    
                    $_SESSION['nombre'] = $usuario->nombre;
                }

              
            }
        
        }


     
        
        $router->render('dashboard/perfil',[
            'titulo'=>'Perfil',
            'alertas'=>$alertas,
            'usuario'=> $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = Usuario::find($_SESSION['id']);
            $usuario ->sincronizar($_POST);

           $alertas = $usuario->nuevo_password();

           if(empty($alertas)) {
            $resultado = $usuario->comprobar_password();

            if($resultado) {
                $usuario->password = $usuario->password_nuevo;
                unset($usuario->password_actual);
                unset($usuario->password_nuevo);
                $usuario->hashPassword();
                $resultado = $usuario->guardar();
                
                if($resultado) {
                    Usuario::setAlerta('exito','Password Guardado Correctamente'); 
                    $alertas = $usuario->getAlertas();  
                }
            

            } else {
                Usuario::setAlerta('error','El password incorrecto');
                $alertas = $usuario->getAlertas();   
            }
           }
        }
         
        $router->render('dashboard/cambiar-password',[
            'titulo'=>'Cambiar Password',
            'alertas'=>$alertas
          
        ]);
    }
}