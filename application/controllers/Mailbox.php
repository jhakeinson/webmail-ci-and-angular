<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailbox extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->model('mail_model');

		if (isset($_SESSION['login'])) {
			$this->mail_model->login(
				$_SESSION['login']['email'],
				$_SESSION['login']['pwd']
			);
		}

		$this->load->library('custom_view');
	}

	public function index()
	{
		if (isset($_SESSION['login'])) {
			return $this->custom_view->render_view('Mailbox', 'mailbox');
		}

		return $this->custom_view->render_view('Mailbox', 'login');
	}

	public function get_all_mailboxes() {
		if(isset($_SESSION['login'])) {

			$data = array(
				'mailboxes' => $this->mail_model->get_mailboxes()
			);

			$this->load->view('mailbox_list', $data);
		}
	}

	public function get_all_messages_from_mailbox() {
		$mb_name = $this->input->get('mb_name', TRUE);

		if(isset($_SESSION['login'])) {
			$data = array(
				'messages' => $this->mail_model->get_messages_summary($mb_name)
			);

			$this->load->view('message_list', $data);
		}
	}

	public function process_uploaded_file() {
		$file = $_FILES['file'];
		$tmp_folder = './tmp/upload/';

		if(move_uploaded_file($file['tmp_name'], $tmp_folder.$file['name'])) {
			$status = 200;
			$response = array(
				'message' => 'File upload successful.',
				'code' => 'UPLOAD_SUCCESS',
				'status' => '200'
			);
		} else {
			$status = 422;
			$response = array(
				'message' => 'File upload failed.',
				'code' => 'UPLOAD_FAILED',
				'status' => '422'
			);
		}

		return $this->output
			->set_status_header($status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	public function delete_attachment() {
		$file = $this->input->get('file');

		if(unlink('./tmp/upload/'.$file)) {
			$status = 200;
			$response = array(
				'message' => 'File deletion successful',
				'code' => 'DELETE_SUCCESS',
				'status' => '200'
			);
		} else {
			$status = 404;
			$response = array(
				'message' => 'File deletion failed.',
				'code' => 'DELETE_FAILED',
				'status' => '404'
			);
		}

		return $this->output
			->set_status_header($status)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
	}

	public function get_messages($page) {
		if(isset($_SESSION['login'])) {
			$mb_name = $this->input->get('mb_name', TRUE);
			$items_per_page = 10;

			if ($page == 1) {
				$this->mail_model->load_messages($mb_name);
			}

			$data = $this->mail_model->get_messages_summary($page, $items_per_page);

			$this->load->view('message_list', $data);
		}
	}

	public function update_mailboxes() {
		$mailboxes = $this->input->post('unseenCnts');

		if(isset($_SESSION['login'])) {
			$data = array(
				'messages' => $this->mail_model->update_mailboxes($mailboxes)
			);

			$this->load->view('message_list', $data);
		}
	}

	public function get_message_content($msg_id) {
		if(isset($_SESSION['login'])) {
			$mb_name = $this->input->get('mb_name', TRUE);
			$msg = $this->mail_model->get_message($msg_id, $mb_name);

			$data = array(
				'message' => $msg
			);

			$this->load->view('message_content', $data);
		}
	}

	public function send_message() {
		if (isset($_SESSION['login'])) {
			$form_data = $this->input->post();
			$form_data['from'] = $_SESSION['login']['email'];

			if($this->mail_model->send_message($form_data)) {
				$status = 200;
				$response = array(
					'message' => 'Message sent successfully.',
					'code' => 'SEND_SUCCESS',
					'status' => '200'
				);
			} else {
				$status = 422;
				$response = array(
					'message' => 'Message not sent. Unable to process.',
					'code' => 'SEND_FAIlED',
					'status' => '422'
				);
			}

			return $this->output
				->set_status_header($status)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		}
	}

	public function do_action_to_messages($action) {
		if(isset($_SESSION['login'])) {
			$msg_ids = $this->input->get('msgIds');
			$mb_name = $this->input->get('mbName');
			$page = $this->input->get('page');

			switch ($action) {
				case 'delete':
					$successMsg = 'Message(s) deleted successfully.';
					$result = $this->mail_model->delete_messages($msg_ids, $mb_name);
					break;
				case 'mark-read':
					$successMsg = 'Message(s) marked as read successfully.';
					$result = $this->mail_model->mark_read_messages($msg_ids, $mb_name);
					break;
				case 'mark-spam':
					$successMsg = 'Message(s) marked as spam successfully.';
					$result = $this->mail_model->mark_spam_messages($msg_ids, $mb_name);
					break;
				default:
					$result = false;
			}

			if($result) {

				$this->mail_model->load_messages($mb_name);

				$data = $this->mail_model->get_messages_summary($page, 10);

				$html = $this->load->view('message_list', $data, true);

				$status = 200;
				$response = array(
					'message' => $successMsg,
					'html' => $html,
					'code' => 'ACTION_SUCCESS',
					'status' => '200'
				);
			} else {
				$status = 422;
				$response = array(
					'message' => 'Failed to execute action.',
					'code' => 'ACTION_FAILED',
					'status' => '422'
				);
			}

			return $this->output
				->set_status_header($status)
				->set_content_type('application/json', 'utf-8')
				->set_output(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
		}
	}
}
