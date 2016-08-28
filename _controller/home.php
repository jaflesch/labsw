<?php
class Home extends Controller {

	public static function index() {
		$bag = array();

		if(Auth::user()) {
			// debug
			//print_r(Auth::getUser());
		 	echo self::render("home/index.html", $bag);
		}
		else echo self::render("login/index.html", $bag);
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}

	public static function login() {
		$json = new stdclass();
		
		$dbConn = static::$dbConn;		
		$senha = md5($_POST['password']);		

		$query = "
			SELECT *
			FROM usuario
			WHERE senha = '{$senha}' AND login = '{$_POST['login']}'
		";

		$result = mysqli_query($dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$user = mysqli_fetch_object($result);
			
			// Security
			unset($user->senha);

			Auth::login($user);
			$json->success = true;
		}
		else {
			$json->success = false;
		}

		die(json_encode($json));
	}

	public static function logout() {
		Auth::logout();
		self::redirect("");
	}
}

Home::exec($app);