<?php
require_once 'MySql.php';

abstract class ModelBase{
    protected $_db;
    protected $_config;
    protected $_lang;
    protected $_error;
    protected $_notice;

    public function __construct(){
        $this->_config = Config::singleton();
        $this->_error = array();
        $lang = Session::get('lang') ? Session::get('lang') : $this->_config->get('defaultLang');
         
        $this->_lang = new Translator($lang);
        $this->_db = Bd::getSingleton(
                        new MySql($this->_config->get('dbhost'), 
                                  $this->_config->get('dbname'), 
                                  $this->_config->get('dbuser'), 
                                  $this->_config->get('dbpass')
                                 )
                        );
    }
    
    public function getLang(){
        return $this->_lang;
    }
    
    public function getError(){
        return $this->_error;
    }

    public function addError($error){
        $this->_error[] = $error;
    }
    
    public function getNotice(){
        return $this->_notice;
    }

    public function addNotice($notice){
        $this->_notice[] = $notice;
    }
}
