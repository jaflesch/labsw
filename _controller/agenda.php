<?php
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
		$bag['calendar'] =  self::draw_calendar(10,2016);
		echo self::render("agenda/index.html", $bag);

		/* sample usages */
		//echo '<h2>July 2009</h2>';
		//echo self::draw_calendar(9,2016);

		//echo '<h2>August 2009</h2>';
	}

	private static function draw_calendar($month,$year) {

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
				if($list_day == 11 && $month == 9 && $year == 2016)
					$calendar.= '<div class="day-number event">'.$list_day.'</div>';
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

	public static function render($tpl, $vars=array()) {
		return parent::render($tpl,$vars);
	}
}

Agenda::exec($app);