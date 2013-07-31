<?php
interface DbHandler{

    public function connect();

    public function disconnect();

    public function getOneField($field, $sql);

    public function getOne($sql);

    public function getAll($sql);

    public function insert($table, $data);

    public function delete($table, $primaryKey, $key);

    public function update($table, $data, $primaryKey, $key);

    public function execute($sql);
}