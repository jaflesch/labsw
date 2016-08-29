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
				"user" => Auth::getUser(),
				"trofeu" => self::getTrophyCountFromUser(Auth::id())
			);
			echo self::render("user/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function senha() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("user/senha.html", $bag);
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

	private static function getTrophyCountFromUser($id) {
		$trophies = array();

		$query = "
			SELECT count(*) total 
			FROM `trofeu_usuario` tu 
			INNER JOIN trofeu t ON t.id = tu.id_trofeu 
			WHERE tu.id_usuario = {$id} AND t.categoria = 1
		";
		$result = mysqli_query(static::$dbConn, $query);
		$trophies['bronze'] = mysqli_fetch_object($result);

		$query = "
			SELECT count(*) total 
			FROM `trofeu_usuario` tu 
			INNER JOIN trofeu t ON t.id = tu.id_trofeu 
			WHERE tu.id_usuario = {$id} AND t.categoria = 2
		";
		$result = mysqli_query(static::$dbConn, $query);
		$trophies['prata'] = mysqli_fetch_object($result);

		$query = "
			SELECT count(*) total 
			FROM `trofeu_usuario` tu 
			INNER JOIN trofeu t ON t.id = tu.id_trofeu 
			WHERE tu.id_usuario = {$id} AND t.categoria = 3
		";
		$result = mysqli_query(static::$dbConn, $query);
		$trophies['ouro'] = mysqli_fetch_object($result);

		$query = "
			SELECT count(*) total 
			FROM `trofeu_usuario` tu 
			INNER JOIN trofeu t ON t.id = tu.id_trofeu 
			WHERE tu.id_usuario = {$id} AND t.categoria = 4
		";
		$result = mysqli_query(static::$dbConn, $query);
		$trophies['platina'] = mysqli_fetch_object($result);

		return $trophies;
	}
}

Home::exec($app);