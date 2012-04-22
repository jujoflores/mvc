<?php
class Config{
    private $_vars;
    private static $_instance;

    private function __construct(){
        $this->_vars = array();
    }

    //Con set vamos guardando nuestras variables.
    public function set($name, $value){
        if(! isset($this->_vars[$name])){
            $this->_vars[$name] = $value;
        }
    }

    //Con get('nombre_de_la_variable') recuperamos un valor.
    public function get($name){
        if(isset($this->_vars[$name])){
            return $this->_vars[$name];
        }
    }

    public static function singleton(){
        if(! self::$_instance){
            self::$_instance = new Config();
        }
        return self::$_instance;
    }
}