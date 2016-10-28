<?php
include("_lib/data.php");

class Lembretes extends Controller {

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

	// AJAX Calls::
	public static function getCategoryLabel() {
		$post = static::$app->post;
		$category = null;

		$query = "
			SELECT *
			FROM categoria
			WHERE id_projeto = {$post->id_projeto}
		";
		$result = mysqli_query(static::$dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$category = toUTF($fetch);
		}

		return $category;
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
			$media = 120;
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

	public static function create() {
		$id = Auth::id();
		$post = static::$app->post;
		//$data_entrega = Data::str2date($post['data_entrega']);
		$prioridade = ($prioridade == 0)? $post['prioridade'] : $post['prioridade']-1;
		$status_erro = (isset($post['status_erro']) && $post['status_erro'] != "")? $post['status_erro'] : 0;

		$json = new stdclass();
		
		$query = "
			INSERT INTO tarefa (
				id_autor,
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
				NOW()
			)
		";
		$result = mysqli_query(static::$dbConn, $query) or die(mysqli_error(static::$dbConn));
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
			UPDATE tarefa 
			SET 
				titulo = '{$post['titulo']}',
				prioridade = {$prioridade},
				descricao = '{$post['descricao']}',
				data = '{$data}'
			WHERE id = {$id} AND id_autor = {$id_user}
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
				$fetch->prioridade_label = self::getPriorityLabel($fetch->prioridade);
				$fetch->prioridade = self::getPrioridade($fetch->prioridade);
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

	public static function complete() {
		$id_user = Auth::id();
		$id = (int) static::$app->post['id'];

		$json = new stdclass();

		$query = "
			UPDATE lembrete
			SET status = 1
			WHERE id_usuario = {$id_user} AND id = {$id}
		";
		$result = mysqli_query(static::$dbConn, $query);		
		$json->success = ($result)? true : false;
		
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
			$fetch->prioridade_label = self::getPriorityLabel($fetch->prioridade);
			$fetch->prioridade = self::getPrioridade($fetch->prioridade);
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

Lembretes::exec($app);