<?php
class Contato extends Controller {

	public static function index() {
		static::auth();

		$bag = array();

		echo self::render("contato/index.html", $bag);
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}
}

Contato::exec($app);