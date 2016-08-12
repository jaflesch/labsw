<?php
class Traducao {
	public static function get($key) {
		global $app;

		$static_array = array(
			"PT" => array(
				"contato_sucesso" => "Parabéns! Seu contato foi enviado com sucesso. Estaremos lhe retornado assim que possível.",
				"contato_error" => "Ocorreu um erro ao enviar seu formulário de contato. Por favor, tente novamente.",
				"interesse_sucesso" => "Parabéns! Sua manifestação de interesse neste curso foi enviada com sucesso. Estaremos retornando assim que possível.",
				"invalid_csrf" => "Token inválido."
			)
		);

		return ($static_array[$app->idioma][$key]);
	}
}
?>