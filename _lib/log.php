<?php
class Log {
	
	const CRIAR = 1;
	const EDITAR = 2;
	const DELETAR = 3;

	public static function insert($id_tarefa) {
		$id_user = Auth::id();
		$acao = self::CRIAR;
		
		$query = "
			INSERT INTO log (id_autor, id_tarefa, acao, data)
			VALUES ({$id_user}, {$id_tarefa}, {$acao}, NOW() )
		";
		$result = mysqli_query(static::$dbConn, $query);

		return $result;
	}

	public static function update($id_tarefa) {
		$id_user = Auth::id();
		$acao = self::EDITAR;
		
		$query = "
			INSERT INTO log (id_autor, id_tarefa, acao, data)
			VALUES ({$id_user}, {$id_tarefa}, {$acao}, NOW() )
		";
		$result = mysqli_query(static::$dbConn, $query);

		return $result;
	}

	public static function delete($id_tarefa) {
		$id_user = Auth::id();
		$acao = self::DELETAR;
		
		$query = "
			INSERT INTO log (id_autor, id_tarefa, acao, data)
			VALUES ({$id_user}, {$id_tarefa}, {$acao}, NOW() )
		";
		$result = mysqli_query(static::$dbConn, $query);

		return $result;
	}

	public static function report($id_autor) {
		$log = array();

		$query = "
			SELECT l.*, u.usuario_nome, t.titulo tarefa_titulo
			FROM log l
			INNER JOIN usuario u ON u.id = l.id_autor
			INNER JOIN tarefa t ON t.id = l.id_tarefa
			WHERE l.id_autor = {$id_autor}
		";
		$result = mysqli_query(static::$dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$log[] = self::translate($fetch);
		}

		return $log;
	}
	private static function translate($log) {
		$string = "O usuário ". $log->usuario_nome;

		switch($log->acao)  {
			case self::CRIAR: $string .= " criou"; break;
			case self::EDITAR: $string .= " editou"; break;
			case self::REMOVER: $string .= " deletou"; break;
		}

		$datetime = explode(" ", $log->data);
		$time =  explode(":", $datetime[1]);
		$string .= " a tarefa ".$log->tarefa_titulo." às ".$time[0]."h".$time[1]."min na data".Data::date2str($datetime[0]);

		return toUTF($string);
	}
}
?>