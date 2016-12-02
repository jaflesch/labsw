<?php
require '_lib/data.php';

class Agenda extends Controller {

	public static function index() {
		$bag = array();

		// if(Auth::user()) {
		// 	// debug
		// 	//print_r(Auth::getUser());
		// 	$bag["user"] = Auth::getUser();
		// 	//echo self::render("home/index.html", $bag);
		// }
		//else 
		$mes = date('m');
		$ano = date('Y');
		$events = self::getAllEvents();

		$bag['today'] = Data::today();
		$bag['calendar'] =  self::draw_calendar($mes, $ano, $events);
		$bag['eventos'] =  self::getEventosByData(date('d'), $mes, $ano);
		
		$mesNext = $mes;
		$anoNext = $ano;
		$mesPrev = $mes;
		$anoPrev = $ano;

		if($mes == 12){
			$mesNext = 1;
			$anoNext = $ano + 1;
		}
		else {
			$mesNext = $mes + 1;
			$anoNext = $ano;	
		}

		if($mes == 1){
			$mesPrev = 12;
			$anoPrev = $ano - 1;
		}
		else {
			$mesPrev = $mes - 1;
			$anoPrev = $ano;	
		}

		$bag['data_prev'] = $mesPrev.'-'.$anoPrev;
		$bag['data_next'] = $mesNext.'-'.$anoNext;
		
		echo self::render("agenda/index.html", $bag);

		/* sample usages */
		//echo '<h2>July 2009</h2>';
		//echo self::draw_calendar(9,2016);

		//echo '<h2>August 2009</h2>';
	}

	public static function get_calendar() {
		$post = static::$app->post;
		$piece = explode("-", $post['data']);
		$events = self::getAllEvents();
		
		$tmes = $piece[0];
		$tano = $piece[1];	

		$tmesNext = $tmes;
		$tanoNext = $tano;
		$tmesPrev = $tmes;
		$tanoPrev = $tano;

		if($tmes == 12){
			$tmesNext = 1;
			$tanoNext = $tano + 1;
		}
		else {
			$tmesNext = $tmes + 1;
			$tanoNext = $tano;	
		}

		if($tmes == 1){
			$tmesPrev = 12;
			$tanoPrev = $tano - 1;
		}
		else {
			$tmesPrev = $tmes - 1;
			$tanoPrev = $tano;	
		}

		$json = new stdclass();
		$json->prev = $tmesPrev.'-'.$tanoPrev;
		$json->next = $tmesNext.'-'.$tanoNext;
		$json->calendar = self::draw_calendar($tmes, $tano, $events);

		$eventos = self::getEventosByData(1, $tmes, $tano);
		$list = "";
		
		if(count($eventos) > 0) {
			foreach ($eventos as $key => $value) {

				$path = static::$app->base_path;
				$path = explode("htdocs", $path);
				$path = str_replace("\\", "/", $path[1]);

				$tipo = $eventos[$key]->tipo == "l" ? "lembretes/" : "tarefas/editar/".$eventos[$key]->id;
				$label = $eventos[$key]->tipo == "l" ? "Lembrete" : "Tarefa";
				$list .= "
					<li>
						<a href='{$path}/{$tipo}'>
							<i class='fa fa-bookmark'></i>{$label} - ". $eventos[$key]->titulo. "
						</a>
					</li>
				";
				
			}
		}
		else {
			$list .= "<li> Nenhum evento encontrado para esta data. </li>";
		}

		$json->list = $list;
		$json->data_extenso = Data::string('1'.'/'.$tmes.'/'.$tano);

		die(json_encode($json));
	}

