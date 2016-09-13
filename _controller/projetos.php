<?php
include("_lib/data.php");

class Projetos extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser(),
				"projetos" => self::getAllProjetosByUserId(Auth::id())
			);
		
			echo self::render("projetos/index.html", $bag);
		}
		else self::redirect("home");
	}

	public static function sobre() {
		$id = static::$app->parametros[2];

		if(Auth::user() && (self::isUserOnProject(Auth::id(), $id) || self::isProjectPublic($id)) ) {
			$bag = array(
				"user" => Auth::getUser(),
				"projeto" => self::getProjetoById($id),
				"equipe" => self::getTeamByProjetoId($id),
				"informacoes" => self::getInfoByProjetoId($id)
			);
			
			echo self::render("projetos/sobre.html", $bag);
		}
		else self::redirect("projetos");
	}

	public static function gerenciar() {
		if(Auth::user() && Auth::is('admin')) {
			$bag = array(
				"user" => Auth::getUser(),
				"projetos" => self::getAllProjetos()
			);

			echo self::render("projetos/gerenciar.html", $bag);
		}
		else self::redirect("home");
	}

	public static function equipe() {
		if(Auth::user() && Auth::is('admin')) {
			
			$bag = array(
				"user" => Auth::getUser(),
				"projetos" => self::getAllProjetos()
			);

			echo self::render("projetos/equipe.html", $bag);
		}
		else self::redirect("home");
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}

	// AJAX Calls::

	public static function create() {
		$id = Auth::id();
		$post = static::$app->post;
		$data = Data::str2date($post['data']);
		$prioridade = $post['prioridade'] - 1;

		$json = new stdclass();

		$query = "
			INSERT INTO lembrete (
				id_usuario,
				titulo, 
				descricao,
				prioridade,
				data
			)
			VALUES (
				{$id},
				'{$post['titulo']}',
				'{$post['descricao']}',
				{$prioridade},
				'{$data}'
			)
		";
		$result = mysqli_query(static::$dbConn, $query);
		$json->success = ($result)? true : false;		

		die(json_encode($json));
	}

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
		$id_user = Auth::id();
		$id = (int) static::$app->post['id'];

		$json = new stdclass();

		$query = "
			SELECT *
			FROM projeto p
			WHERE p.id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) == 1 ) {
			$fetch = mysqli_fetch_object($result);
			
			$json->success = true;
			$json->projeto = toUTF($fetch);
		}
		else $json->success = false;
		
		$query = "
			SELECT u.login, f.descricao funcao
			FROM projeto p
			INNER JOIN equipe e ON e.id_projeto = p.id 
			INNER JOIN usuario u ON u.id = e.id_usuario
			INNER JOIN funcao f ON f.id = e.funcao
			WHERE p.id = {$id}
			ORDER BY login
		";
		$equipe = array();
		$result = mysqli_query(static::$dbConn, $query);
		
		if($result && mysqli_num_rows($result) > 0) {
			while($fetch = mysqli_fetch_object($result)) {
				$equipe[] = $fetch;
			}

			$json->success = true;
			$json->equipe = toUTF($equipe);
		}
		else $json->success = false;

		die(json_encode($json));
	}

	public static function delete() {
		$id_user = Auth::id();
		$id = (int) static::$app->post['id'];
		$json = new stdclass();

		$query = "
			DELETE FROM projeto
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);		
		
		$json->success = (mysqli_affected_rows(static::$dbConn) > 0)? true : false;
		
		die(json_encode($json));
	}

	public static function getlist() {
		$post = static::$app->post;
		$id = Auth::id();
		$ORDER = self::getOrder($post['sort_type'], $post['sort_privacidade']);

		$query = "
			SELECT DISTINCT p.*, u.login criador
			FROM projeto p 
			LEFT JOIN equipe e ON e.id_projeto = p.id
			LEFT JOIN usuario u ON  u.id = p.id_admin
			{$ORDER}
		";

		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		$lembretes = array();
		if($result && mysqli_num_rows($result) > 0) {
			while ($fetch = mysqli_fetch_object($result)) {
				$fetch = toUTF($fetch);
				$fetch->privacidade = ($fetch->privacidade == 1)? 
				"Privado <i class='fa fa-lock' style='margin-left: 3px;'></i>" : 
				"Público <i class='fa fa-eye' style='margin-left: 3px;'></i>";

				echo "
					<tr data-id='{$fetch->id}'>
						<td> {$fetch->nome } </td>	
						<td class='text-center'>
							<span class='identidade-box' style='background: {$fetch->identidade_visual};'></span>
						</td>	
						<td class='text-center'>
							<a href='{$fetch->url}' target='_blank'> Visitar <i class='fa fa-external-link' style='margin-left: 2px;'></i></a>
						</td>	
						<td class='text-center'> {$fetch->criador} </td>
						<td class='text-center'> {$fetch->privacidade} </td>
						<td>
							<button class='btn-delete btn btn-default text-center pull-right btn-danger'><i class='fa fa-times'></i></button>
							<button class='btn-edit btn btn-default text-center pull-right' style='margin-right: 2px;'><i class='fa fa-pencil'></i></button>
						</td>	
					</tr>
				";
			}
		}
		else echo "
			<tr style='height: 51px;'>
				<td class='text-danger'><i class='fa fa-exclamation-triangle' style='margin-right: 5px;'></i> Nenhum resultado encontrado!</td>
				<td></td>
				<td></td>	
				<td></td>
				<td></td>	
				<td></td>	
			</tr>
		";
	}

	// Helpers::
	private static function getAllProjetos() {
		$projetos = array();

		$query = "
			SELECT DISTINCT p.*, u.login criador
			FROM projeto p 
			LEFT JOIN equipe e ON e.id_projeto = p.id
			LEFT JOIN usuario u ON  u.id = p.id_admin
			ORDER BY p.nome ASC
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) > 0) {
			while($fetch = mysqli_fetch_object($result)) {
				$fetch->privacidade = ($fetch->privacidade == 1)? 
				"Privado <i class='fa fa-lock' style='margin-left: 3px;'></i>" : 
				"Público <i class='fa fa-eye' style='margin-left: 3px;'></i>";

				$projetos[] = $fetch;
			}
		}

		return toUTF($projetos);
	}

	private static function getAllProjetosByUserId($id) {
		$lembretes = array();

		$query = "
			SELECT *
			FROM equipe e
			INNER JOIN projeto p ON e.id_projeto = p.id
			WHERE e.id_usuario = {$id}
			ORDER BY p.nome
		";

		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$lembretes[] = $fetch;
		}

		return toUTF($lembretes);
	}

	private static function isUserOnProject($id_user, $id_project) {
		$query = "
			SELECT *
			FROM projeto p 
			INNER JOIN equipe e ON e.id_projeto = p.id
			WHERE e.id_usuario = {$id_user} AND p.id = {$id_project}
		";

		$result = mysqli_query(static::$dbConn, $query);

		return mysqli_num_rows($result);
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

	private static function isProjectPublic($id) {
		$query = "
			SELECT id
			FROM projeto 
			WHERE id = {$id} AND privacidade = 0
		";
		$result = mysqli_query(static::$dbConn, $query);
		var_dump(mysqli_num_rows($result));
		return mysqli_num_rows($result);
	}

	private static function getProjetoById($id) {
		$query = "
			SELECT *
			FROM projeto
			WHERE id = {$id}
		";

		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result)) {
			$fetch = mysqli_fetch_object($result);
			$fetch->imagem = ($fetch->imagem != "")? "_files/projeto/{$fetch->id}/".$fetch->imagem : "assets/img/login/logo.png";
		}

		return toUTF($fetch);
	}

	private static function getTeamByProjetoId($id) {
		$equipe = array();

		$query = "
			SELECT e.funcao, u.login usuario, u.id
			FROM projeto p
			INNER JOIN equipe e ON e.id_projeto = p.id 
			INNER JOIN usuario u ON u.id = e.id_usuario
			WHERE p.id = {$id}
			ORDER BY e.admin DESC
		";

		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result)) {
			while($fetch = mysqli_fetch_object($result)) {
				$fetch->inicial = $fetch->usuario[0];
				$equipe[] = $fetch;
			}
		}

		return toUTF($equipe);
	}

	private static function getInfoByProjetoId($id) {
		$informacoes = array();
		$membros = array();
		
		// Obtém dados gerais do projeto::
		$query = "
			SELECT 
				p.nome projeto, 
			    p.identidade_visual, 
			    p.privacidade, 
			    u.login criador,
			    u2.nome membro 
			FROM `projeto` p
			INNER JOIN equipe e ON e.id_projeto = p.id
			INNER JOIN usuario u2 ON u2.id = e.id_usuario
			INNER JOIN usuario u ON u.id = p.id_admin
			WHERE p.id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$fetch->privacidade = ($fetch->privacidade == 1)? "Privado" : "Público";
			$membros[] = $fetch;
		}		

		// Obtém função do usuário no projeto::
		$id_user = Auth::id();
		$query = "
			SELECT  f.descricao funcao 
			FROM `projeto` p
			INNER JOIN equipe e ON e.id_projeto = p.id
			INNER JOIN usuario u ON u.id = e.id_usuario
			INNER JOIN funcao f ON f.id = e.funcao
			WHERE p.id = {$id} AND u.id = {$id_user}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$fetch = mysqli_fetch_object($result);
		
		$is_admin = (self::isAdmin(Auth::id(), $id))? true : false;

		$informacoes["funcao"] = $fetch->funcao;
		$informacoes["total_equipe"] = count($membros);
		$informacoes["geral"] = $membros[0];
		$informacoes["admin"] = $is_admin;
		
		return toUTF($informacoes);
	}

	private static function getOrder($order, $filter) {
		
		$ORDERBY = "ORDER BY ";
		switch($order) {
			case 1: $ORDERBY .= "nome ASC"; break;
			case 2:	$ORDERBY .= "nome DESC"; break;
			case 3:	$ORDERBY .= "criador ASC"; break;
			case 4:	$ORDERBY .= "criador DESC"; break;
		}

		// privacidade::
		$post = preprocess(static::$app->post['search_titulo']);
		
		$WHERE = "WHERE p.nome LIKE '%{$post}%' ";
		switch($filter) {
			case 1: $WHERE .= ""; break;
			case 2:	$WHERE .= "AND privacidade = 0"; break;
			case 3:	$WHERE .= "AND privacidade = 1"; break;
		}
		
		return $WHERE." ".$ORDERBY;
	}
}

Projetos::exec($app);