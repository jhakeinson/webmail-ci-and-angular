<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	function __construct()
	{
		parent::__construct();

		$this->load->model('mail_model');
	}

	function login() {
		$creds = $this->input->post();

		if ($this->mail_model->login($creds['email'], $creds['pwd'])) {
			$_SESSION['login'] = array(
				'email' => $creds['email'],
				'pwd' => $creds['pwd']
			);
		}

		redirect('/');
	}

	function logout() {
		if(isset($_SESSION['login'])) {
			//clear session from globals
			$_SESSION = array();
			//clear session from disk
			session_destroy();

			redirect('/');
		}
	}
}
