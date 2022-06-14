<?php 
namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController {

    public static function login(Router $router) {

        $alertas =[];
   
        if($_SERVER['REQUEST_METHOD']=== 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarLogin();

            if(empty($alertas)) {
                $usuario = Usuario::where('email',$usuario->email);

                if(!$usuario || !$usuario->confirmado) {
                    Usuario::setAlerta('error','Usuario no valido o no registrado');
                } else {    
                        //elusuario existe comprobar password
                    if (password_verify($_POST['password'],$usuario->password)) {
                        //iniciar session
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        //redireccionar 

                        header('Location:/dashboard');

                    } else {
                        Usuario::setAlerta('error','Password Incorrecto');
                    }
                    
            
                } 
            }

        }   

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'titulo'=> 'Iniciar Sesión',
            'alertas'=> $alertas
        ]);
    }

    public static function logout() {
        session_start();
        $_SESSION= [];
        header('Location:/');

    }

    public static function crear(Router $router) {
        $usuario = new Usuario();

        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD']=== 'POST') {
            $usuario->sincronizar($_POST);
            $alertas=   $usuario->validarNuevaCuenta();

            
            
            if(empty($alertas)){
                $existeUsuario = Usuario::where('email',$usuario->email);

                    if($existeUsuario) {
                        Usuario::setAlerta('error','Ya esta registrado el usuario');
                        $alertas = Usuario::getAlertas();
                    } else {
                        // hashear el password
                        $usuario->hashPassword();
                        //eliminar password 2
                        unset($usuario->password2); 
                        //generar token
                        $usuario->crearToken();
                        //enviar email con token
                        $email = new Email($usuario->nombre,$usuario->email,$usuario->token);
                        $email->enviarConfirmacion();
                        
                        //crear u nuveo usuario
                        $resultado =  $usuario->guardar();
                        
                       if($resultado) {
                           header('Location: /mensaje');
                       }
                    }
            }
        }

        $router->render('auth/crear', [
            'titulo'=> 'Crear',
            'usuario'=>$usuario,
            'alertas'=>$alertas
        ]); 

    }

    public static function olvide(Router $router) {
        $alertas =[];

        if($_SERVER['REQUEST_METHOD']==='POST') {
            $auth = new Usuario($_POST);
            $alertas = $auth->validarEmail();

          if(empty($alertas)) {
              $usuario = Usuario::where('email',$auth->email);

              if($usuario && $usuario->confirmado) {

                //generar un token

                $usuario->crearToken();
                unset($usuario->password2); 
                $usuario->guardar();

                //enviar email
                $email = new Email($usuario->nombre,$usuario->email,$usuario->token);
                $email->enviarInstruciones();

                //alerta de exito 
                Usuario::setAlerta('exito', 'Se han enviado las intruciones para Reestablecer tu password');
              
              } else {
                    Usuario::setAlerta('error',"El usuario no existe o no esta confirmado");
              }
            //   debuguear($usuario);
          }

        }

        $alertas= Usuario::getAlertas();


        $router->render('auth/olvide', [
            'titulo'=> 'Olvidaste tu contraseña',
            'alertas'=>$alertas
       
        ]); 


    }

    
    public static function reestablecer(Router $router) {
        $token = s($_GET['token']);
        $mostrar= true;

        if(!$token) header('Location:/');

        $alertas= [];

        $usuario = Usuario::where('token',$token);

      if(empty($usuario)){
          Usuario::setAlerta('error','token no valido');
          $mostrar=false;
      }

        if($_SERVER['REQUEST_METHOD']=== 'POST') {
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPassword();
            if(empty($alertas)){
                $usuario->hashPassword();
                unset($usuario->password2); 
                $usuario->token = null;
                $resultado = $usuario->guardar();

                if($resultado) {
                    header('Location: /');
                }

             
            }
       
        }

        
        $alertas= Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'titulo'=> 'Reestablece tu Password',
            'alertas'=>$alertas,
            'mostrar'=>$mostrar
        ]);
        }

    public static function mensaje(Router $router) {
        

        $router->render('auth/mensaje', [
            'titulo'=> 'Cuenta Creada | Confirmala ahora'
        ]); 


        
    }

    public static function confirmar(Router $router) {

        $alertas = [];
        $token = s($_GET['token']);
        $usuario = Usuario::where('token', $token);
      
        if(empty($usuario)) {
            //mostrar mensaje de error
            Usuario::setAlerta('error','Token No Válido');
        } else {
            //usuario confirmado
            $usuario->confirmado = "1";
            $usuario->token = "";
            $usuario->guardar();
            Usuario::setAlerta('exito','Te has registrado Correctamente');
        }
        //obtener alertas
        $alertas = Usuario::getAlertas();


        $router->render('auth/confirmar', [
            'titulo'=> 'Confirmar Cuenta',
            'alertas'=>$alertas
        ]); 

    }
}
