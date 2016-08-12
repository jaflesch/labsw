<?php
class CSRF {
	public static function generate($key) {
		if (!isset($_SESSION["csrf"])) {
			$_SESSION["csrf"] = array();
		}

		$csrf = strtoupper(sha1(uniqid()));

		$_SESSION["csrf"][$key] = $csrf;

		return ($csrf);
	}

	public static function validate($key, $value) {
		$valid = false;

		if (isset($_SESSION["csrf"][$key])) {
			$valid = ($_SESSION["csrf"][$key] == $value);

			//Erase the token after the validation
			unset($_SESSION["csrf"][$key]);
		}

		return ($valid);
	}
}
?>