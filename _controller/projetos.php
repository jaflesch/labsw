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

		if(Auth::user() && self::isUserOnProject(Auth::id(), $id) || self::isProjectPublic($id) ) {
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
		$data = Data::str2date($post['data'])." 00:00:00";
		$prioridade = $post['prioridade'] - 1;

		$json = new stdclass();

		$query = "
			UPDATE lembrete 
			SET 
				titulo = '{$post['titulo']}',
				prioridade = {$prioridade},
				descricao = '{$post['descricao']}',
				data = '{$data}'
			WHERE id = {$id} AND id_usuario = {$id_user}
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
			FROM lembrete
			WHERE id_usuario = {$id_user} AND id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) == 1 ) {
			$fetch = mysqli_fetch_object($result);
			
			// correct display on HTML <select>
			$fetch->prioridade++;
			$fetch->data = Data::datetime2str($fetch->data);

			$json->success = true;
			$json->lembrete = toUTF($fetch);
		}
		else $json->success = false;
		
		die(json_encode($json));
	}

	public static function delete() {
		$id_user = Auth::id();
		$id = (int) static::$app->post['id'];

		$json = new stdclass();

		$query = "
			DELETE FROM lembrete
			WHERE id_usuario = {$id_user} AND id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);		
		$json->success = ($result)? true : false;
		
		die(json_encode($json));
	}

	public static function getlist() {
		$post = static::$app->post;
		$id = Auth::id();
		$ORDER = self::getOrder($post['sort_type']);

		$query = "
			SELECT *
			FROM equipe e
			INNER JOIN projeto p ON e.id_projeto = p.id
			WHERE e.id_usuario = {$id}
			{$ORDER}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$lembretes = array();
		if($result && mysqli_num_rows($result) > 0) {
			while ($fetch = mysqli_fetch_object($result)) {
				echo "
					<tr data-id='{$fetch->id}'>
						<td> {$fetch->titulo } </td>	
						<td class='text-center'><span class='prioridade-label {$fetch->prioridade_label}'> {$fetch->prioridade} </span></td>	
						<td class='text-center'> {$fetch->data} </td>	
						<td class='text-center'> {$fetch->status} </td>
						<td>
							<button class='btn-delete btn btn-default text-center pull-right btn-danger'><i class='fa fa-times'></i></button>
							<button class='btn-check btn btn-default text-center pull-right btn-success' style='margin-right: 2px;'><i class='fa fa-check'></i></button>
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
			</tr>
		";
	}

	// Helpers::
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

		// may be useful::
		//$result = mysqli_query(static::$dbConn, $query)
		return mysqli_query(static::$dbConn, $query);
	}

	private static function isProjectPublic($id) {
		// to do query
		return false;
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

	private static function getStatus($status, $data) {
		switch ($status) {
			case 0:	
				$data_entrega = new DateTime($data);
				$data_hoje = new DateTime();
				$diff = $data_entrega->diff($data_hoje);
				
				// (string) [+/-]days
				$days = $diff->format("%R%a");
				return ($days[0] == '+' && (int)$days[1] > 0) ? "Atrasado" : "Em andamento";

			case 1: return "Completo";
			default: return "Status: ".$status;
		}
	}

	private static function getPrioridade($prioridade) {
		switch ($prioridade) {
			case 0:	return "Baixa";
			case 1: return "Normal";
			case 2: return "Alta";
			case 3: return "Crítica";
			default: return "Prioridade: ".$prioridade;
		}
	}

	private static function getPriorityLabel($prioridade) {
		switch ($prioridade) {
			case 0:	return "alert-low";
			case 1: return "alert-normal";
			case 2: return "alert-danger";
			case 3: return "alert-critical";
			default: return "Prioridade: ".$prioridade;
		}
	}

	private static function getOrder($int) {
		switch ($int) {
			case 1:	return "ORDER BY prioridade DESC";
			case 2: return "ORDER BY prioridade ASC";
			case 3: return "ORDER BY titulo ASC";
			case 4: return "ORDER BY titulo DESC";
			case 5:	return "ORDER BY data DESC";
			case 6: return "ORDER BY data ASC";

			default: return "";
		}
	}
}

Projetos::exec($app);