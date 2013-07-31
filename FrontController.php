<?php
require_once '../application/libs/Config.php';
require_once '../application/config/config.php';
require_once $config->get('libsFolder') . 'Error.php';

class FrontController{

    public static function myErrorHandler($errno, $errstr, $errfile, $errline){
    	global $config;
        switch ($errno) {
            case E_USER_ERROR:
            case E_ERROR:
                $err = "<b>My ERROR</b> [$errno] $errstr<br />\n";
                $err .= "  Fatal error on line $errline in file $errfile";
                new Error($err);
                header("location: error");
                exit();
        }

        return true;
    }

    public static function main(){
    	global $config;
        ini_set('session.gc_maxlifetime', 28800);
        ini_set('memory_limit', '34M');
        $old_error_handler = set_error_handler(array('FrontController', 'myErrorHandler'));
        require_once $config->get('libsFolder') .  'Db.php';
        require_once $config->get('libsFolder') . 'Translator.php';
        require_once $config->get('libsFolder') . 'ControllerBase.php';
        require_once $config->get('libsFolder') . 'ModelBase.php';
        require_once $config->get('libsFolder') . 'View.php';
        require_once $config->get('libsFolder') . 'Session.php';
        require_once $config->get('libsFolder') . 'Log.php';
        require_once $config->get('libsFolder') . 'Route.php';
        require_once $config->get('libsFolder') . 'FlashMessenger.php';
        require_once $config->get('libsFolder') . 'Utils.php';

        $sessionName = $config->get('sessionName') ? $config->get('sessionName') : 'haras';
        Session::setName($sessionName);
        Session::start();

        $config->set('flashMessenger', new FlashMessenger());        
        $config->set('log', new Log());
        
        if(! empty($_GET['lang']))
        	Session::add('lang', $_GET['lang']);
        
        $route = Route::getInstance();
        $route->load($config->get('configFolder') . 'routes.ini');
        $route->run();
    }
}