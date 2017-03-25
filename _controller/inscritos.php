<?php
class Formularios extends Controller {

	public static function index() {
		$bag = array(
			"inscritos" => self::getAllInscritos()
		);
		echo self::render("inscritos/index.html", $bag);
	}

	public static function getAllInscritos() {
		global $dbConn;
		$post = (object)$_POST;

		$query = "
			SELECT *
			FROM inscrito 
			ORDER BY nome
		";
		$result = $dbConn->query($query);

		while($fetch = $result->fetch_object()) {
			$inscritos[] = $fetch;
		}

		return toUTF($inscritos);
	}
}
Formularios::exec($app);