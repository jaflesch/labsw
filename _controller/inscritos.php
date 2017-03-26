<?php
class Formularios extends Controller {

	public static function index() {
		$bag = array(
			"inscritos" => self::getAllInscritos()
		);
		echo self::render("inscritos/index.html", $bag);
	}

	public static function editar() {
		$id = static::$app->parametros[2];
		
		$bag = array(
			"inscrito" => self::getInscritoById($id)
		);
		
		echo self::render("inscritos/editar.html", $bag);
	}

	public static function pagamento() {
		$id = static::$app->parametros[2];
		
		$bag = array(
			"inscrito" => self::getPagamentoByInscritoId($id)
		);
		
		echo self::render("inscritos/pagamento.html", $bag);
	}

	// AJAX
	public static function update() {
		global $dbConn;
		$post = (object)$_POST;
		
		// prepare data
		$post->estado = 'RS';
		$post->formacao_aluno_fisica = isset($post->formacao_aluno_fisica)? 1 : 0;
		$post->formacao_aluno_outro = isset($post->formacao_aluno_outro)? 1 : 0;
		$post->formacao_graduado_fisica = isset($post->formacao_graduado_fisica)? 1 : 0;
		$post->formacao_graduado_outro = isset($post->formacao_graduado_outro)? 1 : 0;
		$post->formacao_pos = isset($post->formacao_pos)? 1 : 0;
		$post->formacao_especializacao = isset($post->formacao_especializacao)? 1 : 0;
		$post->formacao_mestre = isset($post->formacao_mestre)? 1 : 0;
		$post->formacao_doutor = isset($post->formacao_doutor)? 1 : 0;
		$post->nivel_especializacao = isset($post->nivel_especializacao)? 1 : 0;
		$post->nivel_mestre = isset($post->nivel_mestre)? 1 : 0;
		$post->nivel_doutor = isset($post->nivel_doutor)? 1 : 0;
		$edicao = static::$eeefis->edicao;

		$query = "
			UPDATE inscrito
			SET 
				nome = ?,
				cpf = ?,
				email = ?,
				documento_estrangeiro = ?,
				endereco = ?,
				cep = ?,
				estado = ?,
				cidade = ?,
				formacao_aluno = ?,
				formacao_aluno_outro = ?,
				formacao_aluno_nome = ?,
				formacao_graduado = ?,
				formacao_graduado_outro = ?,
				formacao_graduado_nome = ?,
				formacao_pos = ?,
				formacao_pos_instituicao = ?,
				especializacao = ?,
				mestre = ?,
				doutor = ?,
				estabelecimento = ?,
				tipo_estabelecimento = ?,
				nivel_especializacao = ?,
				nivel_mestre = ?,
				nivel_doutor = ?,
				atua_sala = ?
			WHERE id = ? AND edicao = {$edicao}
		";

		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param(
		 	"ssssssssiisiisisiiisiiiiii",
		 	$post->nome,
		 	$post->cpf,
		 	$post->email,
			$post->documento_estrangeiros,
			$post->endereco,
			$post->cep,
			$post->estado,			
			$post->cidade,
			$post->formacao_aluno_fisica,
			$post->formacao_aluno_outro,
			$post->formacao_aluno_nome,
			$post->formacao_graduado_fisica,
			$post->formacao_graduado_outro,
			$post->formacao_graduado_nome,
			$post->formacao_pos,
			$post->formacao_pos_instituicao,
			$post->formacao_especializacao,
			$post->formacao_mestre,
			$post->formacao_doutor,
			$post->estabelecimento,
			$post->tipo_estabelecimento,
			$post->nivel_especializacao,
			$post->nivel_mestre,
			$post->nivel_doutor,
			$post->atua_sala,
			$post->id
		);

		$result = $mysqli->execute();
		
		// send data via json on AJAX request
		$json = new stdclass();
		$json->result = $result;

		self::output($json);
	}

	public static function update_pagamento() {
		global $dbConn;
		$post = (object)$_POST;
		
		$query = "
			UPDATE pagamento
			SET 
				status = ?,
				data = ?,
				pago = ?
			WHERE id = ?
		";

		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param(
		 	"isii",
		 	$post->status,
		 	$post->data,
		 	$post->pago,
			$post->id
		);

		$result = $mysqli->execute();
		
		// send data via json on AJAX request
		$json = new stdclass();
		$json->result = $result;

		self::output($json);
	}

	// Helpers
	private static function getAllInscritos() {
		global $dbConn;
		$post = (object)$_POST;
		$edicao = static::$eeefis->edicao;

		$query = "
			SELECT i.*, p.status grupo
			FROM inscrito i
			INNER JOIN pagamento p ON i.id = p.id_inscrito
			WHERE i.edicao = {$edicao}
			ORDER BY nome
		";
		$result = $dbConn->query($query);

		while($fetch = $result->fetch_object()) {
			$fetch->grupo = self::dictionaryGrupo($fetch->grupo);
			$inscritos[] = $fetch;
		}

		return toUTF($inscritos);
	}

	private static function getInscritoById($id) {
		global $dbConn;
		$edicao = static::$eeefis->edicao;

		$query = "
			SELECT *
			FROM inscrito 
			WHERE id = ? AND edicao = {$edicao}
		";
		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param("i", $id);
		$mysqli->execute();
		$result = $mysqli->get_result();
		$inscrito = $result->fetch_object();

		return toUTF($inscrito);
	}

	private static function getPagamentoByInscritoId($id) {
		global $dbConn;
		$edicao = static::$eeefis->edicao;

		$query = "
			SELECT i.nome, p.*
			FROM pagamento p 
			INNER JOIN inscrito i ON i.id = p.id_inscrito
			WHERE p.id_inscrito = ? AND i.edicao = {$edicao}
		";
		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param("i", $id);
		$mysqli->execute();
		$result = $mysqli->get_result();
		$inscrito = $result->fetch_object();

		return toUTF($inscrito);
	}

	private static function dictionaryGrupo($int) {
		switch ($int) {
			case 0: return "Comissão Organizadora";	
			case 1: return "Convidado";	
			case 2: return "Isento";
			case 3: return "Ministrante de Minicurso";
			case 4: return "Monitor";
			case 5: return "Participante";
			default: return "Outro";
		}
	}
}
Formularios::exec($app);