<?php
class Translator{

    private $_language;
    private $_eol = "\n";
    private $_commands = array();

    public function __construct($language){
        $this->_language = $language ? $language : 'es';
        $this->_readFile();
    }

    private function _readFile(){
        $config = Config::getInstance();
        $filename = $config->get('langFolder') . strtolower($this->_language) . '.ini';
        $content = file_get_contents($filename);
        $lines = explode($this->_eol, $content);
        
        if(is_array($lines) && count($lines) > 0){
        	foreach($lines as $line){
        		$this->_readLine($line);
        	}
        }
    }

    private function _readLine($line){
    	if($line){
    		if($line[0] != '#' && $line[0] != ';'){
    			list($key, $value) = explode('=', $line, 2);
    			$key = trim($key);
    			$value = trim($value);
    			$this->_commands[$key] = $value;
    		}
    	}
    }
    
    
    public function getLanguage(){
        return $this->_language;
    }

    public function getFieldSufix(){
        return '_' . strtolower($this->getLanguage());
    }

    public function translate($command, $params = array()){
        if(! isset($this->_commands[$command])){
            return '';
        }

        $text = $this->_commands[$command];
        $totalParams = count($params);

        if(is_array($params) && $totalParams > 0){
            for($index = 0; $index < $totalParams; $index ++){
                $text = str_replace('@PARAM' . ($index + 1), $params[$index], $text);
            }
        }
        return $text;
    }
}