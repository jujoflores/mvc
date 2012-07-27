<?php
require_once 'ManejadorBdInterface.php';

class Bd{
    private static $_instance;
    private $_handler;

    private function __construct(ManejadorBdInterface $handler){
        $this->_handler = $handler;
    }

    public function nuevaConexion(ManejadorBdInterface $handler){
        return new Bd($handler);
    }

    // Singleton method
    public static function getInstance(ManejadorBdInterface $handler){
        if(! self::$_instance){
            self::$_instance = new Bd($handler);
        }
        return self::$_instance;
    }

    // Clone not allowed
    public function __clone(){
        trigger_error('Clone not allowed.', E_USER_ERROR);
    }

    public function ejecutar($sql){
        return $this->_handler->ejecutar($sql);
    }

    public function obtenerRegistro($sql){
        return $this->_handler->obtenerRegistro($sql);
    }

    public function obtenerCampo($field, $sql){
        return $this->_handler->obtenerCampo($field, $sql);
    }

    public function obtenerDatos($sql){
        return $this->_handler->obtenerDatos($sql);
    }

    public function insertarRegistro($table, $data){
        return $this->_handler->insertarRegistro($table, $data);
    }

    public function eliminarRegistro($table, $primaryKey, $key){
        return $this->_handler->eliminarRegistro($table, $primaryKey, $key);
    }

    public function actualizarRegistro($table, $data, $primaryKey, $key){
        return $this->_handler->actualizarRegistro($table, $data, $primaryKey, $key);
    }
}