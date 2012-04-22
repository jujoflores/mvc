<?php
require_once '../application/libs/Error.php'; //Error

class FrontController{

    public function myErrorHandler($errno, $errstr, $errfile, $errline){
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
                $err = "<b>My ERROR</b> [$errno] $errstr<br />\n";
                $err .= "  Fatal error on line $errline in file $errfile";
                new Error($err);
                header("location: ../public/index.php?controlador=error");
                exit();
                break;
        }

        return true;
    }

    static public function main(){
        ini_set('session.gc_maxlifetime', 28800);
        $old_error_handler = set_error_handler(array('FrontController', 'myErrorHandler'));
        //Incluimos algunas clases:
        require_once '../application/libs/Config.php'; //de configuracion
        require_once '../application/libs/Bd.php'; //BD con singleton
        require_once '../application/libs/Translator.php'; //Multilenguaje
        require_once '../application/libs/ControllerBase.php'; //Clase controlador base
        require_once '../application/libs/ModelBase.php'; //Clase modelo base
        require_once '../application/libs/View.php'; //Mini motor de plantillas
        require_once '../application/config/config.php'; //Archivo con configuraciones.
        require_once '../application/libs/Session.php'; //Session
        require_once '../application/libs/Log.php'; //Logs

        //Iniciamos la sesion
        $sessionName = $config->get('sessionName') ? $config->get('sessionName') : 'portal';
        Session::setName($sessionName);
        Session::start();

        //Logs
        $config->set('log', new Log());

        //Formamos el nombre del Controlador o en su defecto, tomamos que es el IndexController
        if(! empty($_GET['controlador'])){
            $controllerName = strtolower($_GET['controlador']);
            $controllerName[0] = strtoupper($controllerName[0]);
            $controllerName = $controllerName . 'Controller';
        }else{
            $controllerName = "IndexController";
        }

        //Lo mismo sucede con las acciones, si no hay accion, tomamos index como accion
        if(! empty($_GET['accion']))
            $actionName = $_GET['accion'];
        else
            $actionName = "index";

        //Poder cambiar el idioma rapidamente
        if(! empty($_GET['lang']))
            Session::add('lang', $_GET['lang']);

        $controllerPath = $config->get('controllersFolder') . $controllerName . '.php';

        //Incluimos el fichero que contiene nuestra clase controladora solicitada
        if(is_file($controllerPath))
        {
            require_once $controllerPath;
        }else{
            die("El controlador {$controllerName} no existe - 404 not found");
        }
        //Si no existe la clase que buscamos y su accion, tiramos un error 404
        if(is_callable(array($controllerName, $actionName)) == false){
            trigger_error($controllerName . '->' . $actionName . '` no existe', E_USER_NOTICE);
            return false;
        }
        //Si todo esta bien, creamos una instancia del controlador y llamamos a la accion
        $controller = new $controllerName();

        if(method_exists($controller, 'init')){
            $controller->init();
        }

        $controller->$actionName();
    }
}