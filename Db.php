<?php
require_once 'DbHandler.php';

class Db{
    private static $_instance;
    private $_handler;

    private function __construct(DbHandler $handler){
        $this->_handler = $handler;
    }

    public function newConnection(DbHandler $handler){
        return new Db($handler);
    }

    public static function getInstance(DbHandler $handler){
        if(! self::$_instance){
            self::$_instance = new Db($handler);
        }
        return self::$_instance;
    }

    public function __clone(){
        trigger_error('Clone not allowed.', E_USER_ERROR);
    }

    public function execute($sql){
        return $this->_handler->execute($sql);
    }

    public function getOne($sql){
        return $this->_handler->getOne($sql);
    }

    public function getOneField($field, $sql){
        return $this->_handler->getOneField($field, $sql);
    }

    public function getAll($sql){
        return $this->_handler->getAll($sql);
    }

    public function insert($table, $data){
        return $this->_handler->insert($table, $data);
    }

    public function delete($table, $primaryKey, $key){
        return $this->_handler->delete($table, $primaryKey, $key);
    }

    public function update($table, $data, $primaryKey, $key){
        return $this->_handler->update($table, $data, $primaryKey, $key);
    }
}