<?php
include("_lib/data.php");

class ConfiguracoesGlobais extends Controller {

	public static function index() {
		if(Auth::user() && Auth::is('admin')) {

			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("configuracoes_globais/index.html", $bag);
		}
		else self::redirect("home");
	}
}

ConfiguracoesGlobais::exec($app);