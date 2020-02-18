<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Ddeboer\Imap\Server;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

class Test extends CI_Controller {

	public function index()
	{
		$uname = 'jake.i@3wcorner.com';
		$pword = 'Tvzqd.gwh!-x';
		$this->config->load('email_config');
		$test_imap = $this->config->item('imap');

		try {
			$test = new Server($test_imap['host'], $test_imap['port']);
			$connection = $test->authenticate($uname, $pword);
		} catch (Exception $e) {
			echo json_encode((array)$e);
		}

		$mailboxes = $connection->getMailboxes();

		foreach ($mailboxes as $mailbox) {
			// Skip container-only mailboxes
			// @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
			if ($mailbox->getAttributes() & \LATT_NOSELECT) {
				continue;
			}

			// $mailbox is instance of \Ddeboer\Imap\Mailbox
			print_r($mailbox->getMessage(2)->getFrom()->getAddress());
		}

		//$this->load->view('mailbox');
	}

	public function test_send2() {

		//Create a new SMTP instance
		$smtp = new SMTP;
//Enable connection-level debug output
		$smtp->do_debug = SMTP::DEBUG_CONNECTION;
		try {
			//Connect to an SMTP server
			if (!$smtp->connect('vip0.3wns.com', 465)) {
				throw new Exception('Connect failed');
			}
			//Say hello
			if (!$smtp->hello(gethostname())) {
				throw new Exception('EHLO failed: ' . $smtp->getError()['error']);
			}
			//Get the list of ESMTP services the server offers
			$e = $smtp->getServerExtList();
			//If server can do TLS encryption, use it
			if (is_array($e) && array_key_exists('STARTTLS', $e)) {
				$tlsok = $smtp->startTLS();
				if (!$tlsok) {
					throw new Exception('Failed to start encryption: ' . $smtp->getError()['error']);
				}
				//Repeat EHLO after STARTTLS
				if (!$smtp->hello(gethostname())) {
					throw new Exception('EHLO (2) failed: ' . $smtp->getError()['error']);
				}
				//Get new capabilities list, which will usually now include AUTH if it didn't before
				$e = $smtp->getServerExtList();
			}
			//If server supports authentication, do it (even if no encryption)
			if (is_array($e) && array_key_exists('AUTH', $e)) {
				if ($smtp->authenticate('username', 'password')) {
					echo "Connected ok!";
				} else {
					throw new Exception('Authentication failed: ' . $smtp->getError()['error']);
				}
			}
		} catch (Exception $e) {
			echo 'SMTP error: ' . $e->getMessage(), "\n";
		}
//Whatever happened, close the connection.
		$smtp->quit(true);

		$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
		try {
			//Server settings
			$mail->SMTPDebug = 2;                                 // Enable verbose debug output
			$mail->isSMTP();                                      // Set mailer to use SMTP
			$mail->Host = 'vip0.3wns.com';  // Specify main and backup SMTP servers
			$mail->SMTPAuth = true;                               // Enable SMTP authentication
			$mail->Username = 'jake.i@3wcorner.com';                 // SMTP username
			$mail->Password = 'Tvzqd.gwh!-x';
			$mail->SMTPSecure = 'ssl';
			$mail->Port = 465;                                    // TCP port to connect to

			//Recipients
			$mail->setFrom('jake.i@3wcorner.com', 'Mailer');
			$mail->addAddress('coniemarjhakeinson@gmail.com', 'Test');

			//Content
			$mail->isHTML(true);                                  // Set email format to HTML
			$mail->Subject = 'Here is the subject';
			$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
			$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

			$mail->send();
			echo 'Message has been sent';
		} catch (Exception $e) {
			echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
		}
	}

	public function test_send() {

		$this->load->model('mail_model');

		$this->mail_model->login($_SESSION['login']['email'], $_SESSION['login']['pwd']);

		$msg = array(
			'from' => 'jake.i@3wcorner.com',
			'to' => 'coniemarjhakeinson@gmail.com',
			'cc' => '',
			'subject' => 'Test',
			'body' => 'Test <h2>body</h2>'
		);

		if($this->mail_model->send_message($msg)) {
			echo 'send!';
		} else {
			echo 'failed';
		}
	}
}
