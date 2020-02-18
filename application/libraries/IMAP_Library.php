<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Description: Project model class
 */

class IMAP_Library
{
	private $imap_config;
	private $CI;

	public function __construct()
	{
		log_message('Debug', 'IMAP class is loaded.');

		$this->CI = get_instance();

		$this->CI->config->load('email_config');
		$this->imap_config = $this->CI->config->item('imap');
	}

	public function load()
	{
		$imap_obj = new Ddeboer\Imap\Server(
			$this->imap_config['host'],
			$this->imap_config['port']
		);

		return $imap_obj;
	}
}
