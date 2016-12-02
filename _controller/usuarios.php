<?php
class Usuarios extends Controller {

	public static function index() {
		if(Auth::user() && Auth::is('admin')) {
			$bag = array(
				"user" => Auth::getUser(),
				"usuarios" => self::getAllUsers()
			);
			echo self::render("usuarios/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function novo() {
		if(Auth::user() && Auth::is('admin')) {
			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("usuarios/novo.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function editar() {
		if(Auth::user() && Auth::is('admin')) {
			
			$id = (int)static::$app->parametros[2];
			$bag = array(
				"user" => Auth::getUser(),
				"usuario" => self::getUsuarioById($id)
			);
			
			echo self::render("usuarios/editar.html", $bag);
		}
		else echo self::redirect("");
	}

	private static function getAllUsers() {
		global $dbConn;
		$usuarios = array();

		$query = "
			SELECT id, nome, login, nivel_privilegios
			FROM usuario
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$fetch->funcao = self::getFuncao($fetch->nivel_privilegios);
			$usuarios[] = $fetch;
		}
		return toUTF($usuarios);
	}

	private static function getUsuarioById($id) {
		global $dbConn;
		$query = "
			SELECT id, nome, login, nivel_privilegios
			FROM usuario
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		$fetch = mysqli_fetch_object($result);
		
		return toUTF($fetch);
	}

	private static function getFuncao($nivel) {
		switch($nivel) {
			case 0: return "Administrador";
			case 1: return "Usuário";
			case 2: return "Visitante";
		}
	}
}

Usuarios::exec($app);
