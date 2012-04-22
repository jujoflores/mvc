<?php
interface ManejadorBdInterface{

    public function conectar();

    public function desconectar();

    public function obtenerCampo($field, $sql);

    public function obtenerRegistro($sql);

    public function obtenerDatos($sql);

    public function insertarRegistro($table, $data);

    public function eliminarRegistro($table, $primaryKey, $key);

    public function actualizarRegistro($table, $data, $primaryKey, $key);

    public function ejecutar($sql);
}