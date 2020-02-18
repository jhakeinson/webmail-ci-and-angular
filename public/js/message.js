
var Message = (function () {
	let container = $('#messageMain');
	let currPage = 1;
	let currMbName = 'INBOX';

	var retrieveMessages = function (page, mbName) {
		let url = [host, 'messages/page/'+page+'?mb_name=' + mbName].join('/');
		container.html(
			$('<div>')
				.css('padding-bottom', '10px')
				.addClass('text-center')
				.html($('<i class="fa fa-spinner fa-spin fa-3x">'))
		);
		container.load(url, function () {
			currPage = page;
			currMbName = mbName;
			bindEvents();
		});
	};

	var bindEvents = function() {
		container.find('#messagesTable tr').off().on('click', retrieveMsgContent);
		container.find('#deleteMessages').off().on('click', deleteMessagesHandler);
		container.find('#markAsRead').off().on('click', markAsReadHandler);
		container.find('#markAsSpam').off().on('click', markAsSpamHandler);
		container.find('#messagesTable tr input[type="checkbox"]').off().on('click', (e) => {e.stopPropagation()});
		container.on('click', 'button.btn-reply', replyBtnClickHandler);
		container.on('click', 'button.btn-forward', forwardBtnClickHandler);
		container.on('click', 'button.btn-delete', deleteBtnClickHandler);
		container.find('.btn-reload').off().on('click', reloadBtnClickHandler);
		container.find('.inbox-pagination .page-next').off().on('click', changePageHandler);
		container.find('.inbox-pagination .page-previous').off().on('click', changePageHandler);
	};

	var retrieveMsgContent = function (e) {
		e.preventDefault();
		let activeMb = Mailbox.getActiveMailbox();
		let msg = $(e.currentTarget);
		let mbName = activeMb.data('mailboxEncName');
		let msgId = msg.data('msgId');
		let param = $.param({'mb_name': mbName});
		let url = [host, 'message', msgId + '?' + param].join('/');
		container.empty();
		container.html(
			$('<div>')
				.css('padding-bottom', '10px')
				.addClass('text-center')
				.html($('<i class="fa fa-spinner fa-spin fa-3x">'))
		);
		container.load(url, (e) => {bindEvents()});
	};

	var markAsReadHandler = function(e) {
		e.preventDefault();

		let mb = Mailbox.getActiveMailbox();
		let mbName = mb.data('mailboxEncName');
		let msgIds = getCheckedMsgsId();

		let params = $.param({'msgIds[]': msgIds, 'page': currPage, 'mbName': mbName}, true);
		let url = host + '/message/mark-read?' + params;

		Notification.notifText('Marking messages as read)...');
		Notification.show('info');
		$.ajax({
			url: url,
			method: 'GET',
			success: function(data) {
				container.empty();
				container.html(data.html);

				Notification.notifText(data.message);
				Notification.show('info');
				bindEvents();
			},
			error: function (data) {
				Notification.notifText('Failed to mark messages as read.');
				Notification.show('error');
				bindEvents();
			}
		});
	};

	var markAsSpamHandler = function(e) {
		e.preventDefault();

		let mb = Mailbox.getActiveMailbox();
		let mbName = mb.data('mailboxEncName');
		let msgIds = getCheckedMsgsId();

		let params = $.param({'msgIds[]': msgIds, 'page': currPage, 'mbName': mbName}, true);
		let url = host + '/message/mark-spam?' + params;

		Notification.notifText('Marking messages as spam...');
		Notification.show('info');

		removeCheckedMsgs();
		$.ajax({
			url: url,
			method: 'GET',
			success: function(data) {
				container.empty();
				container.html(data.html);

				Notification.notifText(data.message);
				Notification.show('info');
				bindEvents();
			},
			error: function (data) {
				Notification.notifText('Failed to mark messages as spam.');
				Notification.show('error');
				bindEvents();
			}
		});
	};

	var deleteMessagesHandler = function(e) {
		e.preventDefault();

		let msgIds = getCheckedMsgsId();

		deleteMessages(msgIds, currPage , currMbName);
	};

	var deleteMessages = function(msgIds, page, mbName) {
		let params = $.param({'msgIds[]': msgIds, 'page': page, 'mbName': mbName}, true);
		let url = host + '/message/delete?' + params;

		Notification.notifText('Deleting message(s)...');
		Notification.show('info');

		removeCheckedMsgs();
		$.ajax({
			url: url,
			method: 'GET',
			success: function(data) {
				container.empty();
				container.html(data.html);

				Notification.notifText(data.message);
				Notification.show('info');
				bindEvents();
			},
			error: function () {
				Notification.hide(0);
				Notification.notifText('Failed to delete messages.');
				Notification.show('error');
				bindEvents();
			}
		});
	};

	var getCheckedMsgsId = function() {
		let msgIds = [];
		let checkboxes = container.find('#messagesTable tr input[type="checkbox"]:checked');

		checkboxes.each(function(i, e) {
			let tr = $(e).closest('tr');
			let msgId = tr.data('msgId');

			msgIds.push(msgId);
		});

		return msgIds;
	};

	var removeCheckedMsgs = function() {
		let checkboxes = container.find('#messagesTable tr input[type="checkbox"]:checked');

		checkboxes.each(function(i, e) {
			let tr = $(e).closest('tr').remove();
		});
	};

	var changePageHandler = function(e) {
		e.preventDefault();
		e.stopPropagation();

		let activeMb = Mailbox.getActiveMailbox();
		let page = $(e.currentTarget).data('page');
		let mbName = activeMb.data('mailboxEncName');

		console.log(e.currentTarget, page);

		Message.retrieveMessages(page, mbName);
	};

	var reloadBtnClickHandler = function(e) {
		e.preventDefault();

		let mb = Mailbox.getActiveMailbox();
		let mbName = mb.data('mailboxEncName');

		retrieveMessages(1, mbName);
	};

	var replyBtnClickHandler = function(e) {
		e.preventDefault();
		e.stopPropagation();

		Form.resetForm();

		let form = Form.getForm();

		let to = container.find('.from').text();
		let subject = 'Re: ' + container.find('.subject').text();

		form.find('input#to').val(to);
		form.find('input#subject').val(subject);

		$('#myModal').modal('show');
	};

	var forwardBtnClickHandler = function(e) {
		e.preventDefault();
		e.stopPropagation();

		Form.resetForm();

		let form = Form.getForm();

		let subject = 'Fwd: ' + container.find('.subject').text();

		form.find('input#subject').val(subject);

		$('#myModal').modal('show');
	};

	var deleteBtnClickHandler = function (e) {
		e.preventDefault();

		let msgId = container.find('.message').data('msgId');
		let mb = Mailbox.getActiveMailbox();
		let mbName = mb.data('mailboxEncName');

		deleteMessages([msgId], mbName);
	};

	return {
		retrieveMessages: retrieveMessages
	}
})();
