<?php
class Auth {
	
	public static function getUser() {
		session_start();
		return $_SESSION['user'];
	}

	public static function id() {
		session_start();
		return $_SESSION['user']->id;
	}

	public static function user() {
		session_start();
		return isset($_SESSION['user']);
	}
	public static function login($user) {
		session_start();
		$_SESSION['user'] = $user;
		$_SESSION['user']->funcao = self::getFuncao($_SESSION['user']->nivel_privilegios);
	}

	public static function logout() {
		session_start();
		unset($_SESSION['user']);
		session_destroy();
	}

	public static function is($role) {
		session_start();
		switch(strtolower($role)) {
			case 'adm':
			case 'admin':
			case 'a':
				$nivel = 0;
				break;

			case 'user':
			case 'usuario':
			case 'u':
			case 'dev':
				$nivel = 1;
				break;

			case 'guest':
			case 'visitor':
			case 'visitante':
			case 'v':
				$nivel = 2;
				break;
		}

		$user = self::getUser();
		return $user->nivel_privilegios == $nivel;
	}

	private static function getFuncao($nivel) {
		switch($nivel) {
			case 0: return "Administrador";
			case 1: return "Usuário";
			case 2: return "Visitante";
		}
	}
}
?>