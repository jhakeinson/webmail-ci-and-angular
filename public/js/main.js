$(document).ready(function ($) {
	Mailbox.retrieveMailboxes();
	Message.retrieveMessages(1, 'INBOX');
	Form.initForm();
});
