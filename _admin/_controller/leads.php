<?php
require '../_lib/data.php';
class Leads extends Controller {

	public static function index() {
		$bag = array(
			"user" => Auth::getUser(),
			"leads" => self::getAllLeads()
		);
		
		if(Auth::user())
			echo self::render("leads/index.html", $bag);
		else
			self::redirect("login");
	}

	public static function visualizar() {
		$id = (int)static::$app->parametros[2];

		$bag = array(
			"user" => Auth::getUser(),
			"lead" => self::getLeadById($id)
		);
		
		if(Auth::user())
			echo self::render("leads/visualizar.html", $bag);
		else
			self::redirect("login");
	}

	public static function exportar() {
		global $dbConn;
		$leads = array();

		$query = "
			SELECT *
			FROM lead
			ORDER BY id DESC
		";
		$result = mysqli_query($dbConn, $query);
		while ($fetch = mysqli_fetch_object($result)) {
			$fetch->date = Data::date2str($fetch->date);
			$fetch->message = nl2br($fetch->message);

			$leads[] = $fetch;
		}

		header('Content-Type: application/vnd.ms-excel; charset=utf-8');
		header('Content-Disposition: attachment; filename="Leads.xls"'); 
		header('Content-Transfer-Encoding: binary');
		
		echo "<!DOCTYPE html>";
		echo "<html>";
			echo "<head>";
				echo "<meta http-equiv='Content-type' content='text/html;charset=utf-8' />";
				echo "<meta charset='utf-8' />";
			echo "</head>";
			echo "<body>";
				echo "<table>";
					echo "<tr>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd'><strong>Nome</strong></td>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd; text-align: left; padding-right:10px'><strong>E-mail</strong></td>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd; text-align: left; padding-right:10px'><strong>Telefone</strong></td>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd; text-align: left; padding-right:10px'><strong>Cidade</strong></td>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd; text-align: left; padding-right:10px'><strong>Data</strong></td>";
						echo "<td style='border-bottom:1px solid #cccccc; background: #dddddd'><strong>Mensagem</strong></td>";
					echo "</tr>";

					$i = 0;
					foreach ($leads as $fetch) {
						$background = ($i % 2 == 0)? "#ffffff" : "#fafafa";						
						
						if (strlen($fetch->name) >= 1) {
							echo "<tr style='border: 1px solid #eeeeee; border-top: 0px'>";
								echo "<td align='left' style='background: {$background}; vertical-align: middle'>{$fetch->name}</td>";
								echo "<td align='left' style='background: {$background}; vertical-align: middle'>{$fetch->email}</td>";
								echo "<td align='left' style='background: {$background}; vertical-align: middle'>{$fetch->phone}</td>";
								echo "<td align='left' style='background: {$background}; vertical-align: middle'>{$fetch->city}</td>";
								echo "<td align='left' style='background: {$background}; vertical-align: middle'>{$fetch->date}</td>";
								echo "<td align='justify' style='background: {$background}'>{$fetch->message}</td>";
							echo "</tr>";
						}
						$i++;
					}
				echo "</table>";
			echo "</body>";
		echo "</html>";
	}

	public static function delete() {
		$id = (int)static::$app->post['id'];
		
		global $dbConn;
		$leads = array();

		$query = "
			DELETE FROM lead
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);

		$json = new stdclass();
		$json->success = $result;
		$json->msg = ($json->success) ? "Lead excluído com sucesso!" : "Erro ao excluir lead";

		die(json_encode($json));
	}

	private static function getAllLeads() {
		global $dbConn;
		$leads = array();

		$query = "
			SELECT id, name, email, date
			FROM lead
			ORDER BY date DESC
		";
		$result = mysqli_query($dbConn, $query);
		while($fetch = mysqli_fetch_object($result)) {
			$fetch->date = Data::date2str($fetch->date);
			$leads[] = $fetch;
		}

		return toUTF($leads);
	}

	private static function getLeadById($id) {
		global $dbConn;
		
		$query = "
			SELECT *
			FROM lead
			WHERE id = {$id}
		";
		$result = mysqli_query($dbConn, $query);
		if($result && mysqli_num_rows($result) == 1) {
			$fetch = mysqli_fetch_object($result);
			$fetch->date = Data::date2str($fetch->date);
		}
		
		return toUTF($fetch);
	}
}
Leads::exec($app);