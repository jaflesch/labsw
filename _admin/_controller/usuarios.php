<?php
require '../_lib/data.php';
class Usuarios extends Controller {

	public static function index() {
		$bag = array(
			"user" => Auth::getUser(),
			"users" => self::getAllUsers()
		);
		
		if(Auth::user())
			echo self::render("usuarios/index.html", $bag);
		else
			self::redirect("login");
	}

	public static function criar() {
		$bag = array(
			"user" => Auth::getUser()
		);
		
		if(Auth::user())
			echo self::render("usuarios/criar.html", $bag);
		else
			self::redirect("login");
	}

	public static function editar() {
		$id = (int)static::$app->parametros[2];

		$bag = array(
			"user" => Auth::getUser(),
			"user_info" => self::getUserById($id)
		);
		
		if(Auth::user())
			echo self::render("usuarios/editar.html", $bag);
		else
			self::redirect("login");
	}
	
	public static function insert() {
		global $dbConn;
		$post = preprocess((object)static::$app->post);
		
		$json = new stdclass();
		if($post->senha != $post->senha2) {
			$json->success = false;
			$json->msg = "As duas senhas preenchidas estão diferentes!";
			die(json_encode($json));
		}
		if(!self::isUniqueLogin($post->login)) {
			$json->success = false;
			$json->msg = "Já existe um usuário com este login. Por favor, escolha outro.";
			die(json_encode($json));
		}
		if(!self::isUniqueEmail($post->email)) {
			$json->success = false;
			$json->msg = "Já existe um usuário com este e-mail. Por favor, escolha outro.";
			die(json_encode($json));
		}
		
		$password = hash('sha512', $post->senha);
		$query = "
			INSERT INTO user (login, email, password) VALUES ('{$post->login}', '{$post->email}', '{$password}')
		";
		$result = mysqli_query($dbConn, $query);

		$json->success = $result;
		$json->msg = ($result === true)? "Usuário criado com sucesso!" : "Erro ao criar usuário. Por favor, tente novamente.";
		die(json_encode($json));
	}

	public static function update() {
		global $dbConn;
		$post = preprocess((object)static::$app->post);
		$id = (int)$post->id;

		$query = "
			SELECT login, email
			FROM user
			WHERE id = {$id}
		";			
		$result = mysqli_query($dbConn, $query);
		$fetch = mysqli_fetch_object($result);

		$json = new stdclass();
		if($post->senha != $post->senha2) {
			$json->success = false;
			$json->msg = "As duas senhas preenchidas estão diferentes!";
			die(json_encode($json));
		}
		if(!self::isUniqueLogin($post->login) && $post->login != $fetch->login) {
			$json->success = false;
			$json->msg = "Já existe um usuário com este login. Por favor, escolha outro.";
			die(json_encode($json));
		}
		if(!self::isUniqueEmail($post->email) && $post->email != $fetch->email) {
			$json->success = false;
			$json->msg = "Já existe um usuário com este e-mail. Por favor, escolha outro.";
			die(json_encode($json));
		}
		
		if($post->senha == "") {
			$query = "
				UPDATE user 
				SET login = '{$post->login}',
					email = '{$post->email}'
				WHERE id = {$id}
			";			
		}
		else {
			$password = hash('sha512', $post->senha);
			$query = "
				UPDATE user 
				SET login = '{$post->login}',
					email = '{$post->email}',
					password = '{$password}'
				WHERE id = {$id}
			";				
		}
		$result = mysqli_query($dbConn, $query);

		$json->success = $result;
		$json->msg = ($result === true)? "Usuário editado com sucesso!" : "Erro ao editar usuário. Por favor, tente novamente.";
		die(json_encode($json));
	}

	public static function delete() {
		global $dbConn;
		$post = preprocess((object)static::$app->post);
		$id = (int)$post->id;

		$query = "
			DELETE FROM user
			WHERE id = {$id}
		";			
		$result = mysqli_query($dbConn, $query);
		
		$json = new stdclass();
		$json->success = $result;
		$json->msg = ($result === true)? "Usuário removido com sucesso!" : "Erro ao remover usuário. Por favor, tente novamente.";
		die(json_encode($json));
	}
	private static function getAllUsers() {
		global $dbConn;
		$users = array();

		$query = "
			SELECT id, login, email
			FROM user 
			ORDER BY login ASC
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$users[] = $fetch;
		}

		return toUTF($users);
	}

	private static function getUserById($id) {
		global $dbConn;
		$users = array();

		$query = "
			SELECT id, login, email
			FROM user
			WHERE id = {$id} 
		";
		$result = mysqli_query($dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$fetch = mysqli_fetch_object($result);			
		}

		return toUTF($fetch);
	}

	private static function isUniqueLogin($login) {
		global $dbConn;
		$query = "
			SELECT id
			FROM user 
			WHERE login = '{$login}'
		";
		$result = mysqli_query($dbConn, $query);

		return (mysqli_num_rows($result) == 0)? true : false;
	}

	private static function isUniqueEmail($email) {
		global $dbConn;
		$query = "
			SELECT id
			FROM user 
			WHERE email = '{$email}'
		";
		$result = mysqli_query($dbConn, $query);

		return (mysqli_num_rows($result) == 0)? true : false;
	}
}
Usuarios::exec($app);