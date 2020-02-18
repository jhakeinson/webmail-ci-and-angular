<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
 * Description: Mail model class
 */

class Mail_model extends  CI_Model
{
	private $CI;
	private $imap;
	private $connection;
	private $mailer;

	function __construct()
	{
		parent::__construct();
		$this->CI = get_instance();


		$this->load->library('imap_library');
		$this->load->library('phpmailer_library');
		$this->imap = $this->imap_library->load();
		$this->phpmailer_library->load();

	}

	public function login($username, $password)
	{
		try {
			$this->connection = $this->imap->authenticate($username, $password);
			$this->mailer = $this->phpmailer_library->authenticate($username, $password);

			return true;
		} catch (Exception $e) {
			return false;
		}
	}

	public function get_mailboxes()
	{
		$mailboxes = $this->connection->getMailboxes();
		$result_mb = array();

		foreach ($mailboxes as $mailbox) {
			// Skip container-only mailboxes
			// @see https://secure.php.net/manual/en/function.imap-getmailboxes.php
			if ($mailbox->getAttributes() & \LATT_NOSELECT) {
				continue;
			}

			array_push($result_mb, array(
				'count' => $mailbox->count(),
				'name' => $mailbox->getName(),
				'status' => (array) $mailbox->getStatus()
			));
		}

		return $result_mb;
	}

	public function load_messages($mb_name)
	{
		try {
			$mb = $this->connection->getMailbox($mb_name);
			$messages = $mb->getMessages(null, \SORTDATE, true);

			$result_msgs = array();

			foreach ($messages as $message) {
				array_push($result_msgs, array(
					'uid' => $message->getNumber(),
					'subject' => $message->getSubject(),
					'body' => $message->getBodyText(),
					'date' => $message->getDate()->format('d/m/Y g:i a'),
					'is_seen' => $message->isSeen(),
					'atchCount' => count($message->getAttachments())
				));
			}

			$this->cache->file->save('stored_msgs', $result_msgs, 500);

			return true;
		} catch (Exception $e) {
			$this->cache->file->save(array(), $result_msgs, 500);
			return false;
		}
	}

	public function total_loaded_messages() {
		if($this->cache->get('stored_msgs')) {
			return count($this->cache->get('stored_msgs'));
		}

		return 0;
	}

	public function get_messages_summary($page, $items_per_page) {
		$msgs = $this->cache->file->get('stored_msgs');
		$offset = (($page - 1) * $items_per_page);

		$total_items = count($msgs);
		$first = (($page - 1) * $items_per_page) + 1;
		$last = $first + ($items_per_page - 1);
		$messages = array_slice($msgs, $offset, $items_per_page);

		$data = array(
			'messages' => $messages,
			'first' => (count($messages) <= 0)? count($messages):$first,
			'last' => ($total_items < $last)? $total_items:$last,
			'page' => $page,
			'total_items' => $total_items
		);

		return $data;
	}

	public function get_message($msg_id, $mb_name) {
		try {
			$mb = $this->connection->getMailbox($mb_name);
			$message = $mb->getMessage($msg_id);
			$to_array = array_map(array($this, 'get_email_address'), $message->getTo());
			$attachments = $this->get_attachnments_details($message);

			$message->markAsSeen();

			return array(
				'uid' => $message->getNumber(),
				'subject' => $message->getSubject(),
				'from' => $message->getFrom()->getAddress(),
				'to' => $to_array,
				'body' => $message->getBodyHtml(),
				'date' => $message->getDate()->format('d/m/Y g:i a'),
				'is_seen' => $message->isSeen(),
				'attachments' => $attachments
			);
		} catch (Exception $e) {
			return null;
		}
	}

	public function send_message($msg) {
		$mail = $this->mailer;
		$tmp_folder = './tmp/upload/';

		$mail->setFrom($msg['from']);

		//Recipients
		if($msg['to'] != '') {
			$to_array = explode(';', $msg['to']);

			foreach($to_array as $to) {
				$mail->addAddress(trim($to));
			}
		}

		if($msg['cc'] != '') {
			$cc_array = explode(';', $msg['cc']);

			foreach($cc_array as $cc) {
				$mail->addCC(trim($cc));
			}
		}

		//Attachments
		foreach(get_filenames($tmp_folder, FALSE) as $file) {
			$tmp_name = $tmp_folder.$file;
			$filename = $file;

			$mail->addAttachment($tmp_name, $filename);
		}

		//Content
		$mail->isHTML(true);
		$mail->Subject = $msg['subject'];
		$mail->Body    = ($msg['body'])? $msg['body']:' ';

		if($mail->send()) {
			$msgMIME = $mail->getSentMIMEMessage();

			$mailbox = $this->connection->getMailbox('INBOX.Sent');
			$mailbox->addMessage($msgMIME, '\\Seen');

			delete_files($tmp_folder);

			return true;
		}

		delete_files($path);

		return false;
	}

	public function  delete_messages($msg_ids, $mb_name) {
		$mb = $this->connection->getMailbox($mb_name);

		foreach($msg_ids as $id) {
			try {
				$mb->getMessage($id)->delete();
			} catch(Exception $e) {

			}
		}

		if($this->connection->expunge()) {
			return true;
		}

		return false;
	}

	public function  mark_read_messages($msg_ids, $mb_name) {
		$mb = $this->connection->getMailbox($mb_name);

		foreach($msg_ids as $id) {
			try {
				$mb->getMessage($id)->markAsSeen();
			} catch(Exception $e) {

			}
		}

		if($this->connection->expunge()) {
			return true;
		}

		return false;
	}

	public function  mark_spam_messages($msg_ids, $mb_name) {
		$mb = $this->connection->getMailbox($mb_name);
		$spamMb = $mailbox = $this->connection->getMailbox('INBOX.spam');;

		foreach($msg_ids as $id) {
			try {
				$mb->getMessage($id)->move($spamMb);
			} catch(Exception $e) {

			}
		}

		if($this->connection->expunge()) {
			return true;
		}

		return false;
	}

	private function get_attachnments_details($message) {
		$atch_details = array();
		$attachments = $message->getAttachments();

		$msg_num = $message->getNumber();
		$atch_dir = 'public/attachments';
		$new_dir = "$atch_dir/$msg_num";

		if(!is_dir("./$new_dir")) {
			mkdir("./$new_dir");
		}

		foreach($attachments as $attachment) {
			file_put_contents(
				"./$new_dir/".$attachment->getFilename(),
				$attachment->getDecodedContent()
			);

			array_push($atch_details, array(
				'filename' => $attachment->getFilename(),
				'url' => base_url("$new_dir/".$attachment->getFilename())
			));
		}

		return $atch_details;
	}

	private function get_email_address($e) {
		return $e->getAddress();
	}
}
