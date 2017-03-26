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

	// AJAX
	public static function update() {
		global $dbConn;
		$post = (object)$_POST;

		// prepare data
		$post->estado = 'RS';
		$post->formacao_aluno = isset($post->formacao_aluno)? 1 : 0;
		$post->formacao_graduado = isset($post->formacao_graduado)? 1 : 0;
		$post->formacao_pos = isset($post->formacao_pos)? 1 : 0;
		$post->formacao_especializacao = isset($post->formacao_especializacao)? 1 : 0;
		$post->formacao_mestre = isset($post->formacao_mestre)? 1 : 0;
		$post->formacao_doutor = isset($post->formacao_doutor)? 1 : 0;
		$post->nivel_especializacao = isset($post->nivel_especializacao)? 1 : 0;
		$post->nivel_mestre = isset($post->nivel_mestre)? 1 : 0;
		$post->nivel_doutor = isset($post->nivel_doutor)? 1 : 0;

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
				formacao_aluno_nome = ?,
				formacao_graduado = ?,
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
			WHERE id = ?
		";

		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param(
		 	"ssssssssisisisiiisiiiiii",
		 	$post->nome,
		 	$post->cpf,
		 	$post->email,
			$post->documento_estrangeiros,
			$post->endereco,
			$post->cep,
			$post->estado,			
			$post->cidade,
			$post->formacao_aluno,
			$post->formacao_aluno_nome,
			$post->formacao_graduado,
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

	// Helpers
	private static function getAllInscritos() {
		global $dbConn;
		$post = (object)$_POST;

		$query = "
			SELECT *
			FROM inscrito 
			ORDER BY nome
		";
		$result = $dbConn->query($query);

		while($fetch = $result->fetch_object()) {
			$inscritos[] = $fetch;
		}

		return toUTF($inscritos);
	}

	private static function getInscritoById($id) {
		global $dbConn;
		
		$query = "
			SELECT *
			FROM inscrito 
			WHERE id = ?
		";
		$mysqli = $dbConn->prepare($query);
		$mysqli->bind_param("i", $id);
		$mysqli->execute();
		$result = $mysqli->get_result();
		$inscrito = $result->fetch_object();

		return toUTF($inscrito);
	}
}
Formularios::exec($app);