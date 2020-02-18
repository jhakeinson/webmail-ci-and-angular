<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['smtp'] = array(
	'debug_level' => 0,
	'host' => 'vip0.3wns.com',
	'port' => 465,
	'smtp_auth' => true,
	'smtp_secure' => 'ssl'
);

$config['imap'] = array(
	'host' => 'vip0.3wns.com',
	'port' => 993
);
