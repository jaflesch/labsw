<?php
class Data {
	public static function date2str($data) {
		$piece = explode("-", $data);
		return $piece[2]."/".$piece[1]."/".$piece[0];
	}

	public static function str2date($string) {
		$piece = explode("/", $data);
		return $piece[2]."-".$piece[1]."-".$piece[0];
	}

	public static function datetime2str($datetime, $full = false) {
		$hourtime = explode(" ", $datetime);

		$piece = explode("-", $hourtime[0]);

		$string = ($full)? $hourtime[1]." " : "" .$piece[2]."/".$piece[1]."/".$piece[0];
		return $string;
	}
}
?>