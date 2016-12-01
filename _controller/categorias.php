<?php
include("_lib/data.php");

class Categorias extends Controller {

	public static function index() {
		if(Auth::user() && Auth::is('admin')) {

			$bag = array(
				"user" => Auth::getUser(),
				"categorias" => self::getAllCategorias()
			);
			echo self::render("categorias/index.html", $bag);
		}
		else self::redirect("home");
	}

	public static function editar() {
		if(Auth::user() && Auth::is('admin')) {

			$id = static::$app->parametros[2];
			$bag = array(
				"user" => Auth::getUser(),
				"categoria" => self::getCategoriaById($id),
				"subcategorias" => self::getAllSubcategoriasByCategoriaId($id)
			);
			echo self::render("categorias/editar.html", $bag);
		}
		else self::redirect("home");
	}

	public static function novo() {
		if(Auth::user() && Auth::is('admin')) {

			$bag = array(
				"user" => Auth::getUser()
			);
			echo self::render("categorias/novo.html", $bag);
		}
		else self::redirect("home");
	}

	// AJAX Calls::
	public static function add_subcategoria() {
		$id_user = Auth::id();
		
		$post = static::$app->post;
		$id = (int)$post['id'];
		
		$json = new stdclass();

		$query = "
			INSERT INTO subcategoria (id_categoria, nome)
			VALUES ({$id}, '{$post['nome']}')
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		$json->success = ($result)? true : false;
		$json->id = mysqli_insert_id(static::$dbConn);

		die(json_encode($json));
	}

	public static function remove_subcategoria() {
		$id_user = Auth::id();
		
		$post = static::$app->post;
		$id = (int)$post['id'];		
		$json = new stdclass();

		$query = "
			DELETE FROM subcategoria 
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		$json->success = ($result)? true : false;

		die(json_encode($json));
	}

	public static function insert() {
		$post = (object)static::$app->post;		
		$json = new stdclass();
		
		$query = "
			INSERT INTO categoria (nome)
			VALUES ('{$post->nome}')
		";
		$result = mysqli_query(static::$dbConn, $query);
		$json->success = $result;
		
		die(json_encode($json));
	}

	public static function update() {
		$post = static::$app->post;
		$id = (int)$post['id'];
		
		$json = new stdclass();

		$query = "
			UPDATE categoria
			SET nome = '{$post['nome']}'
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$json->success = $result;
		
		die(json_encode($json));
	}

	public static function delete() {
		$id = (int)static::$app->post['id'];		
		$json = new stdclass();
		
		$query = "
			DELETE FROM categoria 
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$json->success = $result;
		
		die(json_encode($json));
	}

	private static function getAllCategorias() {
		global $dbConn;
		$categorias = array();

		$query = "
			SELECT *
			FROM categoria
			ORDER BY nome
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$categorias[] = $fetch;
		}

		return toUTF($categorias);
	}

	private static function getCategoriaById($id) {
		global $dbConn;
		
		$query = "
			SELECT *
			FROM categoria
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		$fetch = mysqli_fetch_object($result);

		return toUTF($fetch);
	}

	private static function getAllSubcategoriasByCategoriaId($id) {
		global $dbConn;
		$subcategorias = array();

		$query = "
			SELECT *
			FROM categoria c 
			INNER JOIN subcategoria s ON s.id_categoria = c.id
			WHERE c.id = ${id}
			ORDER BY s.nome
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$subcategorias[] = $fetch;
		}

		return toUTF($subcategorias);
	}
}

Categorias::exec($app);