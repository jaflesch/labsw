<?php
require_once('../config.php');
$config->base_path = dirname(__FILE__);

ini_set("display_errors", "1");
error_reporting(E_ALL);

class Handlers {
    public static $instance = null;
    public function onError($type, $message, $file = null, $line = null, $context = null) {
        $backtrace = debug_backtrace();
        array_shift($backtrace);
 
        $this->notify($message, $backtrace);
        return true;
    }

    public function onException(Exception $exception) {
        $this->notify($exception->getMessage().' in '.$exception->getFile().' on line '.$exception->getLine(),$exception->getTrace());
    }

    public function onShutdown() {
        if (!$error = error_get_last()) {
            return;
        }
 
        // Build a fake backtrace, so we at least can show where we came from.
        $backtrace = array(
            array(
                'file' => $error['file'],
                'line' => $error['line'],
                'function' => '',
                'args' => array(),
            )
        );
        var_dump($backtrace);
 
        $this->notify('[Improper Shutdown] '.$error['message'], $backtrace);
    }

    public function notify($message,$backtrace) {
        $notice = new stdclass();
        $notice->message = $message;
        $notice->backtrace = $backtrace;
    }

    public static function start(){
        if ( !isset(self::$instance)) {
            self::$instance = new self($client, $notifyOnWarning);
 
            set_error_handler(array(self::$instance, 'onError'));
            set_exception_handler(array(self::$instance, 'onException'));
            register_shutdown_function(array(self::$instance, 'onShutdown'));
        }
 
        return self::$instance;
    }
}

Handlers::start();

try {
	require_once('../_lib/string.php');
	require_once('../_lib/controller.php');
	require_once('../_lib/recaptchalib.php');
	require_once('../_lib/Twig/Autoloader.php');
	require_once("../_lib/Traducao.php");
	require_once("../_lib/authentication.php");

	//Initiate Template
	Twig_Autoloader::register();

	//Initiate the database
	$dbConn = @mysqli_connect($config->db->host, $config->db->user, $config->db->pass, $config->db->name);

	//Initiate session
	session_name($config->session_name);
	session_start();

	// filtra sessão
	if (!isset($_SESSION['user_agent'])) {
		$_SESSION['user_agent'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	} 
	else {
		if (!isset($_SERVER['HTTP_USER_AGENT']) || $_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
			session_destroy();
			$_SESSION = array();				
		}
	}

	// Filtra post
	$post = xssFilter($_POST);
	$get = xssFilter($_GET);

	$path_info = isset($_SERVER['PATH_INFO'])?trim($_SERVER['PATH_INFO'],'/'):'';
	$parametros =  explode('/',$path_info);	

	if (isset($_SERVER['HTTP_HOST'])) {
		$base_url = isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		$base_url .= '://' . $_SERVER['HTTP_HOST'];
		$base_url .= str_replace(basename($_SERVER['SCRIPT_NAME']), '', $_SERVER['SCRIPT_NAME']);
	} 
	else {
		$base_url = 'http://localhost/';
	}

	if (!isset($_SESSION['idioma'])) {
		$_SESSION['idioma'] = $config->default_idioma;
	}

	$idioma = $_SESSION['idioma'];

	$app = new stdclass();
	$app->post = $post;
	$app->get = $get;
	$app->path_info = $path_info;
	$app->parametros = $parametros;
	$app->base_url = $base_url;
	$app->config = $config;
	$app->dbConn = $dbConn;
	$app->twig = $twig;
	$app->metadata = $metadata;
	$app->idioma = $idioma;
	$app->request_uri = $_SERVER["REQUEST_URI"];
	
	$app->base_path = $config->base_path;
	$app->debug = $config->debug;
	
	$controller = array_shift($parametros);

	if ($controller == '') {
		$controller = $config->default_controller;
	}

	$controller_path = "_controller/{$controller}.php";

	if (!file_exists($controller_path)) {
		$controller_path = "_controller/404.php";
	}

	$action = array_shift($parametros);

	if ($action == '') {
		$action = $config->default_action;
	}

	$app->controller = $controller;
	$app->action = str_replace("-", "_", $action);

	require ($controller_path);

} 
catch (Exception $e) {
	if ($config->debug) {
		print_r($e->getMessage());
		print_r($e->getLine());
		print_r($e->getTrace());
	}
}