<?php
class Home extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("user/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function perfil() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("user/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function logout() {
		Auth::logout();
		self::redirect("");
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}
}

Home::exec($app);