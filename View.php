<?php
class View{

    function __construct(){
    }

    public function show($name, $vars = array()){
        $config = Config::getInstance();
        $path = $config->get('viewsFolder') . $name;

        if(file_exists($path) == false){
            trigger_error("Template {$path} does not exist.", E_USER_NOTICE);
            return false;
        }
        
        if(is_array($vars)){
            foreach($vars as $key => $value){
                $this->$key = $value;
            }
        }
        
        require($path);
    }
}