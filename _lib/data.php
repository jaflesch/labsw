<?php
class Data {
	public static function date2str($data) {
		$piece = explode("-", $data);
		return $piece[2]."/".$piece[1]."/".$piece[0];
	}

	public static function str2date($string) {
		$piece = explode("/", $string);
		return $piece[2]."-".$piece[1]."-".$piece[0];
	}

	public static function datetime2str($datetime, $full = false) {
		$hourtime = explode(" ", $datetime);

		$piece = explode("-", $hourtime[0]);

		$string = ($full)? $hourtime[1]." " : "" .$piece[2]."/".$piece[1]."/".$piece[0];
		return $string;
	}

	public static function today() {
		$data = date('d/m/Y');
		$pieces = explode("/", $data);

		switch ($pieces[1]) {
			case 1: $mes = 'janeiro'; break;
			case 2: $mes = 'fevereiro'; break;
			case 3: $mes = 'março'; break;
			case 4: $mes = 'abril'; break;
			case 5: $mes = 'maio'; break;
			case 6: $mes = 'junho'; break;
			case 7: $mes = 'julho'; break;
			case 8: $mes = 'agosto'; break;
			case 9: $mes = 'setembro'; break;
			case 10: $mes = 'outubro'; break;
			case 11: $mes = 'novembro'; break;
			case 12: $mes = 'dezembro'; break;
		}

		return $pieces[0].' de '.$mes.' de '.$pieces[2];
	}
}
?>