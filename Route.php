<?php

class Route {
	const ROUTE_KEY= '__route__';
	const HTTP_GET = 'GET';
	const HTTP_POST = 'POST';
	const HTTP_PUT = 'PUT';
	const HTTP_DELETE = 'DELETE';
	
	private static $_instance;
	private $_routes = array();
	private $_regexes= array();
	private $_route = null;	
	
	public static function getInstance(){
    	if(self::$_instance)
			return self::$_instance;

		self::$_instance = new Route();
    	return self::$_instance;
	}

	private function _addRoute($method, $route, $controller, $action){
		$this->_routes[] = array('httpMethod' => $method, 'path' => $route, 'controller' => $controller, 'action' => $action);
		$this->_regexes[]= "#^{$route}\$#";
	}

	private function _getRoute($route = false, $httpMethod = null){		
		$this->_route = $route ? $route : 
			(isset($_GET[self::ROUTE_KEY]) ? $_GET[self::ROUTE_KEY] : '/');

		if($httpMethod === null)
			$httpMethod = $_SERVER['REQUEST_METHOD'];
		
		foreach($this->_regexes as $ind => $regex){
				
			if(preg_match($regex, $this->_route)){				
				$def = $this->_routes[$ind];
				if($httpMethod != $def['httpMethod']){
					continue;
				}else{
					$request = $_REQUEST;
					$files = $_FILES;
					if(isset($request[self::ROUTE_KEY])){
						unset($request[self::ROUTE_KEY]);
					}
					return array('controller' => $def['controller'], 'action' => $def['action'], 'request' => $request, 'files' => $files);
				}
			}
		}
		trigger_error("Could not find route {$this->_route} from {$_SERVER['REQUEST_URI']}", E_USER_NOTICE);
		return array('controller' => 'error', 'action' => 'index', 'request' => array(), 'files' => array());
	}
	
	private function _formatMethod($method){
		switch($method){
			case 'get':
				return self::HTTP_GET;
			case 'post':
				return self::HTTP_POST;
			case 'put':
				return self::HTTP_PUT;
			case 'delete':
				return self::HTTP_DELETE;
			default:
				return false;
		}
	}

	public function load($file){
		if(!file_exists($file)){
			trigger_error("Routes config file not found", E_USER_NOTICE);
			break;
		}
		
		$parsed = parse_ini_file($file, true);
		foreach($parsed as $route){
			$method = $this->_formatMethod(strtolower($route['method']));
			if($method){
				$path = $route['path'] ? $route['path'] : '/';
				$controller = $route['controller'] ? $route['controller'] : 'index';
				$action = $route['action'] ? $route['action'] : 'index';
				$this->_addRoute($method, $path, $controller, $action);
			}
		}		
	}
	
	public function run($route = false, $httpMethod = null){
		$config = Config::getInstance();
		if($route === false){
			$route = isset($_GET[self::ROUTE_KEY]) ? $_GET[self::ROUTE_KEY] : '/';
		}
		
		if($httpMethod === null){
			$httpMethod = $_SERVER['REQUEST_METHOD'];
		}
		
		$routeDef = $this->_getRoute($route, $httpMethod);

		if(! empty($routeDef['controller'])){
			$controllerName = strtolower($routeDef['controller']);
			$controllerName[0] = strtoupper($controllerName[0]);
			$controllerName = $controllerName . 'Controller';
		}else{
			$controllerName = "IndexController";
		}
		
		if(! empty($routeDef['action']))
			$actionName = $routeDef['action'];
		else
			$actionName = "index";

		$request = $routeDef['request'];
		$files = $routeDef['files'];
		$controllerPath = $config->get('controllersFolder') . $controllerName . '.php';
		
		if(is_file($controllerPath)){
			require_once $controllerPath;
		}else{
			die("Controller {$controllerName} - 404 not found");
		}
		
		if(!is_callable(array($controllerName, $actionName))){
			trigger_error($controllerName . '->' . $actionName . '` not found', E_USER_NOTICE);
			return false;
		}
		
		$controller = new $controllerName();
		$controller->setRequest($request);
		$controller->setFiles($files);
		
		if(method_exists($controller, 'init')){
			$controller->init();
		}
		
		return $controller->$actionName();
	}	
}
