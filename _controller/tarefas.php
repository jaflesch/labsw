<?php
include("_lib/data.php");

class Tarefas extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser(),
				"tarefas" => self::getAllTarefasByUserId(Auth::id()),
				"projetos" => self::getAllProjetos()
			);
		
			echo self::render("tarefas/index.html", $bag);
		}
		else self::redirect("home");
	}

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}

	public static function novo($tpl, $vars=array()) {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser(),
				"projetos_admin" => self::getAllProjetosWhereUserAdmin(Auth::id()),
				"categorias" =>self::getAllCategorias()
			);
		
			echo self::render("tarefas/novo.html", $bag);
		}
		else self::redirect("home");
	}

	public static function editar($tpl, $vars=array()) {
		if(Auth::user()) {
			$id = static::$app->parametros[2];
			$tarefa = self::getTarefaById($id);
			
			$bag = array(
				"user" => Auth::getUser(),
				"projetos_admin" => self::getAllProjetosWhereUserAdmin(Auth::id()),
				"categorias" => self::getAllCategorias(),
				"subcategorias" => self::getSubcategoriasByCategoriaId($tarefa->id_categoria),
				"membros" => self::getTeamByProjetoId($tarefa->id_projeto),
				"tarefa" => $tarefa
			);
		
			echo self::render("tarefas/editar.html", $bag);
		}
		else self::redirect("home");
	}

	// AJAX Calls::
	public static function create() {
		$id = Auth::id();
		$post = static::$app->post;

		$datetime = explode(" ", $post['data_entrega']);
		$data_entrega = Data::str2date($datetime[0])." ".$datetime[1].":00";
		$prioridade = ($prioridade == 0)? $post['prioridade'] : $post['prioridade']-1;
		$status_erro = (isset($post['status_erro']) && $post['status_erro'] != "")? $post['status_erro'] : 0;
		$post['responsavel_tarefa'] = ($post['responsavel_tarefa'] == 2)? $post['responsavel_membro_tarefa'] : 0;
		
		$json = new stdclass();
		
		$query = "
			INSERT INTO tarefa (
				id_autor,
				id_usuario,
				id_projeto,
				titulo,
				prioridade,
				id_categoria, 
				id_subcategoria,
				descricao_formal,
				descricao_tecnica,
				solucao,
				resultados,
				status_erro,
				tempo_previsto,
				status,
				data_criacao,
				data_entrega
			)
			VALUES (
				{$id},
				{$post['responsavel_tarefa']},
				{$post['projeto']},
				'{$post['titulo']}',
				{$prioridade},
				{$post['categoria']},
				{$post['subcategoria']},
				'{$post['descricao_formal']}',
				'{$post['descricao_tecnica']}',
				'{$post['solucao']}',
				'{$post['resultados']}',
				{$status_erro},
				'{$post['tempo_previsto']}',
				0,
				NOW(),
				'{$data_entrega}'
			)
		";
		
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		$json->success = ($result)? true : false;		
		$json->id = ($json->success)? mysqli_insert_id(static::$dbConn) : -1;
		
		die(json_encode($json));
	}

	public static function update() {
		$post = static::$app->post;
		
		$id = $post['id'];
		$prioridade = ($prioridade == 0)? $post['prioridade'] : $post['prioridade']-1;
		$responsavel_tarefa = ($post['responsavel_tarefa'] == 2)? $post['responsavel_membro_tarefa'] : 0;
		$status_erro = (isset($post['status_erro']) && $post['status_erro'] != "")? $post['status_erro'] : 0;

		$json = new stdclass();

		if(Auth::is('admin')) {
			$query = "
				UPDATE tarefa 
				SET 
					id_usuario = {$responsavel_tarefa},
					id_projeto = {$post['projeto']},
					titulo = '{$post['titulo']}',
					prioridade = {$prioridade},
					id_categoria = {$post['categoria']}, 
					id_subcategoria = {$post['subcategoria']},
					descricao_formal = '{$post['descricao_formal']}',
					descricao_tecnica = '{$post['descricao_tecnica']}',
					solucao = '{$post['solucao']}',
					resultados = '{$post['resultados']}',
					status_erro = {$status_erro},
					tempo_previsto = '{$post['tempo_previsto']}'

				WHERE id = {$id}
			";
		}
		else if(Auth::is('user')) {
			$query = "
				UPDATE tarefa 
				SET 
					id_usuario = {$responsavel_tarefa},
					titulo = '{$post['titulo']}',
					prioridade = {$prioridade},
					descricao_formal = '{$post['descricao_formal']}',
					descricao_tecnica = '{$post['descricao_tecnica']}',
					solucao = '{$post['solucao']}',
					resultados = '{$post['resultados']}',
					status_erro = {$status_erro},
					tempo_previsto = '{$post['tempo_previsto']}'

				WHERE id = {$id}
			";
		}
		
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
			FROM tarefa
			WHERE id_autor = {$id_user} AND id = {$id}
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
			DELETE FROM tarefa
			WHERE id_autor = {$id_user} AND id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);		
		$json->success = ($result)? true : false;
		
		die(json_encode($json));
	}

	public static function getlist() {
		$post = static::$app->post;
		$id = Auth::id();
		$ORDER = self::getOrder($post['sort_type']);
		$value = isset($post['status'])? "0" : "1";
		$STATUS = " AND status = ". $value;

		$query = "
			SELECT *
			FROM tarefa 
			WHERE id_autor = {$id} AND titulo LIKE '%{$post['search_titulo']}%' {$STATUS}
			{$ORDER}
		";
		$result = mysqli_query(static::$dbConn, $query);
		$lembretes = array();
		if($result && mysqli_num_rows($result) > 0) {
			while ($fetch = mysqli_fetch_object($result)) {
				$fetch->descricao = nl2br($fetch->descricao);
				$fetch->status = self::getStatus($fetch->status, $fetch->data);
				$fetch->prioridade_label = self::getPriorityLabel($fetch->prioridade - 1);
				$fetch->prioridade = self::getPrioridade($fetch->prioridade - 1);
				$fetch->data = Data::datetime2str($fetch->data);

				$text = ($fetch->status == "Atrasado")? "text-danger" : "";
				echo "
					<tr data-id='{$fetch->id}' class='{$text}'>
						<td> {$fetch->titulo } </td>	
						<td class='text-center'><span class='prioridade-label {$fetch->prioridade_label}'> {$fetch->prioridade} </span></td>	
						<td class='text-center'> {$fetch->data} </td>	
						<td class='text-center'> {$fetch->status} </td>
						<td>
							<button class='btn-delete btn btn-default text-center pull-right btn-danger'><i class='fa fa-times'></i></button>
				";
				if($fetch->status != "<span style='color:#2fa561;'>Completo</span>") {
					echo "
							<button class='btn-check btn btn-default text-center pull-right btn-success' style='margin-right: 2px;'><i class='fa fa-check'></i></button>
					";
				}
				echo "
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

	public static function get_team() {
		$post = (object)static::$app->post;
		$team = "<option value=''>Selecione uma opção...</option>";
		
		$query = "
			SELECT u.nome, u.id
			FROM equipe e
			INNER JOIN usuario u ON e.id_usuario = u.id
			WHERE id_projeto = {$post->id}
			ORDER BY nome
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		if($result && mysqli_num_rows($result) > 0) {
			while ($fetch = mysqli_fetch_object($result)) {
				$team .= "<option value='{$fetch->id}'>{$fetch->nome}</option>";
			}
		}

		echo $team;
	}

	public static function get_subcategoria() {
		$post = (object)static::$app->post;
		$team = "<option value=''>Selecione uma categoria...</option>";
		
		$query = "
			SELECT s.nome, s.id
			FROM subcategoria s
			INNER JOIN categoria c ON c.id = s.id_categoria
			WHERE id_categoria = {$post->id}
			ORDER BY nome
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
		if($result && mysqli_num_rows($result) > 0) {
			while ($fetch = mysqli_fetch_object($result)) {
				$team .= "<option value='{$fetch->id}'>{$fetch->nome}</option>";
			}
		}

		echo $team;
	}
	
	public static function get_average_time() {
		$post = (object)static::$app->post;
		$tarefas = array();
		$json = new stdclass();

		$query = "
			SELECT tempo_previsto
			FROM tarefa
			WHERE id_categoria = {$post->id_categoria} AND id_subcategoria = {$post->id_subcategoria}
		";
		$result = mysqli_query(static::$dbConn, $query);

		if($result && mysqli_num_rows($result) > 0) {
			$i = 0;
			$tempo = 0;
			while ($fetch = mysqli_fetch_object($result)) {
				$horas = (int)substr($fetch->tempo_previsto, 0, 2);
				$min = (int)substr($fetch->tempo_previsto, 3, 2);

				$tempo += $horas*60 + $min;
				$i++;
			}
			
			// format info
			$media = $tempo / $i;
			if($media < 60) {
				$json->msg = "O tempo médio para a realização de tarefas do tipo <em>{$post->nome}</em> é de aproximadamente {$media} minutos.";
			}
			else {
				$horas = (int) ($media / 60);
				$min = $media % 60;
				$textH = $horas == 1 ? "hora" : "horas";
				$textMin = $min != 0 ? " e {$min} minutos" : "";
				
				$json->msg = "O tempo médio para a realização de tarefas do tipo <em>{$post->nome}</em> é de aproximadamente 
				{$horas} {$textH}{$textMin}.";
			}

			$json->success = true;
		}
		else {
			$json->success = false;
			$json->msg = "";
		}

		die(json_encode($json));
	}

	// Helpers::
	private static function getAllTarefasByUserId($id) {
		$tarefas = array();

		$query = "
			SELECT *
			FROM tarefa 
			WHERE id_autor = {$id} AND status = 0
			ORDER BY prioridade DESC, data_entrega ASC
		";

		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$fetch->descricao = nl2br($fetch->descricao);
			$fetch->status = self::getStatus($fetch->status, $fetch->data);
			$fetch->prioridade_label = self::getPriorityLabel($fetch->prioridade - 1);
			$fetch->prioridade = self::getPrioridade($fetch->prioridade - 1);
			$fetch->data = Data::datetime2str($fetch->data);

			$tarefas[] = $fetch;
		}

		return toUTF($tarefas);
	}

	private static function getAllProjetos() {
		$projetos = array();

		$query = "
			SELECT nome, id
			FROM projeto
			ORDER BY nome
		";

		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$projetos[] = $fetch;
		}

		return toUTF($projetos);
	}

	private static function getAllProjetosWhereUserAdmin($id) {
		$projetosAdmin = array();

		$query = "
			SELECT id, nome
			FROM projeto
			WHERE id_admin = {$id}
			                    
			UNION (
			    SELECT id_projeto id, nome
			    FROM equipe e
			    INNER JOIN projeto p ON p.id = e.id_projeto
			    WHERE id_usuario = {$id} AND admin = 1
			)
			ORDER BY nome
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$projetosAdmin[] = $fetch;
		}

		return toUTF($projetosAdmin);
	}

	private static function getAllCategorias() {
		$categorias = array();

		$query = "
			SELECT *
			FROM categoria
			ORDER BY nome
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$categorias[] = $fetch;
		}

		return toUTF($categorias);
	}

	private static function getTarefaById($id) {
		$tarefa = null;

		$query = "
			SELECT *
			FROM tarefa
			WHERE id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);

		if($result && mysqli_num_rows($result)) {
			$fetch = mysqli_fetch_object($result);
			
			// prepare data
			$datetime = explode(" ", $fetch->data_entrega);
			$fetch->data_entrega = Data::datetime2str($fetch->data_entrega)." ".$datetime[1];
			//$fetch->prioridade++;

			$tarefa = toUTF($fetch);
		}

		return $tarefa;
	}

	private static function getSubcategoriasByCategoriaId($id) {
		$subcategorias = array();

		$query = "
			SELECT id, nome
			FROM subcategoria
			WHERE id_categoria = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$subcategorias[] = toUTF($fetch);
		}

		return $subcategorias;
	}
	
	private static function getTeamByProjetoId($id) {
		$team = array();

		$query = "
			SELECT u.id, u.nome
			FROM equipe e
			INNER JOIN usuario u ON u.id = e.id_usuario
			WHERE id_projeto = {$id}
			ORDER BY u.nome
		";
		$result = mysqli_query(static::$dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$team[] = toUTF($fetch);
		}

		return $team;
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

			case 1: return "<span style='color:#2fa561;'>Completo</span>";
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

Tarefas::exec($app);