<?php
class Auth {
	
	public static function getUser() {
		session_start();
		return $_SESSION['user'];
	}

	public static function user() {
		session_start();
		return isset($_SESSION['user']);
	}
	public static function login($user) {
		session_start();
		$_SESSION['user'] = $user;
	}

	public static function logout() {
		session_start();
		unset($_SESSION['user']);
		session_destroy();
	}
}
?>