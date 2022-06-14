<?php 

namespace Model;

class Usuario extends ActiveRecord {

    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id','nombre','email','password','token','confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password; 
    public $token;
    public $confirmado;  


    public  function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    public function validarLogin() {
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'EMAIL NO VALIDO';
        }
        
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }



    public function validarNuevaCuenta()
    {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password es minimo de 6 Caracteres';
        }

        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'El Password debe coincidir';
        }
            
        return self::$alertas;

    }

    public function validar_perfil() {
        if(!$this->nombre){
            self::$alertas['error'][] = 'El Nombre es obligatorio';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }
        
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'EMAIL NO VALIDO';
        }

        return self::$alertas;
    }

    public function hashPassword() {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken() : void {
        $this->token = uniqid();
    }

    public function validarEmail() {
        if(!$this->email) {
            self::$alertas['error'][] = 'Debes ingresar un Email para Reestablecer tu password';
        }   

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'EMAIL NO VALIDO';
        }
        
        return self::$alertas;
    }

    public function validarPassword() : array {
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El Password es minimo de 6 Caracteres';
        }

                    
        return self::$alertas;
    }

    public  function nuevo_password() : array   {
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

          if(!$this->password_nuevo){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'El Password es minimo de 6 Caracteres';
        }

        return self::$alertas;
    }

    public function comprobar_password() : bool {
        return password_verify($this->password_actual, $this->password);
    }
}