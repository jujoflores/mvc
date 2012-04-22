<?php
/**
 * Abstraccion del array $_SESSION
 *
 * Clase para la utilizacion de las sessiones permitiendo agregar creterios de seguridad
 *
 * @version 1.0
 * @author Federico Lozada Mosto
 * @copyright Copyright (c) 2009
 * @static
 * @final
 */
final class Session{
    private static $token = '';
    private static $httpUserAgent;
    private static $name = '';

    /**
     * Inicializo la sesion
     * @return boolean
     */
    static public function start(){
        self::generateToken();
        
        //obtengo la session vieja
        session_start();
        //regenero el id de la session y borro el archivo de la session
        //session_regenerate_id(true);
        //guardo el id nuevo
        $id_new = session_id();
        //libero las sessiones
        session_write_close();
        //recargo la session nueva
        session_id($id_new);
        session_start();
        
        return self::validSession();
    }

    /**
     * Cierro la session
     * @return boolean
     */
    static public function close(){
        self::start();
        $_SESSION[self::$name] = array();
        unset($_SESSION[self::$name]);
        session_write_close();
        return @session_destroy();
    }

    /**
     * Asigna el nombre del arreglo principal de la sesion
     * @param string $name
     * @return void
     */
    static public function setName($name){
        self::$name = $name;
    }

    /**
     * Verifica que una variable de session exista
     * @param string $name
     * @return boolean
     */
    static private function exist($name){
        return isset($_SESSION[self::$name][$name]);
    }

    /**
     * Agrega una nueva variable de session al array $_SESSION
     * @param string $name
     * @param mixed $valor
     * @return boolean
     */
    static public function add($name, $valor){
        $_SESSION[self::$name][$name] = $valor;
        return true;
    }

    /**
     * Devuelve la varible de session requerida
     * @param string $name
     * @param string $default
     * @return mixed
     */
    static public function get($name, $default = ''){
        return self::exist($name) ? $_SESSION[self::$name][$name] : $default;
    }

    /**
     * Borra una variable de session
     * @param string $name
     * @return boolean
     */
    static public function borrar($name){
        unset($_SESSION[self::$name][$name]);
        return true;
    }

    /**
     * Borro todas las variables de session
     * @return boolean
     */
    static public function refresh(){
        $_SESSION[self::$name] = array();
        self::setTokensSession();
        return true;
    }

    /**
     * Valida que la session sea genuina y que sea la que estamos usando
     * @return boolean
     */
    static private function validSession(){
        //verifico si el token es el mismo
        if(isset($_SESSION[self::$name]['token']) 
            && $_SESSION[self::$name]['token'] != self::$token){
            return false;
        }
        //otra validación
        if(isset($_SESSION[self::$name]['HTTP_USER_AGENT']) 
            && $_SESSION[self::$name]['HTTP_USER_AGENT'] != self::$httpUserAgent){
            return false;
        }
        //esta todo ok =&gt; seteo los parametros iniciales
        self::init();
        
        //seteo los tokens que se van a utilizar
        self::setTokensSession();
        return true;
    }

    static private function setTokensSession(){
        $_SESSION[self::$name]['token'] = self::$token;
        $_SESSION[self::$name]['HTTP_USER_AGENT'] = self::$httpUserAgent;
        return true;
    }

    /**
     * Inicializo ciertos parametros
     * @return boolean
     */
    static private function init(){
        ini_set("session.name", date("YFjzl"));
        ini_set("session.gc_maxlifetime", 28800); //60*60*8 = 28800 =&gt; 8 horas
        ini_set("session.cookie_lifetime", 28800); //60*60*8 = 28800 =&gt; 8 horas
        ini_set("session.cache_limiter", "nocache"); //No guardo el cache
        ini_set("session.gc_probability", 50); //defino la probabilidad de que pase el garbage para q elimine las sessiones caducadas
        
        if(! isset($_SESSION[self::$name])){
            $_SESSION[self::$name] = array();
        }
        
        return true;
    }

    /**
     * Obtengo que browser posee el cliente y lo encodeo en md5 (esto se va a utilizar como uno de los token de verificación)
     * @return string
     */
    static private function getHttpUserAgent(){
        self::$httpUserAgent = md5($_SERVER['HTTP_USER_AGENT']);
        return self::$httpUserAgent;
    }

    /**
     * Obtengo el token que se utilizada para validar la session
     * @return string
     */
    static private function generateToken(){
        self::$token = sha1(date("FlzYj") . self::getHttpUserAgent() . self::getIp());
        return true;
    }

    /**
     * Obtengo la ip del cliente
     * @return string
     */
    private static function getIp(){
        return (getenv("HTTP_X_FORWARDED_FOR")) ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
    }
}
