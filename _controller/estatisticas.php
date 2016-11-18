<?php
include("_lib/data.php");

class Projetos extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser(),
				"projetos" => self::getAllProjetos(),
				"users" => self::getAllUsers()
			);
		
			echo self::render("estatisticas/index.html", $bag);
		}
		else self::redirect("home");
	}

	public static function projeto() {
		if(Auth::user()) {
			$id = (int)static::$app->parametros[2];
			$tarefas = self::getTarefasDataByProjetoId($id);
			$bag = array(
				"tarefas" => $tarefas,
				"nome_projeto" => self::getProjectName($id),
				"max_tarefas" => self::getMaxStatusOnTarefas($tarefas)
			);
		
			echo self::render("estatisticas/projeto.html", $bag);
		}
		else self::redirect("home");
	}

	public static function usuario() {
		if(Auth::user()) {
			$id = (int)static::$app->parametros[2];
			$tarefas = self::getTarefasDataByUserId($id);
			$bag = array(
				"tarefas" => $tarefas,
				"nome_usuario" => self::getUserName($id),
				"max_tarefas" => self::getMaxStatusOnTarefas($tarefas)
			);
		
			echo self::render("estatisticas/usuario.html", $bag);
		}
		else self::redirect("home");
	}
	// AJAX Calls::

	public static function create() {
		$id = Auth::id();
		$post = static::$app->post;
		$privacidade = (int)$post['privacidade'];

		$json = new stdclass();
		$query = "
			INSERT INTO projeto (
				id_admin,
				nome,
				identidade_visual, 
				url,
				privacidade
			)
			VALUES (
				{$id},
				'{$post['nome']}',
				'{$post['identidade_visual']}',
				'{$post['url']}',
				{$privacidade}
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
			SELECT u.login, f.descricao funcao, u.id
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
							<a href='../equipe/sobre/{$fetch->id}'>
								<button class='btn-edit btn btn-default text-center pull-right' style='margin-right: 2px;'><i class='fa fa-pencil'></i></button>
							</a>
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
		
		return mysqli_num_rows($result);
	}

	private static function getAllUsers() {
		$usuario = array();

		$query = "
			SELECT id, login, nome
			FROM usuario
			WHERE nivel_privilegios < 2
			ORDER BY login
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$fetch->inicial = $fetch->login[0];
			$usuario[] = $fetch;
		}
		return toUTF($usuario);
	}

	private static function getTarefasDataByProjetoId($id) {
		global $dbConn;
		
		$tarefa = array(
			"backlog" => 0,
			"trabalhando" => 0,
			"testes" => 0,
			"cliente" => 0,
			"concluida" => 0
		);

		$query = "
			SELECT status
			FROM tarefa
			WHERE id_projeto = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			switch($fetch->status){
				case 0: $tarefa['backlog']++; 		break;
				case 1: $tarefa['trabalhando']++; 	break;
				case 2: $tarefa['testes']++; 		break;
				case 3: $tarefa['cliente']++; 		break;
				case 4: $tarefa['concluida']++; 	break;
			}
		}

		return $tarefa;
	}

	private static function getTarefasDataByUserId($id) {
		global $dbConn;
		
		$tarefa = array(
			"backlog" => 0,
			"trabalhando" => 0,
			"testes" => 0,
			"cliente" => 0,
			"concluida" => 0
		);

		$query = "
			SELECT status
			FROM tarefa
			WHERE id_usuario = {$id} OR id_autor = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			switch($fetch->status){
				case 0: $tarefa['backlog']++; 		break;
				case 1: $tarefa['trabalhando']++; 	break;
				case 2: $tarefa['testes']++; 		break;
				case 3: $tarefa['cliente']++; 		break;
				case 4: $tarefa['concluida']++; 	break;
			}
		}

		return $tarefa;
	}

	private static function getProjectName($id) {
		global $dbConn;

		$query = "
			SELECT nome
			FROM projeto
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		$fetch = mysqli_fetch_object($result);

		return toUTF($fetch->nome);
	}

	private static function getUserName($id) {
		global $dbConn;

		$query = "
			SELECT nome
			FROM usuario
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		$fetch = mysqli_fetch_object($result);

		return toUTF($fetch->nome);
	}

	private static function getMaxStatusOnTarefas($tarefas) {
		$status = "";
		$max = 0;
		$total = 0;
		foreach ($tarefas as $key => $value) {
			if($value > $max) {
				$max = $value;
				$status = $key;
			}
			$total += $value;
		}

		switch ($status) {
			case 'backlog': 	$status = "no Backlog"; break;
			case 'testes': 		$status = "em fase de Testes"; break;
			case 'trabalhando': $status = "em desenvolvimento"; break;
			case 'concluido': 	$status = "concluídas"; break;
			case 'cliente': 	$status = "pendente para avisar o cliente"; break;
		}
		
		return array("status"=> $status, "max"=> $max, "percent" => number_format(($max / $total)*100, 2));
	}
}

Projetos::exec($app);