	private static function draw_calendar($month,$year, $events) {
		$today = date('d/m/Y');
		$today = explode("/", $today);
		$days = array();
		foreach ($events as $key => $value) {
			$days[] = $key;
		}
		/* draw table */
		$calendar = '<table  class="calendar">';

		/* table headings */
		$headings = array('DOM','SEG','TER','QUA','QUI','SEX','SAB');
		$calendar.= '<tr class="calendar-row"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

		/* days and weeks vars now ... */
		$running_day = date('w',mktime(0,0,0,$month,1,$year));
		$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
		$days_in_this_week = 1;
		$day_counter = 0;
		$dates_array = array();

		/* row for week one */
		$calendar.= '<tr class="calendar-row">';
		$past_month_days = date('t', mktime(0,0,0,$month-1,1,$year));
		$past_month_days -= $running_day - 1;

		/* print "blank" days until the first of the current week */
		for($x = 0; $x < $running_day; $x++):
			$calendar.= '<td class="calendar-day-np">'.$past_month_days.'</td>';
			$days_in_this_week++;
			$past_month_days++;
		endfor;

		/* keep going with days.... */		
		for($list_day = 1; $list_day <= $days_in_month; $list_day++):
			$calendar.= '<td class="calendar-day">';
				/* add in the day number */
				
				if(in_array($list_day, $days)) {
					if($events[$list_day]->ano == $year && $events[$list_day]->mes == $month)
						$calendar.= '<div class="day-number task">'.$list_day.'</div>';
					else
						$calendar.= '<div class="day-number">'.$list_day.'</div>';
				}
				else if($list_day == $today[0] && $month == $today[1] && $year == $today[2]) {
					$calendar.= '<div class="day-number event">'.$list_day.'</div>';
				}
				else
					$calendar.= '<div class="day-number">'.$list_day.'</div>';
				
				/** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
				//$calendar.= str_repeat('<p></p>',2);
				
			$calendar.= '</td>';
			if($running_day == 6):
				$calendar.= '</tr>';
				if(($day_counter+1) != $days_in_month):
					$calendar.= '<tr class="calendar-row">';
				endif;
				$running_day = -1;
				$days_in_this_week = 0;
			endif;
			$days_in_this_week++; $running_day++; $day_counter++;
		endfor;

		/* finish the rest of the days in the week */
		if($days_in_this_week < 8):
			for($x = 1; $x <= (8 - $days_in_this_week); $x++):
				$calendar.= '<td class="calendar-day-np">'.$x.' </td>';
			endfor;
		endif;

		/* final row */
		$calendar.= '</tr>';

		/* end the table */
		$calendar.= '</table>';
		
		/* all done, return result */
		return $calendar;
	}

	private static function getAllEvents() {
		global $dbConn;
		$datas[] = array();
		$id = Auth::id();

		$query = "
			SELECT id, titulo, data_entrega data, 't' tipo
			FROM `tarefa` 
			WHERE (id_autor = {$id} OR id_usuario = {$id}) AND status < 4

			UNION

			SELECT id, titulo, data, 'l' tipo
			FROM lembrete
			WHERE id_usuario = {$id} AND status = 0
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$piece = explode(" ", $fetch->data);
			$piece = explode("-", $piece[0]);
			$fetch->dia = (int)$piece[2];
			$fetch->mes = $piece[1];
			$fetch->ano = $piece[0];
			$datas[$fetch->dia] = $fetch;
		}

		return toUTF($datas);
	}

	private static function getEventosByData($dia, $mes, $ano) {
		global $dbConn;
		$datas = array();
		$id = Auth::id();

		$query = "
			SELECT id, titulo, data_entrega data, 't' tipo
			FROM tarefa
			WHERE (id_autor = {$id} OR id_usuario = {$id}) AND status < 4 AND DAY(data_entrega) = {$dia} AND MONTH(data_entrega) = {$mes} AND YEAR(data_entrega) = {$ano}

			UNION

			SELECT id, titulo, data, 'l' tipo
			FROM lembrete
			WHERE id_usuario = {$id} AND status = 0 AND DAY(data) = {$dia} AND MONTH(data) = {$mes} AND YEAR(data) = {$ano}
		";
		$result = mysqli_query($dbConn, $query) or die(mysqli_error($dbConn));
		while ($fetch = mysqli_fetch_object($result)) {
			$piece = explode(" ", $fetch->data);
			$piece = explode("-", $piece[0]);
			$fetch->dia = (int)$piece[2];
			$fetch->mes = $piece[1];
			$fetch->ano = $piece[0];

			if($fetch->titulo != "")
				$datas[$fetch->dia] = $fetch;
		}

		return toUTF($datas);
	}
		
}

Agenda::exec($app);