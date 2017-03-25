<?php
class Textos extends Controller {

	public static function index() {
		$bag = array(
			"user" => Auth::getUser(),
			"texts" => self::getAllTexts()
		);
		
		if(Auth::user())
			echo self::render("textos/index.html", $bag);
		else
			self::redirect("login");
	}

	public static function editar() {
		$id = (int)static::$app->parametros[2];

		$bag = array(
			"user" => Auth::getUser(),
			"text" => self::getTextById($id)
		);
		
		if(Auth::user())
			echo self::render("textos/editar.html", $bag);
		else
			self::redirect("login");
	}

	public static function update() {
		global $dbConn;
		$post = preprocess((object)static::$app->post);
		
		$query = "
			UPDATE text 
			SET 
				title = '{$post->title}',
				content = '{$post->content}'
			WHERE id = {$post->id}
		";
		$result = mysqli_query($dbConn, $query);
		$json = new stdclass();
		$json->success = $result;
		$json->msg = ($result === true) ? "Texto editado com sucesso!" : "Erro ao editar texto. Por favor, tente novamente.";

		die(json_encode($json));
	}

	
	private static function getAllTexts() {
		global $dbConn;
		$texts = array();

		$query = "
			SELECT *
			FROM `text`
			ORDER BY title
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$texts[]= $fetch;
		}

		return toUTF($texts);
	}

	private static function getTextById($id) {
		global $dbConn;
		
		$query = "
			SELECT *
			FROM text
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		if($result && mysqli_num_rows($result) == 1 ) {
			$fetch = mysqli_fetch_object($result);			
		}

		return toUTF($fetch);
	}

}
Textos::exec($app);