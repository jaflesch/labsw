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
}

Lembretes::exec($app);