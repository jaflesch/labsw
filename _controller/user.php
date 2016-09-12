<?php
class Home extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => self::getUserById(Auth::id() )
			);
			echo self::render("user/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function perfil() {
		if(Auth::user()) {
			$bag = array(
				"user" => self::getUserById(Auth::id() ),
				"trofeu" => self::getTrophyCountFromUser(Auth::id())
			);
			echo self::render("user/index.html", $bag);
		}
		else echo self::redirect("");
	}

	public static function update_perfil() {
		
		$json = new stdclass();
		$id = Auth::id();

		// Se existe um usuário com o login fornecido e de id != do usuário solicitando alteração
		$query = "
			SELECT *
			FROM usuario
			WHERE id != {$id} AND login = '{$_POST['login']}'
		";
		if(mysqli_num_rows(mysqli_query(static::$dbConn, $query)) > 0) {
			$json->success = false;
			$json->msg = "login_error";
			die(json_encode($json));
		}
		
		// Se existe um usuário com o e-mail fornecido e de id != do usuário solicitando alteração
		$query = "
			SELECT *
			FROM usuario
			WHERE id != {$id} AND email = '{$_POST['email']}'
		";
		if(mysqli_num_rows(mysqli_query(static::$dbConn, $query)) > 0) {
			$json->success = false;
			$json->msg = "email_error";
			die(json_encode($json));
		}
		
		// Se não existe um usuário com login e e-mail fornecidos e de id != do usuário solicitando alteração
		$query = "
			UPDATE usuario
			SET 
				nome = '{$_POST['nome']}',
				login = '{$_POST['login']}',
				email = '{$_POST['email']}'
			
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		if($result) {
			$json->success = true;
			$json->msg = "Dados alterados com sucesso!";
			die(json_encode($json));
		}
		else {
			$json->success = false;
			$json->msg = "Erro na execução do script.";
			die(json_encode($json));
		}
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

	public static function update_senha() {
		
		$json = new stdclass();
		
		if($_POST['senha'] != $_POST['senha2']) {
			$json->success = false;
			$json->msg = "As senhas estão diferentes!";
			$json->error = 1;
			die(json_encode($json));
		}
		
		$id = Auth::id();
		$query = "
			SELECT senha
			FROM usuario
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$fetch = mysqli_fetch_object($result);
		
		if($fetch->senha != md5($_POST['senha_antiga'])) {
			$json->success = false;
			$json->msg = "A senha antiga está incorreta!";
			$json->error = 2;
			die(json_encode($json));
		}
		
		$senha = md5($_POST['senha']);
		$query = "
			UPDATE usuario
			SET senha = '{$senha}'
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result) {
			$json->success = true;
			$json->msg = "Senha alterada com sucesso!";
			$json->error = 0;
			die(json_encode($json));
		}
		else {
			$json->success = false;
			$json->msg = "Erro na execução do script.";
			$json->error = -1;
			die(json_encode($json));
		}		
	}
	
	public static function logout() {
		Auth::logout();
		self::redirect("");
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}

	private static function getUserById($id) {

		$query = "
			SELECT *
			FROM usuario
			WHERE id= {$id}
		";

		$result = mysqli_query(static::$dbConn, $query);
		$fetch = mysqli_fetch_object($result);
		$fetch->funcao = self::getFuncao($fetch->funcao);
		unset($fetch->senha);

		return toUTF($fetch);
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

	private static function getFuncao($nivel) {
		switch($nivel) {
			case 0: return "Administrador";
			case 1: return "Usuário";
			case 2: return "Visitante";
		}
	}
}

Home::exec($app);
