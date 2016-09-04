<?php
include("_lib/data.php");

class Lembretes extends Controller {

	public static function index() {
		if(Auth::user()) {
			$bag = array(
				"user" => Auth::getUser(),
				"lembretes" => self::getAllLembretesByUserId(Auth::id())
			);
		
			echo self::render("lembretes/index.html", $bag);
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

	public static function getlist() {
		$post = static::$app->post;
		$id = Auth::id();
		$ORDER = self::getOrder($post['sort_type']);

		$query = "
			SELECT *
			FROM lembrete 
			WHERE id_usuario = {$id} AND titulo LIKE '%{$post['search_titulo']}%'
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
				echo "
					<tr>
						<td> {$fetch->titulo } </td>	
						<td class='text-center'><span class='prioridade-label {$fetch->prioridade_label}'> {$fetch->prioridade} </span></td>	
						<td class='text-center'> {$fetch->data} </td>	
						<td class='text-center'> {$fetch->status} </td>
						<td>
							<button class='btn btn-default text-center pull-right btn-danger'><i class='fa fa-times'></i></button>
							<button class='btn btn-default text-center pull-right btn-success' style='margin-right: 1px;'><i class='fa fa-check'></i></button>
							<button class='btn btn-default text-center pull-right' style='margin-right: 1px;'><i class='fa fa-pencil'></i></button>
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
	private static function getAllLembretesByUserId($id) {
		$lembretes = array();

		$query = "
			SELECT *
			FROM lembrete 
			WHERE id_usuario = {$id}
		";

		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$fetch->descricao = nl2br($fetch->descricao);
			$fetch->status = self::getStatus($fetch->status, $fetch->data);
			$fetch->prioridade_label = self::getPriorityLabel($fetch->prioridade);
			$fetch->prioridade = self::getPrioridade($fetch->prioridade);
			$fetch->data = Data::datetime2str($fetch->data);

			$lembretes[] = $fetch;
		}

		return toUTF($lembretes);
	}

	private static function getStatus($status, $data) {
		switch ($status) {
			case 0:	
				$data_entrega = new DateTime($data);
				$data_hoje = new DateTime();
				$diff = $data_entrega->diff($data_hoje);
				
				// (string) [+/-]days
				$days = $diff->format("%R%a");

				return ($days[0] == '-' && (int)$days[1] > 0) ? "Atrasado" : "Em andamento";

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

Lembretes::exec($app);