<?php
class Login extends Controller {

	public static function index() {
		if(Auth::user())
			self::redirect("home");
		else {
			echo self::render("login/index.html");
		}
	}
	
	public static function submit_form() {
		global $dbConn;

		$post = (object)static::$app->post;
		$post = preprocess($post);
		$post->password = hash('sha512', $post->password);
		$json = new stdclass();

		$query = "
			SELECT *
			FROM user 
			WHERE BINARY login = '{$post->login}' AND BINARY password = '{$post->password}'
		";
		$result = mysqli_query($dbConn, $query) or die(mysqli_error($dbConn));
		if($result && mysqli_num_rows($result) == 1) {
			$user = mysqli_fetch_object($result);
			Auth::logout();
			Auth::setUser($user);
			
			$json->success = true;			
		}
		else {
			$json->success = false;
			$json->msg = "Usuário ou senha inválidos!";
		}

		die(json_encode($json));
	}	 
}
Login::exec($app);