<?php
include("_lib/data.php");

class Equipe extends Controller {

	public static function index() {
		self::redirect("projetos");
	}

	public static function sobre() {
		$id = static::$app->parametros[2];

		if(Auth::user() && Auth::is('admin')) {
			
			$bag = array(
				"projeto" => self::getProjetoByID($id),
				"usuarios" => self::getAllUsers(),
				"funcoes" => self::getAllFuncoes(),
				"equipe" => self::getEquipeByProjetoID($id)
			);

			echo self::render("equipe/sobre.html", $bag);
		}
		else self::redirect("projetos");
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}

	// AJAX Calls::

	public static function edit() {
		$id_user = Auth::id();
		
		$post = static::$app->post;
		$id = (int)$post['id'];
		
		$json = new stdclass();

		$query = "
			UPDATE projeto 
			SET 
				nome = '{$post['nome']}',
				privacidade = {$post['privacidade']},
				identidade_visual = '{$post['identidade_visual']}',
				url = '{$post['url']}'
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		$json->success = ($result)? true : false;

		die(json_encode($json));
	}

	public static function get() {
		$post = static::$app->post;
		$id = (int)$post['id'];
		
		$json = new stdclass();

		$query = "
			SELECT * 
			FROM equipe
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$fetch = mysqli_fetch_object($result);
			$json->success = true;
			$json->data = $fetch;			
		}
		else {
			$json->success = false;
		}

		die(json_encode($json));
	}

	public static function getlist() {
		$id = (int)static::$app->post['id'];
		$equipe = "";

		$query = "
			SELECT f.descricao funcao, u.nome nome, e.id, e.admin
			FROM projeto p
			INNER JOIN equipe e ON e.id_projeto = p.id 
			INNER JOIN funcao f ON f.id = e.funcao 
			INNER JOIN usuario u ON u.id = e.id_usuario
			WHERE p.id = {$id}
			ORDER BY e.admin ASC, u.nome ASC
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result)) {
			while($fetch = mysqli_fetch_object($result)) {
				$fetch = toUTF($fetch);
				switch ($fetch->admin) {
					case 0: $fetch->admin = "Desenvolvedor"; break;
					case 1: $fetch->admin = "Administrador"; break;
					case 2: $fetch->admin = "Visitante"; break;
				}
				echo "
					<tr data-id='{$fetch->id}'>
						<td> {$fetch->nome } </td>	
						<td> {$fetch->funcao} </td>
						<td> {$fetch->admin} </td>
						<td>
							<button class='btn-edit btn btn-default text-center' style='margin-right: 2px;'><i class='fa fa-pencil'></i></button>
							<button class='btn-delete btn btn-default text-center btn-danger'><i class='fa fa-times'></i></button>
						</td>	
					</tr>
				";
			}
		}
		else echo "
			<tr>
				<td class='text-danger'><i class='fa fa-exclamation-triangle' style='margin-right: 5px;'></i> Nenhum membro na equipe ainda! </td>
				<td>-</td>
				<td>-</td>	
				<td>-</td>
			</tr>
		";
	}

	public static function remove_member() {
		$post = (object)static::$app->post;
		$id_user = (int) $post->id;
		$id_projeto = (int) $post->id_projeto;
		$json = new stdclass();
		$json->success = true;
		die(json_encode($json));

		$query = "
			DELETE FROM equipe
			WHERE id_usuario = {$id_user} AND id_projeto = {$id_projeto}
		";
		$result = mysqli_query(static::$dbConn, $query);

		$json = new stdclass();
		$json->success = $result;

		die(json_encode($json));
	}

	public static function add_member() {
		$post = (object)static::$app->post;
		$id_usuario = (int) $post->nome;
		$id_projeto = (int) $post->id_projeto;
		$funcao = (int) $post->funcao;
		$posicao = (int) $post->posicao;

		// get msg for mail body
		$default_msg = "Olá, X, <br/>você foi selecionado para participar do projeto Y na função de Z.";
		$mensagem = (isset($post->mensagem) && $post->mensagem != "")? $post->mensagem : $default_msg; 
		
		$query = "
			SELECT id
			FROM equipe
			WHERE id_projeto = {$id_projeto} AND id_usuario = {$id_usuario}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if(mysqli_num_rows($result) > 0) {
			$json = new stdclass();
			$json->success = false;
			$json->msg = "Membro já pertencente à equipe!";

			die(json_encode($json));
		}

		$query = "
			INSERT INTO equipe (id_projeto, id_usuario, funcao, admin)
			VALUES (
				{$id_projeto},
				{$id_usuario},
				{$funcao},
				0
			)
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		if($result) {
			$json = new stdclass();
			$json->success = true;
			$json->msg = "Membro adicionado à equipe!";
		}

		die(json_encode($json));
	}

	public static function edit_member() {
		$post = (object)static::$app->post;
		$id = (int) $post->id;

		$query = "
			UPDATE equipe 
			SET 
				id_usuario = {$post->nome},
				funcao = {$post->funcao},
				admin = {$post->posicao}
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		if($result) {
			$json = new stdclass();
			$json->success = true;
			$json->msg = "Membro editado com sucesso!";
		}

		die(json_encode($json));
	}

	// Helpers::
	private static function getProjetoByID($id) {
		$projeto = null;

		$query = "
			SELECT *
			FROM projeto
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$projeto = mysqli_fetch_object($result);
			$projeto->imagem = ($projeto->imagem != "")? "_files/projeto/{$projeto->id}/".$projeto->imagem : "assets/img/login/logo.png";
		}

		return toUTF($projeto);
	}

	private static function getEquipeByProjetoID($id) {
		$equipe = array();

		$query = "
			SELECT u.nome, f.descricao funcao, e.admin, e.id
			FROM usuario u
			INNER JOIN equipe e ON e.id_usuario = u.id
			INNER JOIN funcao f ON e.funcao = f.id
			WHERE e.id_projeto = {$id}
			ORDER BY e.admin ASC, u.nome ASC
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) > 0) {
			while($fetch = mysqli_fetch_object($result)) {
				switch ($fetch->admin) {
					case 0: $fetch->admin = "Desenvolvedor"; break;
					case 1: $fetch->admin = "Administrador"; break;
					case 2: $fetch->admin = "Visitante"; break;
				}
				$equipe[] = $fetch;
			}
		}

		return toUTF($equipe);
	}

	private static function isAdmin($id_user, $id_project) {
		$query = "
			SELECT p.id
			FROM projeto p 
			INNER JOIN equipe e ON e.id_projeto = p.id
			INNER JOIN usuario u ON u.id = e.id_usuario
			WHERE p.id = {$id_project} AND u.id = {$id_user} AND e.admin = 1
		";
		$result = mysqli_query(static::$dbConn, $query);
		
		return (mysqli_num_rows($result))? 1 : 0;
	}

	private static function getAllUsers() {
		$usuario = array();

		$query = "
			SELECT id, login
			FROM usuario
			WHERE nivel_privilegios < 3
			ORDER BY login ASC
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$usuario[] = $fetch;
		}
		return toUTF($usuario);
	}

	private static function getAllFuncoes() {
		$funcao = array();

		$query = "
			SELECT id, descricao
			FROM funcao
			ORDER BY descricao
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$funcao[] = $fetch;
		}
		return toUTF($funcao);
	}
}

Equipe::exec($app);