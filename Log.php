<?php
class Log{
    private $_fileName;
    private $_path;

    private function _setPath(){
        $this->_path = '../log/';
        $this->_fileName = '01' . date('m') . date('Y') . '.log';
        $fileName = $this->_path . $this->_fileName;

        if(!is_file($fileName)){
            if(!is_dir($this->_path)){
                mkdir($this->_path);
                chmod($this->_path, 0777);
            }

            touch($fileName);
            chmod($fileName, 0777);
        }

        if(!is_file($fileName)){
            new Error('Can not create the log file');
        }
    }

    public function write($action, $detail = ''){
        $string = '';
        $string .= date('d/m/Y H:i:s') . "\t";
        $string .= Session::get('nombre') . "\t";
        $string .= $action . "\t";
        $string .= $detail . "\n";
        $this->_setPath();

        file_put_contents($this->_path . $this->_fileName, $string, FILE_APPEND);
    }
}