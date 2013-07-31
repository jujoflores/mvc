<?php
abstract class ControllerBase{
    
    protected $_view;
    protected $_config;
    protected $_request;
    protected $_files;

    function __construct(){
        $this->_config = Config::getInstance();
        $this->_view = new View();
    }
    
    protected function _redirect($location){
        header("location: {$location}");
        exit;
    }
    
    public function setRequest($request){
    	$this->_request = $request; 
    }
    
    public function getRequest(){
    	return $this->_request;
    }
    
    public function getRequestByName($name){
    	return isset($this->_request[$name]) ? $this->_request[$name] : false;
    }
    
    public function setFiles($files){
    	$this->_files = $files;
    }
    
    public function getFiles(){
    	return $this->_files;
    }

    public function getFilesByName($name){
    	return isset($this->_files[$name]) ? $this->_files[$name] : false;
    }
    
}