<?php
final class Session{
    private static $_token = '';
    private static $_httpUserAgent;
    private static $_name = '';

    private static function _getHttpUserAgent(){
    	self::$_httpUserAgent = md5($_SERVER['HTTP_USER_AGENT']);
    	return self::$_httpUserAgent;
    }
    
    private static function _getIp(){
    	return (getenv("HTTP_X_FORWARDED_FOR")) ? getenv("HTTP_X_FORWARDED_FOR") : getenv("REMOTE_ADDR");
    }
    
    private static function _generateToken(){
    	self::$_token = sha1(date("FlzYj") . self::_getHttpUserAgent() . self::_getIp());
    	return true;
    }
    
    private static function _exist($name){
        return isset($_SESSION[self::$_name][$name]);
    }

    private static function _validateSession(){
        if(isset($_SESSION[self::$_name]['token']) 
            && $_SESSION[self::$_name]['token'] != self::$_token){
            return false;
        }
        if(isset($_SESSION[self::$_name]['HTTP_USER_AGENT']) 
            && $_SESSION[self::$_name]['HTTP_USER_AGENT'] != self::$_httpUserAgent){
            return false;
        }
        self::_init();
        self::_setTokensSession();
        return true;
    }

    private static function _setTokensSession(){
        $_SESSION[self::$_name]['token'] = self::$_token;
        $_SESSION[self::$_name]['HTTP_USER_AGENT'] = self::$_httpUserAgent;
        return true;
    }

    private static function _init(){
        ini_set("session.name", date("YFjzl"));
        ini_set("session.gc_maxlifetime", 28800); //60*60*8 = 28800 =&gt; 8 hours
        ini_set("session.cookie_lifetime", 28800); //60*60*8 = 28800 =&gt; 8 hours
        ini_set("session.cache_limiter", "nocache");
        ini_set("session.gc_probability", 50);
        
        if(! isset($_SESSION[self::$_name])){
            $_SESSION[self::$_name] = array();
        }
        
        return true;
    }
    
    public static function start(){
        self::_generateToken();
        session_start();
        $id_new = session_id();
        session_write_close();
        session_id($id_new);
        session_start();        
        return self::_validateSession();
    }

    public static function close(){
        self::start();
        $_SESSION[self::$_name] = array();
        unset($_SESSION[self::$_name]);
        session_write_close();
        return @session_destroy();
    }

    public static function setName($name){
        self::$_name = $name;
    }

    public static function add($name, $valor){
        $_SESSION[self::$_name][$name] = $valor;
        return true;
    }

    public static function get($name, $default = ''){
        return self::_exist($name) ? $_SESSION[self::$_name][$name] : $default;
    }

    public static function borrar($name){
        unset($_SESSION[self::$_name][$name]);
        return true;
    }

    public static function refresh(){
        $_SESSION[self::$_name] = array();
        self::_setTokensSession();
        return true;
    }
}
