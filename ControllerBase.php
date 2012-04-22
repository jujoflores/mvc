<?php
abstract class ControllerBase{
    
    protected $_view;
    protected $_config;

    function __construct(){
        $this->_config = Config::singleton();
        $this->_view = new View();
    }
    
    protected function _redirect($location){
        header("location: {$location}");
    }
}