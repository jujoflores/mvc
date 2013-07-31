<?php
require_once 'DbHandler.php';
require_once 'Error.php';

class MySql implements DbHandler{
    private $_server;
    private $_base;
    private $_user;
    private $_pass;

    private $_connection;

    public function __construct($server, $base, $user, $pass){
        $this->_server = $server;
        $this->_base = $base;
        $this->_user = $user;
        $this->_pass = $pass;
    }

    public function connect(){      
        if(!$this->_connection){  
            $this->_connection = mysql_connect($this->_server, $this->_user, $this->_pass) 
            	or new Error(mysql_error($this->_connection));
        }
        
        mysql_select_db($this->_base, $this->_connection) or new Error(mysql_error($this->_connection));
    }

    public function disconnect(){
        if($this->_connection){
            mysql_close($this->_connection);
            $this->_connection = null;
        }
    }

    public function getOneField($field, $sql){
        if(!$this->_connection){  
            $this->connect();
        }
        
        $sql .= ' LIMIT 0,1';

        $resource = mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));
        if($resource){
            $row = mysql_fetch_assoc($resource);
            mysql_free_result($resource);
            return $row[$field];
        }
        return false;
    }

    public function getOne($sql){
        if(!$this->_connection){  
            $this->connect();
        }
        
        $sql .= ' LIMIT 0,1';
        $resource = mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));
        if($resource){
            return mysql_fetch_assoc($resource);
        }
        return false;
    }

    public function getAll($sql){
        if(!$this->_connection){  
            $this->connect();
        }

        $resource = mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));

        if($resource && mysql_num_rows($resource)){
            while($row = mysql_fetch_assoc($resource)){
                $result[] = $row;
            }
            mysql_free_result($resource);
            return $result;
        }

        return array();
    }

    public function insert($table, $data){
        if(!$this->_connection){  
            $this->connect();
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

        mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));

        if(mysql_affected_rows($this->_connection) == 1){
            return mysql_insert_id($this->_connection);
        }

        return false;
    }

    public function delete($table, $primaryKey, $key){
        if(!$this->_connection){  
            $this->connect();
        }
        
        $sql = "DELETE FROM {$this->_base}.{$table} WHERE {$primaryKey} = '{$key}'";
        return mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));
    }

    public function update($table, $data, $primaryKey, $key){
        if(!$this->_connection){  
            $this->connect();
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

        return mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));
    }

    public function execute($sql){
        if(!$this->_connection){  
            $this->connect();
        }
        
        return mysql_query($sql, $this->_connection) or new Error(mysql_error($this->_connection));
    }
}