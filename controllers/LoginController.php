<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{

    public static function login(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'Usuario no existe o No está confirmado');
                } else{
                    // El usuario existe y está confirmado
                    if(password_verify($_POST['password'], $usuario->password)){

                        // Iniciar la Sesión del usuario
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('Location: /dashboard');
                    } else{
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){

        session_start();

        $_SESSION = [];

        header('Location: /');
    }

    public static function crear(Router $router){

        //Intanciando un nuevo Usuario
        $usuario = new Usuario;

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);

            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya está registrado');
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hashear el Password
                    $usuario->hashPassword();

                    // Eliminar Password2
                    unset($usuario->password2);

                    // Generar el Token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado = $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);

                    $email->enviarConfirmacion();

                    if($resultado){
                        header('Location: /mensaje');
                    }
            }
        }
    }
        // Render a la vista
        $router->render('auth/crear',[
            'titulo' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar un nuevo Token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actializar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                } else{
                    Usuario::setAlerta('error', 'El Usuario no existe o no está confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/olvide',[
            'titulo' => 'Olvide mi contraseña',
            'alertas' => $alertas
        ]);
    }

    public static function reestablecer(Router $router){

        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token){
            header('Location: /');
        }

        // Identificar el usuario con este Token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){

            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                // Hashear el nuevo password
                $usuario->hashPassword();

                // Eliminar el Token
                $usuario->token = '';

                // Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado){
                    header('Location: /');
                }
            }

        }
        
        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/reestablecer',[
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){

        // Render a la vista
        $router->render('auth/mensaje',[
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);

    }

    public static function confirmar(Router $router){

        $token = s($_GET['token']);

        if(!$token){
            header('Location: /');
        }

        // Encontrar al usuario con ese token
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            // No se encontró un usuario con ese token
            Usuario::setAlerta('error', 'Token no válido');
        } else{
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = "";
            unset($usuario->password2);

            // Guardar en la base de datos
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta comprobada correctamente');
        }

        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/confirmar',[
            'titulo' => 'Confirma tu cuenta UpTask',
            'alertas' => $alertas
        ]);
    }


}

?>