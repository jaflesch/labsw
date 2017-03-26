<?php
class Formularios extends Controller {

	public static function index() {
		self::redirect("home");
	}

	public static function inscricao() {
		echo self::render("formularios/inscricao.html", array());
	}

	public static function send_inscricao() {
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
			INSERT INTO inscrito (
				nome,
				cpf,
				email,
				documento_estrangeiro,
				endereco,
				cep,
				estado,
				cidade,
				formacao_aluno,
				formacao_aluno_outro,
				formacao_aluno_nome,
				formacao_graduado,
				formacao_graduado_outro,
				formacao_graduado_nome,
				formacao_pos,
				formacao_pos_instituicao,
				especializacao,
				mestre,
				doutor,
				estabelecimento,
				tipo_estabelecimento,
				nivel_especializacao,
				nivel_mestre,
				nivel_doutor,
				atua_sala,
				edicao
			)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
			$edicao
		);

		$result = $mysqli->execute();
		if($result) {
			$id = $mysqli->insert_id;
			$data = date('Y-m-d');
			$query = "
				INSERT INTO pagamento (id_inscrito, status, data, pago)
				VALUES (
					{$id}, 
					0,
					'{$data}', 
					0
				)
			";
			$result = $dbConn->query($query);
		}

		// send data via json on AJAX request
		$json = new stdclass();
		$json->result = $result;

		self::output($json);
	}
}
Formularios::exec($app);