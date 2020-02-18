<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Custom_view {

	public $master_layout = '';
	private $CI = null;

	function __construct($master_layout = 'layout/master')
	{
		$this->CI =& get_instance();
		$this->master_layout = $master_layout;
	}

	public function render_view($title, $template, $data = '')
	{
		$this->CI->load->view($this->master_layout, array(
			'title' => $title,
			'template' => $this->CI->load->view($template, $data, TRUE)
		));
	}
}
