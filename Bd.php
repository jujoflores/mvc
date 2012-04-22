<?php
require_once 'ManejadorBdInterface.php';

class Bd{
    private static $_instancia;
    private $_manejador;

    private function __construct(ManejadorBdInterface $manejador){
        $this->_manejador = $manejador;
    }

    public function nuevaConexion(ManejadorBdInterface $manejador){
        return new Bd($manejador);
    }

    // EL metodo singleton
    public static function getSingleton(ManejadorBdInterface $manejador){
        if(! self::$_instancia){
            self::$_instancia = new Bd($manejador);
        }
        return self::$_instancia;
    }

    // Clone no permitido
    public function __clone(){
        trigger_error('Clone no se permite.', E_USER_ERROR);
    }

    public function ejecutar($sql){
        $resultado = $this->_manejador->ejecutar($sql);
        return $resultado;
    }

    public function obtenerRegistro($sql){
        $registro = $this->_manejador->obtenerRegistro($sql);
        return $registro;
    }

    public function obtenerCampo($field, $sql){
        $campo = $this->_manejador->obtenerCampo($field, $sql);
        return $campo;
    }

    public function obtenerDatos($sql){
        $datos = $this->_manejador->obtenerDatos($sql);
        return $datos;
    }

    public function insertarRegistro($table, $data){
        $registro = $this->_manejador->insertarRegistro($table, $data);
        return $registro;
    }

    public function eliminarRegistro($table, $primaryKey, $key){
        $registro = $this->_manejador->eliminarRegistro($table, $primaryKey, $key);
        return $registro;
    }

    public function actualizarRegistro($table, $data, $primaryKey, $key){
        $registro = $this->_manejador->actualizarRegistro($table, $data, $primaryKey, $key);
        return $registro;
    }
}