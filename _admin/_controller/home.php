<?php
require '../_lib/data.php';
class Home extends Controller {

	public static function index() {
		$bag = array(
			"user" => Auth::getUser(),
			"data" => Data::today()
		);
		
		if(Auth::user())
			echo self::render("home/index.html", $bag);
		else
			self::redirect("login");
	}
	
	public static function logout() {
		Auth::logout();
		self::redirect("login");
	}
}
Home::exec($app);