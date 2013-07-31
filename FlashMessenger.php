<?php
require_once '../application/libs/Session.php';

class FlashMessenger{

	static protected $_messages = array();
	protected $_namespace = 'flashMessenger';

    public function __construct(){
    	$this->_messages = Session::get($this->_namespace, array());
    	$this->deleteShowns();
    }

	public function addMessage($name, $message){
		$this->_messages[$name][] = $message;
		Session::add($this->_namespace, $this->_messages);
	}

	public function getMessages($name){
		$messages = Session::get($this->_namespace);
		if(isset($messages[$name]['_shown'])){
			unset($messages[$name]['_shown']);
		}
		$this->removeMessages($name);
		return $messages[$name];
	}

	public function removeMessages($name){
		$this->_messages[$name] = array();
		Session::add($this->_namespace, $this->_messages);
	}
	
	public function deleteShowns(){
		$messages = array();
		foreach($this->_messages as $key => $message){
			if($this->_messages[$key]['_shown'] <= 2){
				$messages[$key] = $message;
				$messages[$key]['_shown'] += 1;
			}
		}
		$this->_messages = $messages;
		Session::add($this->_namespace, $this->_messages);
	}
	
	public function cleanMessages(){
		Session::add($this->_namespace, array());
	}
}