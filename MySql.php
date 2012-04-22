<?php
require_once 'ManejadorBdInterface.php';
require_once 'Error.php';

class MySql implements ManejadorBdInterface{
    private $_servidor;
    private $_base;
    private $_usuario;
    private $_pass;

    private $_conexion;

    public function __construct($servidor, $base, $usuario, $pass){
        $this->_servidor = $servidor;
        $this->_base = $base;
        $this->_usuario = $usuario;
        $this->_pass = $pass;
    }

    public function conectar(){      
        if(!$this->_conexion){  
            $this->_conexion = mysql_connect($this->_servidor, $this->_usuario, $this->_pass) or new Error(mysql_error($this->_conexion));
        }
        
        mysql_select_db($this->_base, $this->_conexion) or new Error(mysql_error($this->_conexion));
    }

    public function desconectar(){
        if($this->_conexion){
            mysql_close($this->_conexion);
            $this->_conexion = null;
        }
    }

    public function obtenerCampo($campo, $sql){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        $sql .= ' LIMIT 0,1';

        $resource = mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));
        if($resource){
            $registro = mysql_fetch_assoc($resource);
            mysql_free_result($resource);
            return $registro[$campo];
        }
        return false;
    }

    public function obtenerRegistro($sql){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        $sql .= ' LIMIT 0,1';
        $resource = mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));
        if($resource){
            return mysql_fetch_assoc($resource);
        }
        return false;
    }

    public function obtenerDatos($sql){
        if(!$this->_conexion){  
            $this->conectar();
        }

        $resource = mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));

        if($resource && mysql_num_rows($resource)){
            while($registro = mysql_fetch_assoc($resource)){
                $resultado[] = $registro;
            }
            mysql_free_result($resource);
            return $resultado;
        }

        return array();
    }

    public function obtenerResource($sql){
        if(!$this->_conexion){  
            $this->conectar();
        }

        $resource = mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));

        if($resource && mysql_num_rows($resource)){
            return $resource;
        }
        
        return false;
    }
    
    public function recorrer($resource){
        return mysql_fetch_assoc($resource);
    }

    public function insertarRegistro($table, $data){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        $fields = array();
        $values = array();
        $sql = '';

        foreach($data as $field => $value){
            $fields[] = $field;
            $values[] = mysql_real_escape_string($value);
        }

        $sql = "INSERT INTO {$this->_base}.{$table} (" . implode(', ', $fields) . ') ';
        $sql .= "VALUES ('" . implode("', '", $values) . "')";

        mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));

        if(mysql_affected_rows($this->_conexion) == 1){
            return mysql_insert_id($this->_conexion);
        }

        return false;
    }

    public function eliminarRegistro($table, $primaryKey, $key){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        $sql = "DELETE FROM {$this->_base}.{$table} WHERE {$primaryKey} = '{$key}'";
        return mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));
    }

    public function actualizarRegistro($table, $data, $primaryKey, $key){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        $fields = array();
        $values = array();
        $sql = '';

        foreach($data as $field => $value){
            $fields[] = "{$field} = '" . mysql_real_escape_string($value) . "'";
        }

        $sql = "UPDATE {$this->_base}.{$table} ";
        $sql .= "SET " . implode(", ", $fields) . " ";
        $sql .= "WHERE {$primaryKey} = '{$key}'";

        return mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));
    }

    public function ejecutar($sql){
        if(!$this->_conexion){  
            $this->conectar();
        }
        
        return mysql_query($sql, $this->_conexion) or new Error(mysql_error($this->_conexion));
    }
}