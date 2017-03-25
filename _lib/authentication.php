<?php
class Auth {
	
	public static function getUser() {
		session_start();
		return $_SESSION['covn']['user'];
	}

	public static function setUser($user) {
		session_start();
		$_SESSION['covn']['user'] = $user;
	}
	
	public static function id() {
		session_start();
		return $_SESSION['covn']['user']->id;
	}

	public static function user() {
		session_start();
		return isset($_SESSION['covn']['user']);
	}

	public static function logout() {
		session_start();
		unset($_SESSION['covn']);
		session_destroy();
	}
}
?>