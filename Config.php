<?php
class Config{
    private $_vars;
    private static $_instance;

    private function __construct(){
        $this->_vars = array();
    }

    public function set($name, $value){
        if(! isset($this->_vars[$name])){
            $this->_vars[$name] = $value;
        }
    }

    public function get($name){
        if(isset($this->_vars[$name])){
            return $this->_vars[$name];
        }
    }

    public static function getInstance(){
        if(! self::$_instance){
            self::$_instance = new Config();
        }
        return self::$_instance;
    }
}