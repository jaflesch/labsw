<?php
class Home extends Controller {

	public static function index() {
		//$_SESSION['user'] = "abc";
		
		$bag = array();

		if(isset($_SESSION['user']))
			echo self::render("login/index.html", $bag);
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}
}

Home::exec($app);