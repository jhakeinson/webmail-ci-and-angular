<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PHPMailer_Library
{

	private $smtp_config;
	private $smtp_obj;
	private $mailer_obj;
	private $CI;

	public function __construct()
	{
		log_message('Debug', 'PHPMailer class is loaded.');

		$this->CI = get_instance();
		$this->CI->config->load('email_config');
		$this->smtp_config = $this->CI->config->item('smtp');
	}

	public function load() {
		$this->mailer_obj = new PHPMailer(true);
		$this->mailer_obj->SMTPDebug = $this->smtp_config['debug_level'];         // Enable verbose debug output
		$this->mailer_obj->isSMTP();                                      	    // Set mailer to use SMTP
		$this->mailer_obj->Host = $this->smtp_config['host'];  					                    // Specify main and backup SMTP servers
		$this->mailer_obj->SMTPAuth = $this->smtp_config['smtp_auth'];            // Enable SMTP authentication
		$this->mailer_obj->SMTPSecure = $this->smtp_config['smtp_secure'];        // Enable TLS encryption, `ssl` also accepted
		$this->mailer_obj->Port = $this->smtp_config['port'];
	}

	public function authenticate($username, $password) {
		$this->mailer_obj->Username = $username;
		$this->mailer_obj->Password = $password;

		return $this->mailer_obj;
	}
}
