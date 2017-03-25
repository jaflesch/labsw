
<?php
class Home extends Controller {

	public static function index() {
		$bag = array();		
		echo self::render("home/index.html", $bag);
	}
}
Home::exec($app);