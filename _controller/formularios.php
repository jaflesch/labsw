<?php
class QuemSomos extends Controller {

	public static function index() {
		self::redirect("home");
	}

	public static function inscricao() {
		echo self::render("formularios/inscricao.html", array());
	}

	private static function getAllMembers() {
		global $dbConn;
		$member = array();

		$query = "
			SELECT *
			FROM member 
		";
		$result = mysqli_query($dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$fetch->photo = ($fetch->photo == "") ? 
				"assets/img/header/logo-eco-produtiva-branco.png" : 
				"public/photo/{$fetch->id}/{$fetch->photo}";
			
			$member[] = $fetch;
		}

		return toUTF($member);
	}
}
QuemSomos::exec($app);