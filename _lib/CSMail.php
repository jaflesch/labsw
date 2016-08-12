<?php
require_once("PHPmailer/PHPmailer.php");

class CSMail {
	private $subject;
	private $mailer;
	
	public function CSMail($subject) {
		$this->setSubject($subject);
		$this->mailer = new PHPMailer();
		
		$this->initialConfig();
	}
	
	public static function validateAddress($email_address) {
		return PHPMailer::ValidateAddress($email_address);
	}
	
	/*
	 * Set initial configurations for PHP Mailer
	 */
	private function initialConfig() {
		$this->mailer->FromName = "Contato";
		$this->mailer->From = "contato@ebanc.com.br";
		$this->mailer->Sender = "contato@ebanc.com.br";

		//Subject
		$this->mailer->Subject = $this->getSubject();
	}
	
	/*
	 * Retrieve Subject
	 * @return String
	 */
	public function getSubject() {
		return $this->subject;
	}
	
	/*
	 * Set subject
	 * @param $subject String
	 * @return void
	 */
	public function setSubject($subject) {
		$this->subject = utf8_decode($subject);
	}
	
	/*
	 * Add BCC
	 * @param $email String
	 * @param $name String
	 * @return void
	 */
	public function addBCC($email, $name) {
		$this->mailer->AddBCC($email, $name);
	}
	
	/*
	 * Add CC
	 * @param $email String
	 * @param $name String
	 * @return void
	 */
	public function addCC($email, $name) {
		$this->mailer->AddCC($email, $name);
	}
	
	/*
	 * Add address
	 * @param $email String
	 * @param $name String
	 * @return void
	 */
	public function addAddress($email, $name) {
		$this->mailer->AddAddress($email, $name);
	}
	
	/*
	 * Set HTML e-mail body
	 * @param $body String
	 * @return void
	 */
	public function setBody($body) {
		$this->mailer->MsgHTML(utf8_decode($body));
	}
	
	public function addAttachment($first, $second) {
		$this->mailer->AddAttachment($first, $second);
	}
	
	/*
	 * Send the e-mail
	 * @return boolean
	 */
	public function send() {
		return $this->mailer->Send();
	}
}
?>