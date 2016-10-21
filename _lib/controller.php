<?php

class Controller {
	public static $app;
	public static $config;
	public static $dbConn;

	public static function redirect($url) {
		$base_url = static::$app->base_url;

		header("Location: {$base_url}{$url}");
		exit(0);
	}

	public static function exec($app) {
		static::$app = $app;
		static::$config = $app->config;
		static::$dbConn = $app->dbConn;
		
		if ($app->action=='') $app->action = $app->default_action;		

		$action = str_replace("-", "_", $app->action);
		if (!method_exists(get_called_class(), $action)) $action = $app->default_action;
		static::$action();
	}

	public static function traduzObj($obj,$vars) {
		if (is_object($obj)) {
			foreach((array)$obj as $key=>$value) {
				$obj->{$key} = static::traduzObj($value, $vars);
			}
		} else if (is_array($obj)) {
			foreach($obj as $key=>$value) {
				$obj[$key] = static::traduzObj($value, $vars);
			}
		} else if (is_string($obj)) {
			$loader = new Twig_Loader_Array(array(
				'index.html' => $obj
			));
			$twig = new Twig_Environment($loader, array(
				'autoescape' => false,
				'auto_reload' => true
			));
			$obj = $twig->render('index.html',$vars);
		}

		return $obj;
	}

	public static function showJson($obj,$vars = array()) {
		header("Content-type: application/json");

		$app = static::$app;

		$vars['app'] = static::$app;
		$vars['config'] = static::$config;
		$vars['traducao'] = array();
		$vars['post'] = static::$app->post;
		$vars['get'] = static::$app->get;
		$vars['session'] = $_SESSION;

		echo json_encode(static::traduzObj($obj,$vars));
		
	}

	public static function render($tpl = "index.html", $vars = array()) {
		$loader = new Twig_Loader_Filesystem(static::$app->base_path.'/_view');
		$twig = new Twig_Environment($loader,array(
			'cache' => static::$app->debug?false:$app->base_path.'/_cache',
			'auto_reload' => true
		));

		$app = static::$app;
		$vars['app'] = static::$app;
		$vars['config'] = static::$config;
		
		return $twig->render($tpl,$vars);
	}
}






