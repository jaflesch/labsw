<?php
class Home extends Controller {

	public static function index() {
		static::auth();

		$bag = array();

		echo self::render("home/index.html", $bag);
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}
}

Home::exec($app);